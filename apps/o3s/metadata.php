<?php
/**
 *  Copyright (C) 2007-2011 Atos
 *
 *  Author: Raphael Semeteys <raphael.semeteys@atos.net>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 *  O3S
 *  metadata.php: extract metadata from QSOS files
 *
**/


  include("config.php");
  include("lang.php");
  include("libs/QSOSDocument_2.0.php");

  $IdDB = mysql_connect($db_host ,$db_user, $db_pwd);
  mysql_select_db($db_db);

  //$file: filename (or URI) of the QSOS document to load
  //Returns: array with metadata
  function getmetadata($file) {
    $metadata = array();
    $keys = array( "qsosMetadata/template/type", "qsosspecificformat", "openSourceCartouche/component/name", "qsosappname", "openSourceCartouche/component/version", "qsosMetadata/language", "openSourceCartouche/license/name" ,"openSourceCartouche/dates/creation", "openSourceCartouche/dates/update", "openSourceCartouche/dates/validation" );
    $myDoc = new QSOSDocument($file);

    foreach ($keys as $key) {
      $metadata[$key] = $myDoc->getkey($key);
    }

    $metadata["authors"] = $myDoc->getauthors();

    $metadata["sections"] = $myDoc->getcountkey("section");

    $metadata["criteria"] = array(
      "total" => $myDoc->getcountkey("element"),
      "scorable" => $myDoc->getcountkey("element/score"),
      "scored" => $myDoc->getcountkey("element[score >= 0]"),
      "notscored" => $myDoc->getcountkey("element[score = '']")
    );

    $metadata["comments"] = array(
      "total" => $myDoc->getcountkey("element/comment"),
      "commented" => $myDoc->getcountkey("element[comment != '']"),
      "notcommented" => $myDoc->getcountkey("element[comment = '']")
    );

    return $metadata;
  }

  $array = array();
  function retrieveTree($path, $parent)  {
    global $delim;
    global $array;

    if ($dir=@opendir($path)) {
    while (($element=readdir($dir))!== false) {
      if (is_dir($path.$delim.$element)
      && $element != "."
      && $element != ".."
      && $element != "CVS"
      && $element != "template"
      && $element != "templates"
      && $element != ".svn") {
        retrieveTree($path.$delim.$element, $parent.$delim.$element);
      } elseif (substr($element, -5) == ".qsos") {
        array_push($array, $parent.$delim.$element);
      }
    }
    closedir($dir);
    }
    return (isset($array) ? $array : false);
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
<?php
  echo "    <link REL=StyleSheet HREF='skins/$skin/o3s.css' TYPE='text/css'/>\n";
?>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
    <script>
      function changeLang(lang) {
        window.location = 'metadata.php?lang=' + lang;
      }
    </script>
  </head>
  <body>
    <div id="bandeau">
      <div id="language">
<?php
  foreach($supported_lang as $l) {
    $checked = $l;
    if (strcmp($l, $lang) == 0) {
      echo "        <input type='radio' onclick=\"changeLang('$l')\" checked=\"true\"/> $l\n";
    } else {
      echo "        <input type='radio' onclick=\"changeLang('$l')\"/> $l\n";
    }
  }
?>
      </div>
      <center>
<?php
  echo "        <a href=\"index.php?lang=" . $lang . "\">Start page</a> |\n";
  echo "        <a href=\"upload.php?lang=" . $lang . "\">Upload an evaluation</a> |\n";
  echo "        <a href=\"search.php?lang=" . $lang . "\">Search for an evaluation</a>\n";
?>
      </center>
    </div>
    <center>
<?php
  echo "<img src='skins/$skin/o3s.png'/>\n";
  echo "<br/><br/>\n";

  echo "<div style='font-weight: bold'>Metadata repository update<br/><br/></div>\n";

  echo "Output: <div style='font-size: small; text-align: left; width: 50%; background-color: lightgrey'>";
  $query = "TRUNCATE TABLE evaluations";
  if ($IdReq = mysql_query($query, $IdDB)) {
    echo "Metadata deleted.<br/>";
  } else {
    echo "Error while deleting metadata...<br/>";
  }

  $evaluations = retrieveTree($sheet, $sheet);

  foreach ($evaluations as $evaluation) {
    $id = end(explode($delim, $evaluation));
    $m = getmetadata($evaluation);

    $query = "INSERT INTO evaluations VALUES (\"$id\",
    \"".$m['qsosappfamily']."\",
    \"".$m['qsosspecificformat']."\",
    \"".$m['qsosappname']."\",
    \"".$m['release']."\",
    \"".$m['appname']."\",
    \"".$m['language']."\",
    \"$evaluation\",
    \"".$m['licensedesc']."\",
    \"".$m['creation']."\",
    \"".$m['validation']."\",
    ".$m['sections'].",
    ".$m['criteria']['total'].",
    ".$m['criteria']['scorable'].",
    ".$m['criteria']['scored'].",
    ".$m['criteria']['notscored'].",
    ".$m['comments']['total'].",
    ".$m['comments']['commented'].",
    ".$m['comments']['notcommented'].")";

    if ($IdReq = mysql_query($query, $IdDB)) {
      echo $m['appname']." proceeded.<br/>";
    } else {
      echo "Error while inserting in database...<br/>";
    }
  }

  echo "</div>";
  echo "<br/><input type='button' value='Back to o3s' onclick=\"window.location='index.php'\">";
  echo " <input type='button' value='Upload evaluations' onclick=\"window.location='upload.php'\">";

  echo "</center>\n";
  echo "</body>\n";
  echo "</html>\n";
?>