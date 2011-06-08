<?php
include("../../../func.php");

$lang = getlang();
head($lang);
?>
    <h1>QSOS HOWTO - How to write a new QSOS evaluation ?</h1>

    Author : <a href="mailto:raphael AT semeteys DOT org">Raphaël Semeteys</a>

    <h3>Abstract</h3>

    <p>Thi small guide describs the steps, good practise and the associate tools to write an QSOS.</p>

    <h2>Downloading the proper functionnal grid</h2>

    <p>Two options :</p>

    <ul>
    	<li>The functionnal grid already exist : QSOS's site contains all stable functionnal grid.(<a href="http://www.qsos.org/sheets/templates/">http://www.qsos.org/sheets/templates/</a>) but you'll have to check in the project's CVS (<a href="http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos">http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos</a>).</li>
	<li>The grid does not exist, check this howto to write it <a href="QSOS-HOWTO_grille_fonctionnelle.php">QSOS HOWTO - Comment créer une nouvelle grille fonctionnelle ?</a></li>
    </ul>

    <h2>QSOS sheets XML format</h2>

	<p>QSOS use XML to formalize and save the evaluation sheets, allowing the use and existence of several tools to use those sheets.</p>

    <p>More details and an XSD Schema to come...</p>

    <h2>Sheet's editors</h2>

    <p>Several QSOS sheets's editors exist that allow to work on the sheet with a more human-friendly view on the sheet.</p>

    <table>
    	<tr>
		<td><b>Editors</b></td>
		<td><b>Technology</b></td>
		<td><b>Comments</b></td>
    	</tr>
    	<tr>
		<td>QSOS Qt Editor</td>
		<td>Qt/Perl</td>
		<td>Works only under with KDE. Download <a href="http://download.savannah.nongnu.org/releases/qsos/qsos-qteditor-0.02.tar.gz">here</a>.</td>
    	</tr>
		<td>QSOS XUL Editor</td>
		<td>Mozilla XUL/Javascript</td>
		<td>Exists as an <a href="http://download.savannah.nongnu.org/releases/qsos/xuleditor-application-0.3.zip">application xulrunner</a> ou d'<a href="http://www.qsos.org/tools/xuleditor-firefox-0.3.xpi">Firefox's extension</a></td>
    	</tr>
    	</tr>
		<td>QSOS Java Editor</td>
		<td>Java/Eclipse SWT</td>
		<td>Works fine on Linux, Windows and Mac OSX. It is the most advanced editor for the moment, please try it !</td>
    	</tr>
    	</tr>
		<td>QSOS Ruby Editor</td>
		<td>Ruby</td>
		<td>Still in developpement</td>
    	</tr>
    </table>

    <p>Those editors allow you to write and edit XML's sheets with the .qsos's extention.</p>

    <h2>Contribut and follow the sheet's evolution</h2>

    <p>Once your sheet is completed, it's essential to send back your sheet to the QSOS community so that's other may used or even correct it.</p>

    <ol>
      <li>Send your sheet on the mailing list : qsos-general@nongnu.org (subscribe here : <a href="http://lists.nongnu.org/mailman/listinfo/qsos-general">http://lists.nongnu.org/mailman/listinfo/qsos-general</a>) or on the french mailing list qsos-french@nongnu.org (subscribe here : <a href="http://lists.nongnu.org/mailman/listinfo/qsos-french">http://lists.nongnu.org/mailman/listinfo/qsos-french</a>).</li>
      <li>The sheet is then integrated to the QSOS referential <a href="https://savannah.nongnu.org/cvs/?group=qsos">CVS</a> by the project's administrator and the all community can access it.</li>
    </ol>

    <p>This document's version : $Id: QSOS-HOWTO_sheet_evaluation.php,v 1.1 2006/08/09 16:07:44 rpelisse Exp $</p>

<?php foot()?>