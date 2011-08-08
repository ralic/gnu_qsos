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
 *  Based on this script: http://programmabilities.com/php/?id=2
 *
**/


  session_start();
  session_unset();
  session_destroy();
  $_SESSION = array();

  if(!isset($_REQUEST['s'])) {
//     die("You can't acces this page directly, go back to O3S");
    $searchstr = "";
  } else {
    $searchstr = $_REQUEST['s'];
  }

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
      function changeLang(lang) {
        window.location = 'search.php?lang=' + lang;
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
  echo "<p><form action='$_SERVER[PHP_SELF]' method='post'>\n";
  echo "	<input type='text' name='s' value='$searchstr' size='20' maxlength='30'/>\n";
  echo "	<input type='submit' value='".$msg['s1_button_search']."'/><br/><br/>\n";
  echo "	<input type='button' value='".$msg['s1_button_back']."' onclick=\"location.href='index.php'\"/>\n";
  echo "</form></p>\n";

  echo "</center>\n";

  if (! empty($searchstr)) {
      // empty() is used to check if we've any search string.
      // If we do, call grep and display the results.
      echo '<hr/>';
      // Call grep with case-insensitive search mode on all files
      $cmdstr = "grep -i -l $searchstr $sheet/*/*/*.qsos";
      $fp = popen($cmdstr, 'r'); // open the output of command as a pipe
      $myresult = array(); // to hold my search results
      while ($buffer = fgetss($fp, 4096)) {
            // grep returns in the format
            // filename: line
            // So, we use split() to split the data
            list($fname, $fline) = split(':', $buffer, 2);
            // we take only the first hit per file
            if (! defined($myresult[$fname])) {
                $myresult[$fname] = $fline;
            }
        }
        // we have results in a hash. lets walk through it and print it
        if (count($myresult)) {
            echo '<ul><br/>';
            while (list($fname, $fline) = each ($myresult)) {
                $name = basename($fname, ".qsos");
                echo "<li><a href='show.php?lang=$lang&svg=yes&s=$searchstr&id[]=$name'>$name</a></li>\n";
            }
            echo '</ul><br/>';
        } else {
              // no hits
              echo $msg['s1_search_msg1']."<strong>$searchstr</strong>".$msg['s1_search_msg2']."<br/>\n";
        }
        pclose($fp);
    }
?>
  </body>
</html>
