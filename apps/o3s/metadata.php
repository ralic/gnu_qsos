<?php
/*
**  Copyright (C) 2009 Atos Origin 
**
**  Author: Raphael Semeteys <raphael.semeteys@atosorigin.com>
**
**  This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
**
**
** O3S
** metadata.php: extract metadata from QSOS files
**
*/

include("config.php");
include("locales/$lang.php");

echo "<html>\n";
echo "<head>\n";
echo "<LINK REL=StyleSheet HREF='skins/$skin/o3s.css' TYPE='text/css'/>\n";
echo "</head>\n";

include("libs/QSOSDocument.php");

//$file: filename (or URI) of the QSOS document to load
//Returns: array with metadata
function getmetadata($file) {
  $metadata = array();
  $keys = array( "qsosappfamily", "qsosspecificformat", "appname", "qsosappname", "release", "language", "licensedesc" ,"creation", "validation" );
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

$evaluations = retrieveTree($sheet, $sheet);

$IdDB = mysql_connect($db_host ,$db_user, $db_pwd);
mysql_select_db($db_db);

$query = "TRUNCATE TABLE evaluations";
if ($IdReq = mysql_query($query, $IdDB)) {
  echo "Meta données supprimées<br/>";
} else {
  echo "Erreur lors de la suppression des meta données...<br/>";
}
  
foreach ($evaluations as $evaluation) {
  $id = end(explode($delim, $evaluation));

  $m = getmetadata($evaluation);
//   echo "<pre>";
//   print_r($m);
//   echo "</pre>";
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
    echo $m['appname']." enregistré en base<br/>";
  } else {
    echo "Erreur d'écriture dans la base...<br/>";
  }
}
?>