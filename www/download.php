<?php include("func.php");?>
<?
$lang = getlang();
head($lang);
?>

<h2>QSOS version 1.5</h2>
<div class="downloads">
English
<ul lang="en">
<li class="pdf"><a href="download/qsos-1.5-en.pdf">PDF</a></li>
<li class="dvi"><a href="download/qsos-1.5-en.dvi">DVI</a></li>
</ul>
French
<ul lang="fr">
<li class="pdf"><a href="download/qsos-1.5-fr.pdf">PDF</a></li>
<li class="dvi"><a href="download/qsos-1.5-fr.dvi">DVI</a></li>
</ul>
source
<ul>
<li class="tgz"><a href="download/qsos-1.5.tar.gz">Latex</a></li>
</ul>

</div>


<h2>Gnu FDL</h2>
<?php
	# Grrik, i18n string shouldn't be here.
	if ($lang == "fr") print "Document en anglais";
?>
<div class="downloads">
<ul>
<li class="pdf"><a href="download/fdl.pdf">PDF</a></li>
<li class="dvi"><a href="download/fdl.dvi">DVI</a></li>
</li>
</div>

<?php foot();?>
