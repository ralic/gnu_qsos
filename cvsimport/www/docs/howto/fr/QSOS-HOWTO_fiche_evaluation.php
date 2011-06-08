<?php
include("../../../func.php");

$lang = getlang();
head($lang);
?>
    <h1>QSOS HOWTO - Comment créer une nouvelle évaluation QSOS ?</h1>

    Auteur : <a href="mailto:raphael AT semeteys DOT org">Raphaël Semeteys</a>

    <h3>Résumé</h3>

    <p>Ce guide didactique décrit les différentes étapes, bonnes pratiques et outils de création d'une fiche d'évaluation QSOS.</p>

    <h2>Récupérer le modèle de fiche pour le domaine concerné</h2>

    <p>Deux cas sont possibles :</p>

    <ul>
    	<li>La grille fonctionnelle du domaine existe déjà : le site QSOS officiel liste les grilles dans un état finalisé (<a href="http://www.qsos.org/sheets/templates/">http://www.qsos.org/sheets/templates/</a>) mais il faut également vérifier dans le CVS du site du projet (<a href="http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos">http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos</a>).</li>
	<li>La grille n'existe pas, consultez le guide didactique <a href="QSOS-HOWTO_grille_fonctionnelle.php">QSOS HOWTO - Comment créer une nouvelle grille fonctionnelle ?</a></li>
    </ul>

    <h2>Le format XML des fiches QSOS</h2>

    <p>QSOS utilise XML pour formaliser et stocker les fiches d'évaluation. Cela permet à plusieurs outils de coexister pour faciliter la création et plus généralement la manipulation (transformation, stockage, ...) des évaluations QSOS.</p>

    <p>Plus de détails et un schéma XSD à venir sur ce format XML...</p>

    <h2>Les éditeurs disponibles</h2>

    <p>Il existe plusieurs éditeurs de fiches d'évaluations QSOS qui permettent de vous éviter le travail d'écriture et de formattage des balises XML de QSOS :</p>
    <table>
    	<tr>
		<td><b>Editeurs</b></td>
		<td><b>Technologies d'implémentation</b></td>
		<td><b>Commentaires</b></td>
    	</tr>
    	<tr>
		<td>QSOS Qt Editor</td>
		<td>Qt/Perl</td>
		<td>Fonctionne uniquement sur KDE. Téléchargeable <a href="http://download.savannah.nongnu.org/releases/qsos/qsos-qteditor-0.02.tar.gz">ici</a>.</td>
    	</tr>
		<td>QSOS XUL Editor</td>
		<td>Mozilla XUL/Javascript</td>
		<td>Existe sous forme d'<a href="http://download.savannah.nongnu.org/releases/qsos/xuleditor-application-0.3.zip">application xulrunner</a> ou d'<a href="http://www.qsos.org/tools/xuleditor-firefox-0.3.xpi">extension Firefox</a></td>
    	</tr>
    	</tr>
		<td>QSOS Java Editor</td>
		<td>Java</td>
		<td>En cours de finalisation</td>
    	</tr>
    	</tr>
		<td>QSOS Ruby Editor</td>
		<td>Ruby</td>
		<td>En cours de développement</td>
    	</tr>
    </table>

    <p>Ces éditeurs vous permettent donc de créer et modifier des fiches au format XML et avec l'extension .qsos</p>

    <h2>Reverser et suivre l'évolution de la fiche</h2>

    <p>Une fois votre fiche terminée, il est primordial de la reverser à la communauté QSOS pour que d'autres puissent l'utiliser, la compléter ou la corriger.</p>

    <ol>
      <li>Postez votre fiche sur la liste de discussion appropriée, qsos-general@nongnu.org (inscription : <a href="http://lists.nongnu.org/mailman/listinfo/qsos-general">http://lists.nongnu.org/mailman/listinfo/qsos-general</a>) ou la liste francophone qsos-french@nongnu.org (inscription : <a href="http://lists.nongnu.org/mailman/listinfo/qsos-french">http://lists.nongnu.org/mailman/listinfo/qsos-french</a>).</li>
      <li>La fiche est alors intégrée au référentiel <a href="https://savannah.nongnu.org/cvs/?group=qsos">CVS</a> de QSOS par les administrateurs du projet et l'ensemble de la communauté QSOS peut ainsi y accéder.</li>
    </ol>

    <p>Version de ce document : $Id: QSOS-HOWTO_fiche_evaluation.php,v 1.1 2006/07/05 16:30:14 rsemeteys Exp $</p>

<?php foot()?>
