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
 *  software.php: lists software in a given family
 *
**/


  session_start();
  include("config.php");
  include("lang.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
<?php
  echo "    <link rel=StyleSheet href='skins/$skin/o3s.css' type='text/css'/>\n";
?>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
    <script>
      function toggleSVG() {
        var svg = document.getElementById("check").getAttribute("svg");
        var links = document.getElementsByTagName("a");
        for(var i=0; i < links.length; i++) {
          var ref = links[i].getAttribute("href");
          if (svg == "on") {
            if (ref.search(/&svg=yes/) != -1) ref = ref.split("&svg=")[0];
            document.getElementById("check").setAttribute("svg", "off");
          } else {
            if (ref.search(/&svg=yes/) == -1) ref += "&svg=yes";
            document.getElementById("check").setAttribute("svg", "on");
          }

          links[i].setAttribute("href", ref);
        }

      }

      function submitForm() {
        var ok = false;
        var inputs = document.getElementsByTagName("input");
        for(var i=0; i < inputs.length; i++) {
          if (inputs[i].type == "checkbox" && inputs[i].name == "f[]" && inputs[i].checked) {
            ok = true;
          }
        }

        if (ok == true) {
          myForm.submit();
        } else {
          alert("At least one product must be checked");
        }
      }

      function changeLang(lang) {
        window.location = 'software.php?lang=' + lang;
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

  if(!isset($_REQUEST['family'])) {
    $family = "";
  } else {
    $family = $_REQUEST['family';
  }

  if (!isset($_SESSION["generic"])) {
    while (list($name, $value) = each($_REQUEST)) {
      if (($name != 'f') && ($name != 'svg')) {
        $_SESSION[$name] = $value;
      }
    }
  }

  $tree= retrieveTree($sheet.$delim.$family);
  $keys = array_keys($tree);

  function retrieveTree($path)  {
    global $delim;

    if ($dir=@opendir($path)) {
    while (($element=readdir($dir))!== false) {
      if (is_dir($path.$delim.$element)
      && $element != "."
      && $element != ".."
      && $element != "CVS"
      && $element != "template"
      && $element != "templates"
      && $element != ".svn") {
        $array[$element] = retrieveTree($path.$delim.$element);
      } elseif (substr($element, -5) == ".qsos") {
        $array[] = $element;
      }
    }
    closedir($dir);
    }
    return (isset($array) ? $array : false);
  }

  echo "<div style='font-weight: bold'>".
    $msg['s3_title'].
    "<br/><br/>\n";
  echo "<input type='button'
    value='".$msg['s3_button_back']."'
    onclick='location.href=\"set_weighting.php?lang=$lang&family=$family\"'/><br/><br/>\n";
  echo "<form id='myForm' action='show.php'>\n";
  echo "<input type='hidden' name='family' value='$family'/>\n";
  echo "<table>\n";
  echo "<tr class='title'>
      <td>$family</td>
      <td align='center'>".$msg['s3_format_xml']."</td>
      <td align='center'>".$msg['s3_format_ods']."</td>
      <td><input type='button' value='".$msg['s3_button_next']."' onclick='submitForm()'></td>
    </tr>\n";
  for ($i=0; $i<count($keys); $i++) {
    if (!is_int($keys[$i])) {
      echo "<tr class='level0'><td colspan='4'>$keys[$i]</td></tr>\n";
      for ($j=0; $j<count($tree[$keys[$i]]); $j++) {
        $file = $tree[$keys[$i]][$j];
        $link = $sheet.$delim.$family.$delim.$keys[$i].$delim.$file;
        $name = basename($file, ".qsos");
        $odsfile = $name.".ods";

        echo "<tr class='level1'
          onmouseover=\"this.setAttribute('class','highlight')\"
          onmouseout=\"this.setAttribute('class','level1')\">\n";
        echo "<td>$name</td>\n";
        echo "<td align='center'>
          <a href='$link'><img src='skins/$skin/xml.png' border='0'/></a>
          </td>\n";
        echo "<td align='center'>
          <a href='export_oo.php?f=$link'><img src='skins/$skin/ods.png' border='0'/></a>
          </td>\n";
        echo "<td align='center' class='html'>
          <span class='logo_html'/>
          <input type='checkbox' class='logo_html' name='f[]' value='$link'>
          </td></tr>\n";
      }
    }
  }
?>
          </table>
          <br/>
<?php
  echo $msg['s3_check_svg'];
?>
          <input id='check' type='checkbox' name='svg' value='yes' onclick='toggleSVG()' svg='on' checked>
        </form>
      </div>
    </center>
  </body>
</html>
