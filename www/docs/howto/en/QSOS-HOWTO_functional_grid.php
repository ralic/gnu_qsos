<?php
include("../../../func.php");

$lang = getlang();
head($lang);
?>
    <h1>QSOS HOWTO - How to design a new functional grid?</h1>

    Author : <a href="mailto:raphael@semeteys.org">Raphaël Semeteys</a>

    <h3>Summary</h3>

    <p>This <i>howto</i> first exposes best practices when designing a the functional grid of a type of software not covered yet by QSOS. It then focuses on how to formalize that grid in the XML QSOS format and contibute it back to all.</p>

    <h2>Step 1 - Design the grid</h2>

    <p>Here comes a few best practices that we recommand you to consider when designing a new functional grid.</p>

    <h3>What to fo before starting</h3>

    <p>Check if agrid doesn't already exist ofor that type of software or for a similar type. The official QSOS website lists finalized grids (<a href="http://www.qsos.org/sheets/templates/">http://www.qsos.org/sheets/templates/</a>), but you should also check the project's CVS (<a href="http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos">http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos</a>).</p>

    <p>Inform the QSOS community of what you're about to, others might already work (or want to) on the same subject : just post a message on the QSOS mailing list qsos-general@nongnu.org (to subscribe : <a href="http://lists.nongnu.org/mailman/listinfo/qsos-general">http://lists.nongnu.org/mailman/listinfo/qsos-general</a>), or whenever not possible, send an email to coordinateur@qsos.org</p> 

    <p>Communicate in the mailing list on your difficulties, doubts or misunderstandings, you'll be given usefull advices, ideas or remarks.</p>

    <h3>Organize your grid</h3>

    <p>Always bear in mind that your grid might be used in contextes you don't know yet. So try to be as generic as possible.</p>

    <p>Regroup your criteria in hierachical levels, your grid will become more structured and readable. However, it is recommanded to limit the number of levels to 3 or 4 maximum.</p>

    <p>The structure often reveals itself as you design the grid, so do not panic if you don't have in minf the global view of it at the earluy beginning.</p>

    <p>Do not hesitate to reorganize the grid if it simplifies its reading and its use.</p>

    <p>Systematically look for already available information as well as your own expertise:</p>

    <ul>
      <li>Use the Internet to find existing comparisons which could give you new ideas and make you pinpoint criteria you didn't think of before yet.</li>
      <li>Check feature lists available on websites of type of sofwtare you are evaluating.</li>
      <li>Always consider several software when designing the functional grid. This will prevent you from building a grid for a specific product only.</li>
      <li>Also consider proprietary products! At this stage the most important objective is to make sure you didn't forget an important aspect of the grid on the way.</li>
    </ul>

    <p>Avoid irrelevant criteria (at least to your eyes), like:</p>

    <ul>
      <li>Subjectives criteria which will be difficult or even impossible to evaluate (like "Performances" for instance). If  you still miss such a criterion, it needs probably to be split in more objective subcriteria (for instance: "Ergonomics" could decome "Ergonomics/GUI global coherence", "Ergonomics/Help/General Help", "Ergonomics/Help/Contectual Help", etc.).</li>
      <li>Too precise crireria or criteria without real new value (like "Number of entries in the menu" for instance).</li>
      <li>Redundant criteria.</li>
    </ul>

    <h4>Critères d'information non notés</h4>

    <p>QSOS allows non scored criteria that still carry information and value the the grid.</p>

    <p>A common dilemna could be illustrated like this: "Should there be one lonely criterion enumarating all possible supported web browser, or should there rather be a single subcriterion per supported browser, the whole being grouped under a single upper-level criteria?".<p>
    <p>

Chaque option a ses avantages et inconvénient, une bonne pratique consiste dans ce cas à choisir la voie du milieu, ainsi dans notre exemple : "Navigateurs Web supportés/Internet Explorer" (évalué), "Navigateurs Web supportés/Mozilla Firefox" (évalué), "Navigateurs Web supportés/Safari" (évalué), "Navigateurs Web supportés/Konqueror" (évalué) et "Navigateurs Web supportés/Autres navigateurs" (liste non évaluée).</p>

    <h2>Step 2 - Formalize the grid in a QSOS XML document</h2>

    <h3>Les grilles fonctionnelles doivent être formalisées au format XML QSOS pour être utilisables</h3>

    <p>Pour générer le fichier XML, plusieurs options se présentent à vous :</p>

    <ul>
      <li>Vous pouvez saisir directement la fiche dans un éditeur de texte en utilisant les balises QSOS. Le bonne pratique est de partir d'une fiche existante.</li>
      <li>Vous pouvez envoyer votre grille au format texte sur la liste de discussion, un autre utilisateur ou un administrateur du projet se chargera certainement de la convertir au format XML pour vous.</li>
      </li>
      <li>Vous pouvez utiliser une version bêta de l'outil graphique "QSOS XUL Template Editor" qui permettra de saisir la grille sans se préoccuper du format XML (<a href="http://cvs.savannah.nongnu.org/viewcvs/qsos/apps/tpl-xuleditor/?root=qsos">accès au CVS</a>). NB : le framework "xulrunner" de Mozilla doit être installé sur votre machine pour que l'éditeur puisse fonctionner.</li>
      <li>Il est primordial de bien remplir toutes le balises du format XML, et notamment les balises &lt;desc0&gt;, &lt;desc1&gt; et &lt;desc2&gt; puisque qu'elles contiennent des indications sur la signification des notes de votre critère (s'il est prévu pour être noté). </li>
      </li>
      <li>De même n'omettez pas de préciser les informations d'entête telles que le domaine auquel s'applique la grille, vos nom, prénom et adresse email, etc...</li>
    </ul>

    <!-- to be removed later -->
    <p>
    Nous sommes conscients qu'actuellement cette étape n'est pas des plus simples. N'hésitez surtout pas à demander de l'aide sur les listes de discussions si jamais vous êtes bloqués ou perdus.
    </p>

    <h2>Step 3 - Share your grid and follow its evolution</h2>

    <p>Une fois votre grille formalisée, il est primordial de la reverser à la communauté QSOS pour que d'autres puissent l'utiliser pour évaluer des logiciels du domaine et aussi pour gérer ses futures évolutions via le référentiel QSOS.</p>

    <ul>
      <li>Postez votre grille sur la liste de discussion appropriée qsos-general@nongnu.org ou qsos-french@nongnu.org</li>
      <li>La grille est alors intégrée au référentiel <a href="https://savannah.nongnu.org/cvs/?group=qsos">CVS</a> de QSOS par les administrateurs du projet et l'ensemble de la communauté QSOS peut ainsi y accéder.</li>
    </ul>

    <p>Version de ce document : $Id: QSOS-HOWTO_functional_grid.php,v 1.1 2006/07/02 22:48:15 rsemeteys Exp $</p>

<?php foot()?>
