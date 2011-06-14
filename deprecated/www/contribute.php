<?php 
include("func.php");
$lang = getlang();
head($lang);

if ($lang=="en") {
?>
	<h1>How to contribute ?</h1>
	<ul>
		<li><a href="docs/howto/en/QSOS-HOWTO_functional_grid.php">How to design a new functional grid?</a></li>
	</ul>
        <ul>
                <li><a href="docs/howto/en/QSOS-HOWTO_sheet_evaluation.php">How to write an evaluation sheet ?</a></li>
        </ul>

<?php
} else if ($lang=="fr") {
?>
	<h1>Comment contribuer</h1>
	<ul>
		<li><a href="docs/howto/fr/QSOS-HOWTO_grille_fonctionnelle.php?lang=fr">Comment créer une nouvelle grille fonctionnelle ?</a></li>
		<li><a href="docs/howto/fr/QSOS-HOWTO_fiche_evaluation.php?lang=fr">Comment créer une nouvelle évaluation QSOS ?</a></li>
	</ul>
<?php
}
foot();
?>
