<?php

function getlang () {
	$lang = $_GET['lang'];
	if (($lang != "en") && ($lang != "fr") && ($lang != "es")) {
		exit;
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
    <link rel="stylesheet" type="text/css" href="style/original/style.css" media="screen" />
  </header>
  <body>
<div id="menu">
<ul>');

if ($lang=="en") {
print "<li><a href=\"index.php?lang=en\">Overview</a></li>
<li><a href=\"methode.php?lang=en\">QSOS method</a></li>
<li><a href=\"download.php?lang=en\">Download</a></li>
<li><a href=\"forum/index.php?lang=en\" target=\"forum\">Forum</a></li>
<li><a href=\"license.php?lang=en\">License</a></li>
<li><a href=\"fiches.php?lang=en\">Sheet</a></li>";
} else if ($lang=="fr"){
print "<li><a href=\"index.php?lang=fr\">Présentation</a></li>
<li><a href=\"methode.php?lang=fr\">Méthode QSOS</a></li>
<li><a href=\"download.php?lang=fr\">Téléchargements</a></li>
<li><a href=\"forum/index.php?lang=fr\" target=\"forum\">Forum</a></li>
<li><a href=\"license.php?lang=fr\">Licence</a></li>
<li><a href=\"fiches.php?lang=fr\">Fiches</a></li>";
}

print('</ul>


<div id="thanks">
QSOS est mise à disposition sous licence libre par <a href="http://www.atosorigin.com">Atos Origin</a>.
</div>
<div id="valide">
XHTML et CSS valide
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