<?php 
include("func.php");
$lang = getlang();
head($lang);

if ($lang=="en") {
?>
	<h1>How to contribute ?</h1>
	<ul>
		<li><a href="docs/howto/fr/QSOS-HOWTO_grille_fonctionnelle.php">HOWTO Comment créer une nouvelle grille fonctionnelle ?</a> (french)</li>
	</ul>
<?php
} else if ($lang=="fr") {
?>
	<h1>Comment contribuer</h1>
	<ul>
		<li><a href="docs/howto/fr/QSOS-HOWTO_grille_fonctionnelle.php">HOWTO Comment créer une nouvelle grille fonctionnelle ?</a></li>
	</ul>
<?php
}
foot();
?>