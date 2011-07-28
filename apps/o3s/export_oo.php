<?php
/**
 *  Copyright (C) 2007-2011 Atos
 *
 *  Author: Raphael Semeteys <raphael.semeteys@atos.net>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 *  O3S
 *  export_oo.php: OpenDocument export
 *
**/


session_start();

include("config.php");
include("lang.php");
include("libs/QSOSDocument.php");
include('libs/pclzip.lib.php');

//Ids of XML files to be exported
$ids = $_REQUEST['id'];
//Name of the ODS file
$odsfile = "QSOS.ods";

//Global variables
$numrow;
$graph_formula_module;
$output;
$input;
$document;
$table0;
$table1;

function getTableName($id) {
  $output = basename($id, ".qsos");
  //OOo 2.x doesn't accept . in tab names
  return str_replace(".", "-", $output);
}

function createCell($style, $type, $value, $formula=false, $validator=false) {
  global $output;

  //HACK: & caracter causes an error because of HTML entities
  $value = str_replace("&", "+", $value);

  $cell = $output->createElement('table:table-cell');
  if ($style != "") $cell->setAttribute("table:style-name",$style);
  if (!($formula)) {
    $cell->setAttribute("office:value-type",$type);
    $cell->setAttribute("office:value",$value);
    $text = $output->createElement('text:p',$value);
    $cell->appendChild($text);
  } else {
    $cell->setAttribute("table:formula",$formula);
  }
  if ($validator) {
    $cell->setAttribute("table:content-validation-name",$validator);
  }

  return $cell;
}

function getFormula($cells) {
  $quotient = "";
  $dividend = "";
  for ($i=0; $i < count($cells); $i++) {
    if ($i != 0) {
      $quotient .= "+";
      $dividend .= "+";
    }
    $quotient .= "[.C".$cells[$i]."]*[.D".$cells[$i]."]";
    $dividend .= "[.D".$cells[$i]."]";
  }
  return "oooc:=IF(($dividend)=0;0;($quotient)/($dividend))";
}

function createTreeCriteria($tree, $table0, $depth) {
  global $output;
  global $input;
  global $numrow;
  $children = array();

  $new_depth = $depth + 1;
  $offset = $new_depth*10;
  $idF = 0;

  switch ($depth) {
    case '0':
      //Section
      $style_row = 'ro1';
      $style_title = 'ce2';
      break;
    case '1':
      //Level 1 criterion
      $style_row = 'ro1';
      $style_title = 'ce3';
      break;
    case '2':
      //Level 2 criterion
      $style_row = 'ro1';
      $style_title = '';
      break;
    default:
      //Level N criterion,  N > 2
      $style_row = 'ro1';
      $style_title = 'ce8';
      break;
  }

  foreach($tree as $element) {
    $name = $element->name;
    $title = $element->title;
    $subtree = $element->children;
    $comment = $input->getgeneric($name, "comment");

    $numrow++;
    array_push($children, $numrow);

    //New row for first sheet (table0, criteria)
    $row = $output->createElement('table:table-row');
    $row->setAttribute("table:style-name",$style_row);
    //Criterion
    $row->appendChild(createCell($style_title, "string", $title));

    //Desc, Desc0, 1 and 2
    $row->appendChild(createCell($style_title, "string", $input->getgeneric($name, "desc")));
    $row->appendChild(createCell($style_title, "string", $input->getgeneric($name, "desc0")));
    $row->appendChild(createCell($style_title, "string", $input->getgeneric($name, "desc1")));
    $row->appendChild(createCell($style_title, "string", $input->getgeneric($name, "desc2")));
    $table0->appendChild($row);

    //Recursivity
    if ($subtree) {
      //Subcriteria regrouping
      $group0 = $output->createElement('table:table-row-group');
      $return = createTreeCriteria($subtree, $group0, $new_depth);
      $table0->appendChild($group0);
    }
  }
  return $children;
}

function createTreeSynthesis($tree, $table0, $depth) {
  global $output;
  global $input;
  global $numrow;
  global $ids;
  $children = array();

  $new_depth = $depth + 1;
  $offset = $new_depth*10;
  $idF = 0;

  switch ($depth) {
    case '0':
      //Section
      $style_row = 'ro1';
      $style_title = 'ce2';
      $style_comment = 'ce2';
      $style_score = 'ce5';
      $style_weight = 'ce5';
      break;
    case '1':
      //Level 1 criterion
      $style_row = 'ro1';
      $style_title = 'ce3';
      $style_comment = 'ce3';
      $style_score = 'ce6';
      $style_weight = 'ce6';
      break;
    case '2':
      //Level 2 criterion
      $style_row = 'ro1';
      $style_title = '';
      $style_comment = '';
      $style_score = '';
      $style_weight = '';
      break;
    default:
      //Level N criterion,  N > 2
      $style_row = 'ro1';
      $style_title = 'ce8';
      $style_comment = 'ce8';
      $style_score = 'ce9';
      $style_weight = 'ce9';
      break;
  }

  foreach($tree as $element) {
    $name = $element->name;
    $title = $element->title;
    $subtree = $element->children;
    $comment = $input->getgeneric($name, "comment");

    $numrow++;
    array_push($children, $numrow);

    $row = $output->createElement('table:table-row');
    $row->setAttribute("table:style-name",$style_row);
    //Criterion
    $row->appendChild(createCell($style_title, "string", $title));
    $table0->appendChild($row);
    //Weight
    $weight = isset($_SESSION[$name])?$_SESSION[$name]:1;
    $row->appendChild(createCell($style_weight, "float", $weight, false, "val1"));
    //Scores
    foreach($ids as $id) {
      $name = getTableName($id);
      $num = $numrow + 7;
      $row->appendChild(createCell($style_score, "string", null, "oooc:=['$name'.C$num]"));
      $table0->appendChild($row);
    }

    //Recursivity
    if ($subtree) {
      //Subcriteria regrouping
      $group0 = $output->createElement('table:table-row-group');
      $return = createTreeSynthesis($subtree, $group0, $new_depth);
      $table0->appendChild($group0);
    }
  }
  return $children;
}

function createTreeEval($tree, $table1, $depth) {
  global $output;
  global $input;
  global $numrow;
  global $msg;
  $children = array();

  $new_depth = $depth + 1;
  $offset = $new_depth*10;
  $idF = 0;

  switch ($depth) {
    case '0':
      //Section
      $style_row = 'ro1';
      $style_title = 'ce2';
      $style_comment = 'ce2';
      $style_score = 'ce5';
      $style_weight = 'ce5';
      break;
    case '1':
      //Level 1 criterion
      $style_row = 'ro1';
      $style_title = 'ce3';
      $style_comment = 'ce3';
      $style_score = 'ce6';
      $style_weight = 'ce6';
      break;
    case '2':
      //Level 2 criterion
      $style_row = 'ro1';
      $style_title = '';
      $style_comment = '';
      $style_score = '';
      $style_weight = '';
      break;
    default:
      //Level N criterion,  N > 2
      $style_row = 'ro1';
      $style_title = 'ce8';
      $style_comment = 'ce8';
      $style_score = 'ce9';
      $style_weight = 'ce9';
      break;
  }

  foreach($tree as $element) {
    $name = $element->name;
    $title = $element->title;
    $subtree = $element->children;
    $comment = $input->getgeneric($name, "comment");

    $numrow++;
    array_push($children, $numrow);

    //New row for second sheet (table1, evaluation)
    $row = $output->createElement('table:table-row');
    $row->setAttribute("table:style-name",$style_row);
    //Criterion
    $row->appendChild(createCell($style_title, "string", $title));
    //Comment
    $row->appendChild(createCell($style_comment, "string", $comment));
    //Score
    $score = createCell($style_score, "float", $element->score);
    $row->appendChild($score);
    //Weight
    $num = $numrow - 7;
    $row->appendChild(createCell($style_weight, "float", null, "oooc:=['".$msg['ods_synthesis']."'.B$num]"));
    $table1->appendChild($row);

    //Recursivity
    if ($subtree) {
      //Subcriteria regrouping
      $group = $output->createElement('table:table-row-group');
      $return = createTreeEval($subtree, $group, $new_depth);
      //Set score formula
      $score->setAttribute("table:formula",getFormula($return));
      $table1->appendChild($group);
    }
  }
  return $children;
}

function createColumn($style,$styledefault) {
  global $output;
  $column = $output->createElement('table:table-column');
  $column->setAttribute("table:style-name",$style);
  $column->setAttribute("table:default-cell-style-name",$styledefault);
  return $column;
}

function createSimpleRow() {
  global $output;
  $row = $output->createElement('table:table-row');
  $row->setAttribute("table:style-name","ro1");
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","Default");
  $cell->setAttribute("table:number-columns-repeated","4");
  $row->appendChild($cell);
  return $row;
}

function createHeaderRow($title,$value) {
  global $output;
  $row = $output->createElement('table:table-row');
  $row->setAttribute("table:style-name","ro1");
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce2");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$title);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce8");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$value);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce10");
  $cell->setAttribute("table:number-columns-repeated","2");
  $row->appendChild($cell);
  return $row;
}

function createTitleRow1() {
  global $output;
  global $msg;

  $row = $output->createElement('table:table-row');
  $row->setAttribute("table:style-name","ro1");
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce15");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_header']);
  $cell->appendChild($text);
  $row->appendChild($cell);

  return $row;
}

function createTitleRow2($title) {
  global $output;

  $row = $output->createElement('table:table-row');
  $row->setAttribute("table:style-name","ro1");
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce14");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$title);
  $cell->appendChild($text);
  $row->appendChild($cell);

  return $row;
}

function createTitleRow3($title) {
  global $output;

  $row = $output->createElement('table:table-row');
  $row->setAttribute("table:style-name","ro1");
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce13");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$title);
  $cell->appendChild($text);
  $row->appendChild($cell);

  return $row;
}

function createValidator() {
  global $output;
  global $msg;

  $validators = $output->createElement('table:content-validations');

  $validator = $output->createElement('table:content-validation');
  $validator->setAttribute("table:name","val1");
  $validator->setAttribute("table:condition","oooc:cell-content-is-whole-number() and cell-content()>=0");
  $validator->setAttribute("table:allow-empty-cell","false");
  $validator->setAttribute("table:base-cell-address",$msg['ods_synthesis']."B6");

  $help = $output->createElement('table:help-message');
  $help->setAttribute("table:title",$msg['ods_val_title']);
  $help->setAttribute("table:display","true");
  $text = $output->createElement('text:p',$msg['ods_val_helpmsg']);
  $help->appendChild($text);
  $validator->appendChild($help);

  $error = $output->createElement('table:error-message');
  $error->setAttribute("table:message-type","stop");
  $error->setAttribute("table:title",$msg['ods_val_error']);
  $error->setAttribute("table:display","true");
  $text = $output->createElement('text:p',$msg['ods_val_errormsg']);
  $error->appendChild($text);
  $validator->appendChild($error);

  $validators->appendChild($validator);

  return $validators;
}

function createFont($fontFamily) {
  global $output;
  $font = $output->createElement('style:font-face');
  $font->setAttribute("style:name",$fontFamily);
  $font->setAttribute("svg:font-family","'$fontFamily'");
  $font->setAttribute("style:font-pitch","variable");
  return $font;
}

function createColumnStyle($name,$width) {
  global $output;
  $style = $output->createElement('style:style');
  $style->setAttribute("style:name",$name);
  $style->setAttribute("style:family","table-column");
  $substyle = $output->createElement('style:table-column-properties');
  $substyle->setAttribute("fo:break-before","auto");
  $substyle->setAttribute("style:column-width",$width);
  $style->appendChild($substyle);
  return $style;
}

function createRowStyle($name,$height) {
  global $output;
  $style = $output->createElement('style:style');
  $style->setAttribute("style:name",$name);
  $style->setAttribute("style:family","table-row");
  $substyle = $output->createElement('style:table-row-properties');
  $substyle->setAttribute("style:row-height",$height);
  $substyle->setAttribute("fo:break-before","auto");
  $substyle->setAttribute("style:use-optimal-row-height","true");
  $style->appendChild($substyle);
  return $style;
}

function createCellStyle($name, $wrap, $backgroundColor, $textAlignSource, $repeatContent, $verticalALign, $textAlign, $marginLeft, $fontColor, $fontWeight, $border, $fontSize, $fontStyle) {
  global $output;
  $style = $output->createElement('style:style');
  $style->setAttribute("style:name",$name);
  $style->setAttribute("style:family","table-cell");
  $style->setAttribute("style:parent-style-name","Default");

  if (isset($wrap) || isset($backgroundColor) || isset($textAlignSource) || isset($repeatContent) || isset($verticalALign) || isset($border)) {
    $substyle = $output->createElement('style:table-cell-properties');
    if (isset($wrap)) $substyle->setAttribute("fo:wrap-option",$wrap);
    if (isset($backgroundColor)) $substyle->setAttribute("fo:background-color",$backgroundColor);
    if (isset($border)) $substyle->setAttribute("fo:border","0.002cm solid #000000");
    if (isset($textAlignSource)) $substyle->setAttribute("style:text-align-source",$textAlignSource);
    if (isset($repeatContent)) $substyle->setAttribute("style:repeat-content",$repeatContent);
    if (isset($verticalALign)) $substyle->setAttribute("style:vertical-align",$verticalALign);
    $style->appendChild($substyle);
  }

  if (isset($textAlign) || isset($marginLeft)) {
    $substyle = $output->createElement('style:paragraph-properties');
    if (isset($textAlign)) $substyle->setAttribute("fo:text-align",$textAlign);
    if (isset($marginLeft)) $substyle->setAttribute("fo:margin-left",$marginLeft);
    $style->appendChild($substyle);
  }

  if (isset($fontColor) || isset($fontWeight) || isset($fontSize) || isset($fontStyle)) {
    $substyle = $output->createElement('style:text-properties');
    if (isset($fontColor)) $substyle->setAttribute("fo:color",$fontColor);
    if (isset($fontWeight)) $substyle->setAttribute("fo:font-weight",$fontWeight);
    if (isset($fontSize)) $substyle->setAttribute("fo:font-size",$fontSize."pt");
    if (isset($fontStyle)) $substyle->setAttribute("fo:font-style",$fontStyle);
    $style->appendChild($substyle);
  }

  return $style;
}

function initSynthesisSheet() {
  global $output;
  global $input;
  global $table0;
  global $msg;
  global $ids;

  $table0 = $output->createElement('table:table');
  $table0->setAttribute("table:name",$msg['ods_synthesis']);
  $table0->setAttribute("table:style-name","ta1");
  $table0->setAttribute("table:print","false");

  $table0->appendChild(createColumn("co0","ce4"));
  $table0->appendChild(createColumn("co4","ce7"));

  foreach($ids as $id) {
    $table0->appendChild(createColumn("co0","ce7"));
  }

  //Title
  $table0->appendChild(createTitleRow1());
  $table0->appendChild(createTitleRow2($input->getkey("qsosappfamily")));
  $table0->appendChild(createTitleRow3($msg['ods_synthesis_title']));

  $table0->appendChild(createSimpleRow());

  //Note on weight modification
  $row = $output->createElement('table:table-row');
  $row->setAttribute("table:style-name","ro1");
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce16");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_note_weight']);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $table0->appendChild($row);

  $table0->appendChild(createSimpleRow());

  //Criteria
  $row = $output->createElement('table:table-row');
  $row->setAttribute("table:style-name","ro1");
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce11");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_criterion']);
  $cell->appendChild($text);
  $row->appendChild($cell);

  //Weight
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce12");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_weight']);
  $cell->appendChild($text);
  $row->appendChild($cell);

  //Evaluations
  foreach($ids as $id) {
    $name = getTableName($id);
    $cell = $output->createElement('table:table-cell');
    $cell->setAttribute("table:style-name","ce12");
    $cell->setAttribute("office:value-type","string");
    $text = $output->createElement('text:p',$name);
    $cell->appendChild($text);
    $row->appendChild($cell);
  }

  $table0->appendChild($row);
}

function initCriteriaSheet() {
  global $output;
  global $input;
  global $table0;
  global $msg;

  //First sheet (Criteria)
  $table0 = $output->createElement('table:table');
  $table0->setAttribute("table:name",$msg['ods_criteria']);
  $table0->setAttribute("table:style-name","ta1");
  $table0->setAttribute("table:print","false");

  $table0->appendChild(createColumn("co0","ce4"));
  $table0->appendChild(createColumn("co0","ce4"));
  $table0->appendChild(createColumn("co0","ce4"));
  $table0->appendChild(createColumn("co0","ce4"));
  $table0->appendChild(createColumn("co0","ce4"));

  //Title
  $table0->appendChild(createTitleRow1());
  $table0->appendChild(createTitleRow2($input->getkey("qsosappfamily")));
  $table0->appendChild(createTitleRow3($msg['ods_citeria_title']));

  $table0->appendChild(createSimpleRow());

  //QSOS version
  $table0->appendChild(createHeaderRow($msg['ods_qsosversion'],$input->getkey("qsosformat")));

  //Template version
  $table0->appendChild(createHeaderRow($msg['ods_templateversion'],$input->getkey("qsosspecificformat")));

  $table0->appendChild(createSimpleRow());

  //Criteria
  $row = $output->createElement('table:table-row');
  $row->setAttribute("table:style-name","ro1");
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce11");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_criterion']);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce11");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_desc']);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce12");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_score0']);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce12");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_score1']);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce12");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_score2']);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $table0->appendChild($row);
}

function initEvaluationSheet($title) {
  global $output;
  global $input;
  global $table1;
  global $msg;

  //Second sheet (Evaluation)
  $table1 = $output->createElement('table:table');
  $table1->setAttribute("table:name",$title);
  $table1->setAttribute("table:style-name","ta1");
  $table1->setAttribute("table:print","false");

  $table1->appendChild(createColumn("co1","ce4"));
  $table1->appendChild(createColumn("co2","ce4"));
  $table1->appendChild(createColumn("co3","ce7"));
  $table1->appendChild(createColumn("co4","ce7"));

  //Title
  $header = $msg['ods_evaluation_title'].$input->getkey("appname")." ".$input->getkey("release");
  $table1->appendChild(createTitleRow1());
  $table1->appendChild(createTitleRow2($input->getkey("qsosappfamily")));
  $table1->appendChild(createTitleRow3($header));

  $table1->appendChild(createSimpleRow());

  //Header
  //Application
  $table1->appendChild(createHeaderRow($msg['ods_application'],$input->getkey("appname")));

  //Release
  $table1->appendChild(createHeaderRow($msg['ods_release'],$input->getkey("release")));

  //License
  $table1->appendChild(createHeaderRow($msg['ods_license'],$input->getkey("licensedesc")));

  //Url
  $table1->appendChild(createHeaderRow($msg['ods_website'],$input->getkey("url")));

  //Description
  $table1->appendChild(createHeaderRow($msg['ods_description'],$input->getkey("desc")));

  //Authors
  $authors = $input->getauthors();
  $list = "";
  for ($i=0; $i < count($authors); $i++) {
    if ($i != 0) {
      $list .= ", ";
    }
    $list .= $authors[$i]->name." (".$authors[$i]->email.")";
  }
  $table1->appendChild(createHeaderRow($msg['ods_authors'],$list));

  //Creation date
  $table1->appendChild(createHeaderRow($msg['ods_creationdate'],$input->getkey("creation")));

  //Validation date
  $table1->appendChild(createHeaderRow($msg['ods_validationdate'],$input->getkey("validation")));

  $table1->appendChild(createSimpleRow());

  //Criteria
  $row = $output->createElement('table:table-row');
  $row->setAttribute("table:style-name","ro1");
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce11");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_criterion']);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce11");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_comment']);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce12");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_score']);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $cell = $output->createElement('table:table-cell');
  $cell->setAttribute("table:style-name","ce12");
  $cell->setAttribute("office:value-type","string");
  $text = $output->createElement('text:p',$msg['ods_weight']);
  $cell->appendChild($text);
  $row->appendChild($cell);
  $table1->appendChild($row);
}

function initDocument() {
  global $output;

  //MAIN DOCUMENT ELEMENT
  $document = $output->createElement('office:document-content');
  $document->setAttribute("xmlns:office","urn:oasis:names:tc:opendocument:xmlns:office:1.0");
  $document->setAttribute("xmlns:style","urn:oasis:names:tc:opendocument:xmlns:style:1.0");
  $document->setAttribute("xmlns:text","urn:oasis:names:tc:opendocument:xmlns:text:1.0");
  $document->setAttribute("xmlns:table","urn:oasis:names:tc:opendocument:xmlns:table:1.0");
  $document->setAttribute("xmlns:draw","urn:oasis:names:tc:opendocument:xmlns:drawing:1.0");
  $document->setAttribute("xmlns:fo","urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0");
  $document->setAttribute("xmlns:xlink","http://www.w3.org/1999/xlink");
  $document->setAttribute("xmlns:dc","http://purl.org/dc/elements/1.1/");
  $document->setAttribute("xmlns:meta","urn:oasis:names:tc:opendocument:xmlns:meta:1.0");
  $document->setAttribute("xmlns:number","urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0");
  $document->setAttribute("xmlns:svg","urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0");
  $document->setAttribute("xmlns:chart","urn:oasis:names:tc:opendocument:xmlns:chart:1.0");
  $document->setAttribute("xmlns:dr3d","urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0");
  $document->setAttribute("xmlns:math","http://www.w3.org/1998/Math/MathML");
  $document->setAttribute("xmlns:form","urn:oasis:names:tc:opendocument:xmlns:form:1.0");
  $document->setAttribute("xmlns:script","urn:oasis:names:tc:opendocument:xmlns:script:1.0");
  $document->setAttribute("xmlns:ooo","http://openoffice.org/2004/office");
  $document->setAttribute("xmlns:ooow","http://openoffice.org/2004/writer");
  $document->setAttribute("xmlns:oooc","http://openoffice.org/2004/calc");
  $document->setAttribute("xmlns:dom","http://www.w3.org/2001/xml-events");
  $document->setAttribute("xmlns:xforms","http://www.w3.org/2002/xforms");
  $document->setAttribute("xmlns:xsd","http://www.w3.org/2001/XMLSchema");
  $document->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
  $document->setAttribute("office:version","1.0");

  //FONT DECLARATIONS
  $fontfaces = $output->createElement('office:font-face-decls');
  $fontfaces->appendChild(createFont("Lucida Sans Unicode"));
  $fontfaces->appendChild(createFont("Tahoma"));
  $fontfaces->appendChild(createFont("Arial"));
  $document->appendChild($fontfaces);

  //STYLE DECLARATIONS
  $styles = $output->createElement('office:automatic-styles');
  //Column styles
  $styles->appendChild(createColumnStyle("co0","4.717cm"));
  $styles->appendChild(createColumnStyle("co1","5.117cm"));
  $styles->appendChild(createColumnStyle("co2","10.931cm"));
  $styles->appendChild(createColumnStyle("co3","1.452cm"));
  $styles->appendChild(createColumnStyle("co4","1.452cm"));
  //Row styles
  $styles->appendChild(createRowStyle("ro1","0.453cm"));
  $styles->appendChild(createRowStyle("ro2","0.453cm"));
  //ta1: basic table
  $style = $output->createElement('style:style');
  $style->setAttribute("style:name","ta1");
  $style->setAttribute("style:family","table");
  $style->setAttribute("style:master-page-name","Default");
  $substyle = $output->createElement('style:table-properties');
  $substyle->setAttribute("table:display","true");
  $substyle->setAttribute("style:writing-mode","lr-tb");
  $style->appendChild($substyle);
  $styles->appendChild($style);
  //Cell styles
  $styles->appendChild(createCellStyle("ce1", "wrap", null, null, null, "middle", null, null, "#ffffff", null, null, null, null));
  $styles->appendChild(createCellStyle("ce2", "wrap", "#2323dc", null, null, "middle", null, null, "#ffffff", "bold", true, null, null));
  $styles->appendChild(createCellStyle("ce3", "wrap", "#99ccff", null, null, "middle", null, null, null, null, true, null, null));
  $styles->appendChild(createCellStyle("ce4","wrap","#ccffff", null, null,"middle", null, null, null, null, true, null, null));
  $styles->appendChild(createCellStyle("ce5", null, "#2323dc", "fix", "false", "middle", "center", "0cm", "#ffffff", "bold", true, null, null));
  $styles->appendChild(createCellStyle("ce6", null, "#99ccff", "fix", "false", "middle", "center", "0cm", null, null, true, null, null));
  $styles->appendChild(createCellStyle("ce7", null, "#ccffff", "fix", "false", "middle", "center", "0cm", null, null, true, null, null));
  $styles->appendChild(createCellStyle("ce8", "wrap", null, "fix", "false", "middle", null, null, null, null, true, null, null));
  $styles->appendChild(createCellStyle("ce9", null, null, "fix", "false", "middle", "center", "0cm", null, null, true, null, null));
  $styles->appendChild(createCellStyle("ce10", "wrap", null, "fix", "false", "middle", null, null, null, null, null, null, null));
  $styles->appendChild(createCellStyle("ce11", "wrap", "#000000", null, null, "middle", null, null, "#ffffff", "bold", true, null, null));
  $styles->appendChild(createCellStyle("ce12", "wrap", "#000000", null, null, "middle", "center", null, "#ffffff", "bold", true, null, null));
  $styles->appendChild(createCellStyle("ce13", null, null, null, null, "middle", null, null, "#000000", "bold", null, null, null));
  $styles->appendChild(createCellStyle("ce14", null, null, null, null, "middle", null, null, "#000000", "bold", null, 12, null));
  $styles->appendChild(createCellStyle("ce15", null, null, null, null, "middle", null, null, "#000000", "bold", null, 14, null));
  $styles->appendChild(createCellStyle("ce16", null, null, null, null, "middle", null, null, "#000000", null, null, null, "italic"));
  $document->appendChild($styles);

  return $document;
}

function createODS() {
  global $msg;
  global $numrow;
  global $odsfile;
  global $graph_formula_module;
  global $temp;
  global $output;
  global $input;
  global $table0;
  global $table1;
  global $db_host;
  global $db_user;
  global $db_pwd;
  global $db_db;
  global $ids;

  $output = new DOMDocument();

  //Init document
  $document = initDocument();
  $body = $output->createElement('office:body');
  $spreadsheet = $output->createElement('office:spreadsheet');

  //Validator for weight values
  $spreadsheet->appendChild(createValidator());

  $IdDB = mysql_connect($db_host ,$db_user, $db_pwd);
  mysql_select_db($db_db);
  $query = "SELECT file FROM evaluations WHERE id = \"$ids[0]\"";
  $IdReq = mysql_query($query, $IdDB);
  $files = mysql_fetch_row($IdReq);
  $file = $files[0];

  //Synthesis Sheet
  $input = new QSOSDocument("$file");
  initSynthesisSheet();
  $numrow = 7; //Reinit row counter
  createTreeSynthesis($input->getTree(), $table0, 0);
  $spreadsheet->appendChild($table0);

  //Criteria Sheet
  initCriteriaSheet();
  createTreeCriteria($input->getTree(), $table0, 0);
  $spreadsheet->appendChild($table0);

  //Evaluation Sheets
  foreach($ids as $id) {
    $query = "SELECT file FROM evaluations WHERE id = \"$id\"";
    $IdReq = mysql_query($query, $IdDB);
    $files = mysql_fetch_row($IdReq);
    $file = $files[0];

    $input = new QSOSDocument("$file");
    initEvaluationSheet(getTableName($id));
    $numrow = 14; //Reinit row counter
    createTreeEval($input->getTree(), $table1, 0);
    $spreadsheet->appendChild($table1);
  }

  //Finalize Document (in memory)
  $body->appendChild($spreadsheet);
  $document->appendChild($body);
  $output->appendChild($document);

  //Finalize Document (on disk)
  $tempdir = $temp.uniqid();
  mkdir($tempdir, 0755);
  $output->save("$tempdir/content.xml");

  copy("template.zip", "ods/$odsfile");

  $oofile = new PclZip("ods/$odsfile");
  $v_list = $oofile->add("$tempdir/content.xml", PCLZIP_OPT_REMOVE_PATH, $tempdir);
  if ($v_list == 0) {
    die("Error 01: ODS generation");
  }
}

//Uncomment to manage a cache
//if (!(file_exists("ods/$odsfile"))) {
  createODS();
//}

//Return ODS file to the browser
header("Location: ods/$odsfile");
exit;
?>