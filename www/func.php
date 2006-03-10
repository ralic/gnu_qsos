<?php

function head ($charset="UTF-8"){
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
<ul>
<li><a href="index.php">Présentation</a></li>
<li><a href="methode.php">Méthode QSOS</a></li>
<li><a href="download.php">Téléchargements</a></li>
<li><a href="forum/index.php" target="forum">Forum</a></li>
<li><a href="license.php">Licence</a></li>
<li><a href="fiches.php">Fiches</a></li>
</ul>


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
