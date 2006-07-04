<?php
include("../../../func.php");

$lang = getlang();
head($lang);
?>
    <h1>QSOS HOWTO - Comment créer une nouvelle grille fonctionnelle ?</h1>

    Auteur : <a href="mailto:raphael AT semeteys DOT org">Raphaël Semeteys</a>

    <h3>Résumé</h3>

    <p>Ce guide didactique couvre deux sujets principaux. Il présente tout d'abord les bonnes pratiques lorsque l'on désire concevoir une grille fonctionnelle QSOS pour un nouveau type de logiciel, il se concentre ensuite sur le marche à suivre pour formaliser la grille au format XML de QSOS et la reverser à la communauté.</p>

    <h2>Concevoir la grille</h2>

    <p>Voici une liste de bonnes pratiques que nous vous recommandons d'appliquer lors de la conception de la grille.</p>

    <h3>Avant de commencer</h3>

    <p>Vérifiez si une grille sur le sujet ou un sujet connexe n'existe pas déjà. Le site QSOS officiel liste les grilles dans un état finalisé (<a href="http://www.qsos.org/sheets/templates/">http://www.qsos.org/sheets/templates/</a>) mais il faut également vérifier dans le CVS du site du projet (<a href="http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos">http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos</a>).</p>

    <p>Prévenez le reste de la communauté QSOS de ce que vous vous appréter à faire, en effet il se peut que d'autres travaillent (ou désirent travailler avec vous) sur le sujet. Pour ce faire il est recommandé de poster un message sur la liste de discussion de QSOS qsos-general@nongnu.org (inscription : <a href="http://lists.nongnu.org/mailman/listinfo/qsos-general">http://lists.nongnu.org/mailman/listinfo/qsos-general</a>) ou la liste francophone qsos-french@nongnu.org (inscription : <a href="http://lists.nongnu.org/mailman/listinfo/qsos-french">http://lists.nongnu.org/mailman/listinfo/qsos-french</a>), ou à défaut, d'envoyer un message à coordinateur@qsos.org</p> 

    <p>Décidez dans quelle langue vous voulez travailler, l'idéal est de respecter la langue officielle de QSOS à savoir l'anglais mais il est également possible de travailler en français si cela vous arrange, d'autres (ou peut être vous même) traduiront la grille si le besoin s'en fait sentir.</p>

    <p>Communiquez vos problèmes, interrogations, doutes ou versions draft de votre grille sur la liste de discussion, vous obtiendrez ainsi une aide précieuse sous forme d'idées, d'avis, de remarques.</p>

    <h3>Organisez votre grille</h3>

    <p>Gardez toujours à l'esprit que votre grille peut être utilisée dans des contextes que vous ne connaissez pas. Essayez donc de prendre le maximum de recul possible et de tendre vers la généricité.</p>

    <p>Regroupez vos critères en niveaux hiérarchiques, cela facilite la lecture et l'organisation de la grille. Il est cependant recommandé de se limiter à 3 (voire 4) niveaux maximum.</p>

    <p>Cette structure peut émerger au fur et à mesure de la conception de la grille, donc pas de panique si vous n'avez pas la vision globale de sa structure dès le départ.</p>

    <p>N'hésitez pas à réorganiser la grille si cela va dans le sens de la simplification et de la facilité de lecture ou d'utilisation.</p>

    <p>Recherchez systématiquement l'information déjà disponible, en plus de votre expertise :</p>

    <ul>
      <li>Faites des recherches sur Internet, on trouve souvent des comparatifs qui peuvent vous donner des idées, vous aider à identifier un critère auquel vous n'aviez pas pensé.</li>
      <li>Exploitez les "feature lists" mises à disposition sur les sites des logiciels du domaine concerné par la grille.</li>
      <li>Ayez toujours en tête plusieurs logiciels lorsque vos réalisez la grille, ceci afin d'éviter de faire une grille du le logiciel A plutôt que celle du domaine dans lequel se range le logiciel A.</li>
      <li>Intégrer également les solutions propriétaires dans votre analyse ! En effet, à ce stade l'objectif est d'obtenir la plus grande couverture possible pour ne pas passer à coté d'un critère important. Charge à vous ensuite de synthétiser et d'éliminer/fusionner des critères non pertinents.</li>
    </ul>

    <p>Evitez les critères qui ne vous semblent pas pertinents, comme par exemple : </p>

    <ul>
      <li>Critères subjectifs qu'il sera difficile voir impossible d'évaluer. Exemple : "Performances". Si le critère vous semble tout de même important mais semble tout de même subjectif, c'est probablement parce qu'il est trop vague. Il est conseillé de le décomposer en plusieurs sous critères qui seront eux plus objectifs et donc plus facilement évaluables. Exemple : "Ergonomie" peut devenir "Ergonomie/Cohérence graphique de l'IHM, Ergonomie/Aide/Aide générale, Ergonomie/Aide/Aide contextuelle, etc...).</li>
      <li>Critères trop précis et sans réelle valeur ajoutée. Exemple : "Nombre d'entrées de le menu".</li>
      <li>Critères redondants.</li>
    </ul>

    <h4>Critères d'information non notés</h4>

    <p>QSOS permet l'utilisation de critères non destinés à être notés, ils font cependant partie intégrante d'une grille car ils apportent de l'information et de la valeur à l'évaluation.</p>

    <p>Un dilemme se pose fréquemment à ce sujet : vaut-il mieux prévoir d'un seul critère sous forme d'une énumération de navigateurs Web supportés, ou plutôt un sous-critère pour chaque navigateur Web supporté, le tout étant regroupé sous un critère de niveau supérieur ?<p>
    <p>Chaque option a ses avantages et inconvénient, une bonne pratique consiste dans ce cas à choisir la voie du milieu, ainsi dans notre exemple : "Navigateurs Web supportés/Internet Explorer" (évalué), "Navigateurs Web supportés/Mozilla Firefox" (évalué), "Navigateurs Web supportés/Safari" (évalué), "Navigateurs Web supportés/Konqueror" (évalué) et "Navigateurs Web supportés/Autres navigateurs" (liste non évaluée).</p>

    <h2>Formaliser la grille au format XML de QSOS</h2>

    <h3>Les grilles fonctionnelles doivent être formalisées au format XML QSOS pour être utilisables</h3>

    <p>Pour générer le fichier XML, plusieurs options se présentent à vous :</p>

    <ul>
      <li>Vous pouvez saisir directement la fiche dans un éditeur de texte en utilisant les balises QSOS. Le bonne pratique est de partir d'une fiche existante.</li>
      <li>Vous pouvez envoyer votre grille au format texte sur la liste de discussion, un autre utilisateur ou un administrateur du projet se chargera certainement de la convertir au format XML pour vous.</li>
      <li>Vous pouvez utiliser une version bêta de l'outil graphique "QSOS XUL Template Editor" qui permettra de saisir la grille sans se préoccuper du format XML (<a href="http://cvs.savannah.nongnu.org/viewcvs/qsos/apps/tpl-xuleditor/?root=qsos">accès au CVS</a>). NB : le framework "xulrunner" de Mozilla doit être installé sur votre machine pour que l'éditeur puisse fonctionner.</li>
    </ul>

    <p>Quel que soit votre choix :</p>

    <ul>
      <li>Il est primordial de bien remplir ou fournir les informations concernant toutes les balises du format XML, et notamment les balises &lt;desc0&gt;, &lt;desc1&gt; et &lt;desc2&gt; puisque qu'elles contiennent des indications sur la signification des notes de votre critère (s'il est prévu pour être noté). </li>
      <li>De même n'omettez pas de préciser les informations d'entête telles que le domaine auquel s'applique la grille, vos nom, prénom et adresse email, etc...</li>
    </ul>

    <!-- a retirer plus tard -->
    <p>
    Nous sommes conscients qu'actuellement cette étape n'est pas des plus simples. N'hésitez surtout pas à demander de l'aide sur les listes de discussions si jamais vous êtes bloqués ou perdus.
    </p>

    <h2>Reverser et suivre l'évolution de la grille</h2>

    <p>Une fois votre grille formalisée, il est primordial de la reverser à la communauté QSOS pour que d'autres puissent l'utiliser pour évaluer des logiciels du domaine et aussi pour gérer ses futures évolutions via le référentiel QSOS.</p>

    <ol>
      <li>Postez votre grille sur la liste de discussion appropriée qsos-general@nongnu.org ou qsos-french@nongnu.org</li>
      <li>La grille est alors intégrée au référentiel <a href="https://savannah.nongnu.org/cvs/?group=qsos">CVS</a> de QSOS par les administrateurs du projet et l'ensemble de la communauté QSOS peut ainsi y accéder.</li>
    </ol>

    <p>Version de ce document : $Id: QSOS-HOWTO_grille_fonctionnelle.php,v 1.2 2006/07/04 23:21:01 rsemeteys Exp $</p>

<?php foot()?>
