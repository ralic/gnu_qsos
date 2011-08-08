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
 *  set_weighting.php: displays form to enter weigthings
 *
**/


  session_start();

  $params = array('lang', 'svg', 'family', 'qsosspecificformat', 'new');

  foreach($params as $param) {
    if(!isset($_REQUEST[$param])) {
      echo "" . $param . " is not set!<br/>";
    } else {
      $$param = $_REQUEST[$param];
    }
  }

  $weights = array();

  if (isset($new) && ($new == "true")) {
    $_SESSION = array();
    while (list($name, $value) = each($_REQUEST)) {
      if (!(in_array($name,$params))) {
        $_SESSION[$name] = $value;
      }
    }
  }

  while (list($name, $value) = each($_SESSION)) {
    if (!(in_array($name,$params))) {
      $weights[$name] = $_SESSION[$name];
    }
  }


  $_SESSION["nbWeights"] = count($weights);

  include("config.php");
  include("lang.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
<?php
  echo "    <link rel=StyleSheet href='skins/$skin/o3s.css' type='text/css'/>\n";
?>
    <script src="commons.js" language="JavaScript" type="text/javascript"></script>
    <script language="JavaScript" type="text/javascript">
      function checkWeight(field) {
        if (isNaN(field.value)) {
          var oldValue = field.value;
          field.value = "1";
          alert(oldValue + "<? echo $msg['s2_err_weight']; ?>");
          field.focus();
        }
      }

      function saveFile() {
        myForm.action = "save_weighting.php";
        myForm.submit();
      }

      function save() {
        myForm.action = "set_weighting.php";
        myForm.submit();
      }

      function back() {
        myForm.action = "list.php";
        myForm.submit();
      }

      function upload() {
        var file = document.getElementById("weighting");
        if (file.value == "") {
          alert("<? echo $msg['s2_err_no_file']; ?>");
          return;
        }
        myForm.action = "set_weighting.php";
        myForm.submit();
      }

      function changeLang(lang) {
        window.location = 'set_weighting.php?lang=' + lang;
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
  echo "      <img src='skins/$skin/o3s.png'/>\n";
?>
      <br/>
      <br/>
<?php
  include("libs/QSOSDocument.php");

  //Check if family and template version exist
  $IdDB = mysql_connect($db_host ,$db_user, $db_pwd);
  mysql_select_db($db_db);
  $query = "SELECT DISTINCT CONCAT(qsosappfamily,qsosspecificformat) FROM evaluations WHERE appname <> '' AND language = '$lang'";
  $IdReq = mysql_query($query, $IdDB);
  $familiesFQDN = array();
  while($row = mysql_fetch_row($IdReq)) {
    array_push($familiesFQDN, $row[0]);
  }
  if (!in_array($family.$qsosspecificformat,$familiesFQDN))
    die ("$family $qsosspecificformat".$msg['s3_err_no_family']);

  $query = "SELECT file FROM evaluations WHERE qsosappfamily = '$family' AND qsosspecificformat = '$qsosspecificformat' LIMIT 0,1";
  $IdReq = mysql_query($query, $IdDB);
  $result = mysql_fetch_row($IdReq);
  $file = $result[0];

  //Upload of weighting file
  if (isset($_FILES['weighting']) && $_FILES['weighting']['tmp_name'] <> "") {
    $weighting = $_FILES['weighting'];
    $dir = $temp.uniqid();
    move_uploaded_file($weighting['tmp_name'], $dir);
    chmod ($dir, 0770);

    $doc = new DOMDocument();
    $doc->load($dir);
    $xpath =  new DOMXPath($doc);

    $nodes = $xpath->query("//family");
    $upload_family = $nodes->item(0)->nodeValue;

    if ($upload_family == $family) {
      $nodes = $xpath->query("//weight");
      foreach ($nodes as $node) {
        $name = $node->getAttribute('id');
        $value = $node->nodeValue;
        $weights[$name] = $value;
      }
      $text = "<div style='texte-align: center; color: red'>"
        .$weighting['name']
        .$msg['s2_loaded']
        ."</div><br/>";
    } else {
      $text = "<div style='texte-align: center; color: red'>"
        .$family.$msg['s2_error1']
        .$upload_family.$msg['s2_error2']
        ."</div><br/>";
    }
  }

  $myDoc = new QSOSDocument($file);
  $tree = $myDoc->getTree();
  $familyname = $myDoc->getkey("qsosappfamily");

  echo "<div style='font-weight: bold'>".$msg['s2_title']."<br/><br/></div>\n";
  echo $text;
  echo "<form id='myForm' enctype='multipart/form-data' method='POST' action='set_weighting.php'>\n";
  echo "<input type='hidden' name='lang' value='$lang'/>\n";
  echo "<input type='hidden' name='svg' value='$svg'/>\n";
  echo "<input type='hidden' name='family' value='$family'/>\n";
  echo "<input type='hidden' name='new' value='true'/>\n";
  echo "<input type='hidden' name='qsosspecificformat' value='$qsosspecificformat'/>\n";
  echo "<table id='table' style='border-collapse: collapse; font-size: 12pt; table-layout: fixed'>\n";
  echo "<tr class='title' style='width: 400px'><td>$familyname</td>\n";
  echo "<td><div style='width: 60px; text-align: center'>"
    .$msg['s2_weight']
    ."</div></td>\n";
  echo "</tr>\n";

  showtree($myDoc, $tree, 0, '');

  echo "<input type='button' value='".$msg['s2_button_back']."'
    onclick='back()'> ";
  echo " <input type='button' value='".$msg['s2_button_save']."'
    onclick='save()'> ";
  echo " <input type='button' value='".$msg['s2_button_saveFile']."'
    onclick='saveFile()'><br/><br/>\n";
  echo "<input type='file' id='weighting' name='weighting'/> ";
  echo "<input type='button' value='".$msg['s2_button_upload']."'
    onclick='upload()'><br/><br/>\n";
  echo "</table>\n";
  echo "</form>\n";

  //Recursive function to display tree of criteria
  function showtree($myDoc, $tree, $depth, $idP) {
    global $upload;
    global $weights;
    $new_depth = $depth + 1;
    $offset = $new_depth*10;
    $idF = 0;

    for($k=0; $k<count($tree); $k++) {
      $name = $tree[$k]->name;
      $title = $tree[$k]->title;
      $subtree = $tree[$k]->children;

      $idF++;
      if ($idP == '') {
        $id = $idF;
      } else  {
        $id = $idP."-".$idF;
      }

      echo "<tr id='$id'
        class='level$depth'
        onmouseover=\"this.setAttribute('class','highlight')\"
        onmouseout=\"this.setAttribute('class','level$depth')\">\n";
      if ($subtree) {
        echo "<td style='width: 400px; text-indent: $offset'>
          <span onclick=\"collapse(this);\" class='expanded'>$title</span></td>\n";
      } else {
        echo "<td style='width: 400px; text-indent: $offset'>
          <span>$title</span></td>\n";
      }

      echo "<td><div style='width: 60px; text-align: center'>\n";
      //If a weighting file has been uploaded use $weights array, if not use default value 1
      echo "<input type='text'
        name='$name' size='3' style='text-align: center' onblur='checkWeight(this)'
        value='".((isset($weights[$name]))?$weights[$name]:1)."'/>\n";

      echo "</div></td>\n";

      echo "</tr>\n";

      if ($subtree) {
        showtree($myDoc, $subtree, $new_depth, $id);
      }
    }
  }
?>
    </center>
  </body>
</html>
