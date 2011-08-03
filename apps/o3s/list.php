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
 *  list.php: lists software in a given family
 *
**/


  session_start();

  include("config.php");
  include("lang.php");

  if(!isset($_REQUEST['family']) || !isset($_REQUEST['qsosspecificformat'])) {
    die("No QSOS family/format to process (you can't acces this page directly, go back to O3S)");
  } else {
    $family = $_REQUEST['family'];
    $qsosspecificformat = $_REQUEST['qsosspecificformat'];
  }

  $backURL = "index.php?lang=$lang";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
<?php
echo "    <link rel=StyleSheet href='skins/$skin/o3s.css' type='text/css'/>\n";
?>
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

  function checkboxes() {
    var ok = false;
    var inputs = document.getElementsByTagName("input");
    for(var i=0; i < inputs.length; i++) {
      if (inputs[i].type == "checkbox" && inputs[i].name == "id[]" && inputs[i].checked) {
        ok = true;
      }
    }
    return ok;
  }

  function submitForm() {
    if (checkboxes() == true) {
      myForm.action = "show.php";
      myForm.submit();
    } else {
      alert("<? echo $msg['s3_err_js_no_file']; ?>");
    }
  }

  function showFreeMind() {
      myForm.action = "freemind.php";
      myForm.submit();
  }

  function exportODS() {
    if (checkboxes() == true) {
      myForm.action = "export_oo.php";
      myForm.submit();
    } else {
      alert("<? echo $msg['s3_err_js_no_file']; ?>");
    }
  }

  function showGraph() {
    if (checkboxes() == true) {
      myForm.action = "radar.php";
      myForm.submit();
    } else {
      alert("<? echo $msg['s3_err_js_no_file']; ?>");
    }
  }

  function showQuadrant() {
    if (checkboxes() == true) {
      myForm.action = "quadrant.php";
      myForm.submit();
    } else {
      alert("<? echo $msg['s3_err_js_no_file']; ?>");
    }
  }

  function setWeights() {
    myForm.action = "set_weighting.php";
    myForm.submit();
  }
</script>
  </head>
  <body>
    <center>
<?php
  echo "      <img src='skins/$skin/o3s.png'/>\n";
  echo "      <br/>\n";
  echo "      <br/>\n";

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

  echo "<div style='font-weight: bold'>".$msg['s3_family'].$family."<br/><br/>"
    .$msg['s3_title']."<br/><br/>\n";
  echo "<input type='button' value='".$msg['s3_button_back']."'
    onclick='location.href=\"$backURL\"'/><br/><br/>\n";

  echo "<form id='myForm' action='show.php'>\n";
  echo "<input type='hidden' name='lang' value='$lang'/>\n";
  echo "<input type='hidden' name='family' value='$family'/>\n";
  echo "<input type='hidden' name='qsosspecificformat' value='$qsosspecificformat'/>\n";
  echo "<table>\n";
  echo "<tr class='title'>
    <td rowspan='2' align='center'>".$msg['s3_software']."</td>
    <td rowspan='2' align='center'>".$msg['s3_table_completed']."</td>
    <td rowspan='2' align='center'>".$msg['s3_table_commented']."</td>
    <td colspan='2' align='center'>".$msg['s3_table_view']."</td>
    <td rowspan='2' align='center'>".$msg['s3_table_compare']."</td>
  </tr>\n";
  echo "<tr class='title'>
    <td align='center'> ".$msg['s3_format_xml']." </td>
    <td align='center'> ".$msg['s3_format_html']." </td>
  </tr>\n";

  $query = "SELECT DISTINCT appname FROM evaluations WHERE qsosappfamily = \"$family\" AND qsosspecificformat = '$qsosspecificformat' ORDER BY appname";
  $IdReq = mysql_query($query, $IdDB);

  while($appname = mysql_fetch_row($IdReq)) {
    echo "<tr class='level0'><td colspan='7'>$appname[0]</td></tr>\n";
    $query2 = "SELECT id, e.release, qsosspecificformat, licensedesc,  criteria_scored/criteria_scorable, criteria_commented/comments, file FROM evaluations e WHERE appname = \"$appname[0]\" ORDER BY e.release";
    $IdReq2 = mysql_query($query2, $IdDB);
    while($software = mysql_fetch_row($IdReq2)) {
      echo "<tr class='level1'
              onmouseover=\"this.setAttribute('class','highlight')\"
              onmouseout=\"this.setAttribute('class','level1')\">\n";
      echo "<td align='center'>$software[1]</td>\n";
      echo "<td align='center'>".ceil($software[4]*100)."% </td>\n";
      echo "<td align='center'>".ceil($software[5]*100)."% </td>\n";
      echo "<td align='center'>
              <a href='$software[6]'><img src='skins/$skin/xml.png' border='0' title='".$msg['s3_format_xml_tooltip']."'/></a>
              </td>\n";
      echo "<td align='center'>
              <a href='html.php?id=$software[0]'><img src='skins/$skin/html.png' border='0' title='".$msg['s3_format_html_tooltip']."'/></a>
              </td>\n";
      echo "<td align='center' class='html'>
              <!--span class='logo_html'/-->
              <input type='checkbox' class='logo_html' name='id[]' value='$software[0]'>
              </td></tr>\n";
    }
  }
  echo "</table><br/>";
  echo "<input type='button' value='".$msg['s3_set_weights']."' onclick='setWeights()'>";
  echo "&nbsp;";
  echo "<input type='button' value='".$msg['s3_show_mindmap']."' onclick='showFreeMind()'>";
  echo "<br/><br/>";
  echo "<input type='button' value='".$msg['s3_format_odf']."' onclick='exportODS()'>";
  echo "&nbsp;";
  echo "<input type='button' value='".$msg['s3_button_next']."' onclick='submitForm()'>";
  echo "<br/><br/>";
  echo $msg['s3_check_svg'].
    " <input id='check' type='checkbox' name='svg' value='yes' onclick='toggleSVG()' svg='on' checked><br/><br/>";
  echo "<input type='button' value='".$msg['s3_graph']."' onclick='showGraph()'><br/><br/>";
  echo "<input type='button' value='".$msg['s3_quadrant']."' onclick='showQuadrant()'>";
?>
        </form>
      </div>
    </center>
  </body>
</html>
