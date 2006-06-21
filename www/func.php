<?php

function getlang () {
	$lang = $_GET['lang'];
	if (($lang != "en") && ($lang != "fr") && ($lang != "es")) {
	# unless lang param correctly initialised i try to get read HTTP_ACCEPT_LANGUAGE
		$tmp = explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
		$http_accept_lang = strtolower(substr(chop($tmp[0]),0,2));
		if ($http_accept_lang == "en-us") {
		if ($http_accept_lang == "en-us") $lang = "en";
		} else if ($http_accept_lang == "fr") {
		if ($http_accept_lang == "fr") $lang = "fr";
		} else {
		$lang = "en";
		}
	}

	return $lang;
}
	
function head ($lang="en"){

$charset="UTF-8";
header("Content-type: text/html; charset=\"$charset\"");
print('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
</html>
  <head>
    <title>QSOS.org</title>
    <meta http-equiv="Content-type" content="text/html"; charset="'.$charset.'"/>
    <link rel="stylesheet" type="text/css" href="/style/original/style.css" media="screen" />
  </header>
  <body>
<div id="menu">
<ul>');

if ($lang=="en") {
print "<li><a href=\"index.php?lang=en\">Overview</a></li>
<li><a href=\"methode.php?lang=en\">QSOS method</a></li>
<li><a href=\"download.php?lang=en\">Download</a></li>
<li><a href=\"license.php?lang=en\">License</a></li>
<li><a href=\"sheets/\">QSOS Sheets</a></li>
<li><a href=\"/download/fiches/\">Old sheets (fr)</a></li>
<li><a href=\"/blog//\"><strong>News!</strong></a></li>
<!--li><a href=\"contribute.php?lang=en\"><strong>Contribute</strong></a></li-->
<li><a href=\"https://savannah.nongnu.org/projects/qsos/\"><strong>Project</strong></a></li>";
} else if ($lang=="fr"){
print "<li><a href=\"index.php?lang=fr\">Présentation</a></li>
<li><a href=\"methode.php?lang=fr\">Méthode QSOS</a></li>
<li><a href=\"download.php?lang=fr\">Téléchargements</a></li>
<li><a href=\"license.php?lang=fr\">Licence</a></li>
<li><a href=\"sheets/\">Fiches QSOS</a></li>
<li><a href=\"/download/fiches/\">Anciennes fiches</a></li>
<li><a href=\"/blog//\"><strong>News!</strong></a></li>
<li><a href=\"contribute.php?lang=fr\"><strong>Contribuer</strong></a></li>
<li><a href=\"https://savannah.nongnu.org/projects/qsos/\"><strong>Project</strong></a></li>";
}

if ($lang != "en") print "<div id=\"flags\"><a href=\"?lang=en\"><img src=\"images/flags/uk.png\" /></a></div>"; 
if ($lang != "fr") print "<div id=\"flags\"><a href=\"?lang=fr\"><img src=\"images/flags/fr.png\" /></a></div>"; 

print('</ul>


<div id="thanks">');
	
if ($lang=="fr"){
print 'QSOS est mise à disposition sous licence libre par <a href="http://www.atosorigin.com">Atos Origin</a>.';
} else {
print 'QSOS is published under the GNU FDL by <a href="http://www.atosorigin.com">Atos Origin</a>.';

}

print('
</div>
<div id="valide">');

if ($lang=="fr") {
	print('Valide XHTML et CSS');
} else {
	print('XHTML and CSS compliant');
}

print('
</div>


</div>

<div id="corp">');
}

function foot (){
print('</div>
</body>
</html>');
}
?>
