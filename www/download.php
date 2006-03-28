<?php include("func.php");?>
<?
$lang = getlang();
head($lang);
?>

<h2>QSOS version 1.5</h2>
<div class="downloads">
English
<ul lang="en">
<li class="pdf"><a href="download/en/qsos-1.5.pdf">PDF</a></li>
<li class="dvi"><a href="download/en/qsos-1.5.dvi">DVI</a></li>
</ul>
French
<ul lang="fr">
<li class="pdf"><a href="download/fr/qsos-1.5.pdf">PDF</a></li>
<li class="dvi"><a href="download/fr/qsos-1.5.dvi">DVI</a></li>
</ul>
source
<ul>
<li class="tgz"><a href="download/fr/qsos-1.5.tar.gz">Latex</a></li>
</ul>

</div>


<h2>Gnu FDL</h2>
<?php
	# Grrik, i18n string shouldn't be here.
	if ($lang == "fr") print "Document en anglais";
?>
<div class="downloads">
<ul>
<li class="pdf"><a href="download/en/fdl.pdf">PDF</a></li>
<li class="dvi"><a href="download/en/fdl.dvi">DVI</a></li>
</li>
</div>

<?php foot();?>
