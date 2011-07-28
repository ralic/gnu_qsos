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
 *  freemind.php: show the template of a given family with FreeMind Flash Viewer
 *
**/


session_start();

include("config.php");
include("lang.php");

$family = $_REQUEST['family'];
$qsosspecificformat = $_REQUEST['qsosspecificformat'];
if (!isset($family)) die("No QSOS family to process");

include("config.php");
$IdDB = mysql_connect($db_host ,$db_user, $db_pwd);
mysql_select_db($db_db);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mind Map Flash Viewer</title>
<style type="text/css">
  /* hide from ie on mac \*/
  html {
  height: 100%;
  overflow: hidden;
  }
  #flashcontent {
  height: 100%;
  }
  /* end hide */
  body {
  height: 100%;
  margin: 0;
  padding: 0;
  background-color: #ffffff;
  }
</style>
</head>
<body>
<?php
$query = "SELECT DISTINCT CONCAT(qsosappfamily,qsosspecificformat) FROM evaluations WHERE appname <> '' AND language = '$lang'";
$IdReq = mysql_query($query, $IdDB);
$familiesFQDN = array();
while($row = mysql_fetch_row($IdReq)) {
  array_push($familiesFQDN, $row[0]);
}
if (!in_array($family.$qsosspecificformat,$familiesFQDN))
  die ("$family $qsosspecificformat".$msg['s3_err_no_family']);

$query = "SELECT file FROM evaluations WHERE qsosappfamily = \"$family\" AND qsosspecificformat = '$qsosspecificformat' LIMIT 0:,1";
$IdReq = mysql_query($query, $IdDB);

if ($file = mysql_fetch_row($IdReq)) {
  # LOAD XML FILE
  $XML = new DOMDocument();
  $XML->load($file[0]);

  # START XSLT
  $xslt = new XSLTProcessor();

  # IMPORT STYLESHEET
  $XSL = new DOMDocument();
  $XSL->load('xslt/template-freemind.xsl');
  $xslt->importStylesheet($XSL);

  #SAVE RESULT
  $name = $family."-".$qsosspecificformat.".mm";
  $filename = "mindmaps/".$name;
  $file = fopen($filename, "w");
  fwrite($file, $xslt->transformToXML($XML));
  fclose($file);

  #DISPLAY RESULT WITH FLASHVIEWER
  print '<script type="text/javascript" src="mindmaps/flashobject.js"></script>
<p style="text-align:center; font-weight:bold"><a href="'.$filename.'">'.$name.'</a></p>
<div id="flashcontent"> Flash plugin or Javascript are turned off. Activate both  and reload to view the mindmap</div>
<script type="text/javascript">
// <![CDATA[
var fo = new FlashObject("mindmaps/visorFreemind.swf", "visorFreeMind", "100%", "100%", 6, "");
fo.addParam("quality", "high");
fo.addParam("bgcolor", "#ffffff");
fo.addVariable("initLoadFile", "'.$filename.'");
fo.write("flashcontent");
// ]]>
</script>';

} else {
  print "Error: no $family ($qsosspecificformat) found in QSOS database!";
}
?>
</body>
</html>