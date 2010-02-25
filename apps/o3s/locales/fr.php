<?php
/*
**  Copyright (C) 2007-2009 Atos Origin 
**
**  Author: Raphael Semeteys <raphael.semeteys@atosorigin.com>
**
**  This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
**
**
** O3S
** fr-FR locale
**
*/

//General
$msg['g_license_notice'] = "Le contenu de cette page est soumis à la  <a href='http://www.gnu.org/copyleft/fdl.html'>GNU Free Documentation License</a>";

//Step 1 (index.php)
$msg['s1_title'] = "Sélectionner la langue et le domaine logiciel";
$msg['s1_table_title'] = "Domaines logiciels";
$msg['s1_table_templateversion'] = "Versions de grille";
$msg['s1_table_nbeval'] = "Evaluations disponibles";
$msg['s1_search'] = "ou effectuer une recherche par mots-clés";
$msg['s1_button'] = "Chercher";
//Step 1 (search.php)
$msg['s1_button_search'] = "Chercher";
$msg['s1_button_back'] = "Retour";
$msg['s1_search_msg1'] = "Désolé. La recherche sur ";
$msg['s1_search_msg2'] = " n'a retourné aucun résultat.";

//Step 2 (set_weighting.php)
$msg['s2_loaded'] = " chargé, n'oubliez pas de sauvegarder.";
$msg['s2_error1'] = " et ";
$msg['s2_error2'] = " ne correspondent pas.";
$msg['s2_title'] = "Entrer vos pondérations";
$msg['s2_button_back'] = "< Retour";
$msg['s2_button_save'] = "Sauvegarder";
$msg['s2_button_saveFile'] = "Exporter en XML";
$msg['s2_button_next'] = "Continuer >";
$msg['s2_button_upload'] = "Charger";
$msg['s2_weight'] = "Poids";
$msg['s2_err_weight'] = " n'est pas une valeur possible, elle a été remise à 1.";
$msg['s2_err_no_file'] = "Aucun fichier de pondération n'a été fourni";

//Step 3 (list.php)
$msg['s3_family'] = "Domaine logiciel : ";
$msg['s3_title'] = "Sélectionner le(s) logiciel(s)";
$msg['s3_button_back'] = "< Retour";
$msg['s3_software'] = "Logiciels";
$msg['s3_table_criteria'] = "Critères";
$msg['s3_table_completed'] = "Complet";
$msg['s3_table_commented'] = "Commenté";
$msg['s3_table_view'] = "Visualiser";
$msg['s3_format_html'] = "HTML";
$msg['s3_format_xml'] = "XML";
$msg['s3_set_weights'] = "Pondérer la grille";
$msg['s3_show_mindmap'] = "Visualiser la grille";
$msg['s3_format_odf'] = "Comparatif ODF";
$msg['s3_format_html_tooltip'] = "Voir au format HTML";
$msg['s3_format_xml_tooltip'] = "Voir au format XML natif de QSOS";
$msg['s3_format_odf_tooltip'] = "Voir au format OpenDocument (tableur)";
$msg['s3_table_compare'] = "Comparer";
$msg['s3_button_next'] = "Comparatif en ligne";
$msg['s3_graph'] = "Comparatif graphique";
$msg['s3_quadrant'] = "Quadrant";
$msg['s3_check_svg'] = "Mon navigateur supporte SVG";
$msg['s3_err_js_no_file'] = "Au moins un logiciel doit être coché";
$msg['s3_err_no_family'] = " non trouvée dans le référentiel";

//Step 4 (show.php)
$msg['s4_title'] = "Visualiser";
$msg['s4_button_back'] = "< Retour";
$msg['s4_button_back_alt'] = "Retour";
$msg['s4_comments'] = "Commentaires";
$msg['s4_score'] = "Note";
$msg['s4_weight'] = "Poids";
$msg['s4_err_no_id'] = " non trouvé dans le référentiel";

//Step 5 (radar.php)
$msg['s5_error'] = "Aucun fichier QSOS n'est fourni !";
$msg['s5_back'] = "Retour";
$msg['s5_up'] = "Remonter d'un niveau";

//ODS export
$msg['ods_criteria'] = "Critères";
$msg['ods_evaluation'] = "Evaluation";
$msg['ods_graph'] = "Graphique";
$msg['ods_softwarefamily'] = "Domaine logiciel";
$msg['ods_qsosversion'] = "Version de QSOS";
$msg['ods_templateversion'] = "Version de la grille";
$msg['ods_criterion'] = "Critère";
$msg['ods_desc'] = "Description";
$msg['ods_score0'] = "Score 0";
$msg['ods_score1'] = "Score 1";
$msg['ods_score2'] = "Score 2";
$msg['ods_application'] = "Application";
$msg['ods_release'] = "Version";
$msg['ods_license'] = "Licence";
$msg['ods_website'] = "Site Web";
$msg['ods_description'] = "Description";
$msg['ods_authors'] = "Auteurs";
$msg['ods_creationdate'] = "Date de création";
$msg['ods_validationdate'] = "Date de validation";
$msg['ods_comment'] = "Commentaires";
$msg['ods_score'] = "Score";
$msg['ods_weight'] = "Poids";
$msg['ods_header'] = "Analyse QSOS";
$msg['ods_synthesis'] = "Synthèse";
$msg['ods_synthesis_title'] = "Synthèse et comparatif dynamique";
$msg['ods_citeria_title'] = "Explications des critères utilisés";
$msg['ods_evaluation_title'] = "Évaluation de ";
$msg['ods_val_title'] = "Modifier le poids";
$msg['ods_val_error'] = "Erreur";
$msg['ods_val_helpmsg'] = "Saisir un nombre entier";
$msg['ods_val_errormsg'] = "Seules les valeurs nulles ou entières sont autorisées";
$msg['ods_note_weight'] = "Vous pouvez modifier le poids des critères dans la colonne B (Poids)";

//QSOS quadrant
$msg['qq_maturity'] = "Maturité";
$msg['qq_funccoverage'] = "Couverture fonctionnelle";

?>
