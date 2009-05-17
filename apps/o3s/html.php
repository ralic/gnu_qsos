<?php
$id = $_GET['id'];
if (!isset($id)) die("No QSOS file to process");

include("config.php");
$IdDB = mysql_connect($db_host ,$db_user, $db_pwd);
mysql_select_db($db_db);

$query = "SELECT file FROM evaluations WHERE id = \"$id\"";
$IdReq = mysql_query($query, $IdDB);

if ($file = mysql_fetch_row($IdReq)) {
  # LOAD XML FILE
  $XML = new DOMDocument();
  $XML->load($file[0]);

  # START XSLT
  $xslt = new XSLTProcessor();

  # IMPORT STYLESHEET
  $XSL = new DOMDocument();
  $XSL->load('xslt/qsos-xhtml.xsl');
  $xslt->importStylesheet($XSL);

  #PRINT
  print $xslt->transformToXML($XML);
} else {
  print "Error: no $id found in QSOS database!";
}
?> 
