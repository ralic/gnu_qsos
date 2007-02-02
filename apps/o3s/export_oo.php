<?php
/*
**  Copyright (C) 2007 Atos Origin 
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
** export_oo.php: OpenDocument export
**
*/

session_start();

include("config.php");
include("libs/QSOSDocument.php");
include('libs/pclzip.lib.php');

//XML file to be exported
$file = $_GET['f'];
//Name of the ODS file
$odsfile = basename($file, ".qsos").".ods";

//Global variables
$numrow;
$graph_formula_module;

function showtree($output, $input, $tree, $table0, $table1, $depth) {
	global $numrow;;
	global $graph_formula_module;
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

	$graph_formula_module1 = '';
	$graph_formula_module2 = '';

	foreach($tree as $element) {
		$name = $element->name;
		$title = $element->title;
		$subtree = $element->children;
		$comment = $input->getgeneric($name, "comment");

		$numrow++;
		array_push($children, $numrow);

		if ($depth == '0') {
			$graph_formula_module1 .= "\$Evaluation.\$A\$$numrow;";
			$graph_formula_module2 .= "\$Evaluation.\$C\$$numrow;";
		}

		//New row for first sheet
		$row = $output->createElement('table:table-row');
		$row->setAttribute("table:style-name",$style_row);
		//Criterion
		$cell = $output->createElement('table:table-cell');
		if ($style_title != "") $cell->setAttribute("table:style-name",$style_title);
		$cell->setAttribute("office:value-type","string");
		$text = $output->createElement('text:p',$title);
		$cell->appendChild($text);
		$row->appendChild($cell);
		//Desc0
		$cell = $output->createElement('table:table-cell');
		if ($style_title != "") $cell->setAttribute("table:style-name",$style_title);
		$cell->setAttribute("office:value-type","string");
		$text = $output->createElement('text:p',$input->getgeneric($name, "desc0"));
		$cell->appendChild($text);
		$row->appendChild($cell);
		//Desc1
		$score = $output->createElement('table:table-cell');
		if ($style_title != "") $score->setAttribute("table:style-name",$style_title);
		$score->setAttribute("office:value-type","string");
		$text = $output->createElement('text:p',$input->getgeneric($name, "desc1"));
		$score->appendChild($text);
		$row->appendChild($score);
		//Desc2
		$cell = $output->createElement('table:table-cell');
		if ($style_title != "") $cell->setAttribute("table:style-name",$style_title);
		$cell->setAttribute("office:value-type","string");
		$text = $output->createElement('text:p',$input->getgeneric($name, "desc2"));
		$cell->appendChild($text);
		$row->appendChild($cell);

		$table0->appendChild($row);

		//New row for second sheet
		$row = $output->createElement('table:table-row');
		$row->setAttribute("table:style-name",$style_row);
		//Criterion
		$cell = $output->createElement('table:table-cell');
		if ($style_title != "") $cell->setAttribute("table:style-name",$style_title);
		$cell->setAttribute("office:value-type","string");
		$text = $output->createElement('text:p',$title);
		$cell->appendChild($text);
		$row->appendChild($cell);
		//Comment
		$cell = $output->createElement('table:table-cell');
		if ($style_comment != "") $cell->setAttribute("table:style-name",$style_comment);
		$cell->setAttribute("office:value-type","string");
		$text = $output->createElement('text:p',$comment);
		$cell->appendChild($text);
		$row->appendChild($cell);
		//Score
		$score = $output->createElement('table:table-cell');
		if ($style_score != "") $score->setAttribute("table:style-name",$style_score);
		$score->setAttribute("office:value-type","float");
		$score->setAttribute("office:value",$element->score);
		$text = $output->createElement('text:p',$element->score);
		$score->appendChild($text);
		$row->appendChild($score);
		//Weight
		$cell = $output->createElement('table:table-cell');
		if ($style_weight != "") $cell->setAttribute("table:style-name",$style_weight);
		$cell->setAttribute("office:value-type","float");
		$cell->setAttribute("office:value",$_SESSION[$name]);
		$text = $output->createElement('text:p',$_SESSION[$name]);
		$cell->appendChild($text);
		$row->appendChild($cell);

		$table1->appendChild($row);

		if ($subtree) {
			//Subcriteria regrouping
			$group0 = $output->createElement('table:table-row-group');
			$group = $output->createElement('table:table-row-group');
			$return = showtree($output, $input, $subtree, $group0, $group, $new_depth);
			//Set score formula
			$score->setAttribute("table:formula",getFormula($return));
			$table0->appendChild($group0);
			$table1->appendChild($group);
		}
	}

	if ($depth == 0) {
		$graph_formula_module = $graph_formula_module1.$graph_formula_module2;
		return $children;
	} else {
		return $children;
	}
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
	return "oooc:=($quotient)/($dividend)";
}

function createODS($file) {
	global $numrow;
	global $odsfile;
	global $graph_formula_module;
	global $temp;
	$input = new QSOSDocument("$file");
	$output = new DOMDocument();
	
	//Document element
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
	
	$scripts = $output->createElement('office:scripts');
	$listeners = $output->createElement('office:event-listeners');
	$listener = $output->createElement('script:event-listener');
	$listener->setAttribute("script:language","ooo:script");
	$listener->setAttribute("script:event-name","dom:load");
	$listener->setAttribute("xlink:href","vnd.sun.star.script:Standard.Module1.Graph?language=Basic&location=document");
	$listeners->appendChild($listener);
	$scripts->appendChild($listeners);
	$document->appendChild($scripts);
	
	//Font declaration
	$fontfaces = $output->createElement('office:font-face-decls');
	
	$font = $output->createElement('style:font-face');
	$font->setAttribute("style:name","Lucida Sans Unicode");
	$font->setAttribute("svg:font-family","'Lucida Sans Unicode'");
	$font->setAttribute("style:font-pitch","variable");
	$fontfaces->appendChild($font);
	
	$font = $output->createElement('style:font-face');
	$font->setAttribute("style:name","Tahoma");
	$font->setAttribute("svg:font-family","Tahoma");
	$font->setAttribute("style:font-pitch","variable");
	$fontfaces->appendChild($font);
	
	$font = $output->createElement('style:font-face');
	$font->setAttribute("style:name","Arial");
	$font->setAttribute("svg:font-family","Arial");
	$font->setAttribute("style:font-pitch","variable");
	$fontfaces->appendChild($font);
	
	$font = $output->createElement('style:font-face');
	$font->setAttribute("style:name","Arial");
	$font->setAttribute("svg:font-family","Arial");
	$font->setAttribute("style:font-family-generic","swiss");
	$font->setAttribute("style:font-pitch","variable");
	$fontfaces->appendChild($font);
	
	$document->appendChild($fontfaces);
	
	//Styles
	$styles = $output->createElement('office:automatic-styles');

	//co0
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","co0");
	$style->setAttribute("style:family","table-column");
	$substyle = $output->createElement('style:table-column-properties');
	$substyle->setAttribute("fo:break-before","auto");
	$substyle->setAttribute("style:column-width","4.717cm");
	$style->appendChild($substyle);
	$styles->appendChild($style);

	//co1
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","co1");
	$style->setAttribute("style:family","table-column");
	$substyle = $output->createElement('style:table-column-properties');
	$substyle->setAttribute("fo:break-before","auto");
	$substyle->setAttribute("style:column-width","5.117cm");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//co2
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","co2");
	$style->setAttribute("style:family","table-column");
	$substyle = $output->createElement('style:table-column-properties');
	$substyle->setAttribute("fo:break-before","auto");
	$substyle->setAttribute("style:column-width","10.931cm");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//co3
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","co3");
	$style->setAttribute("style:family","table-column");
	$substyle = $output->createElement('style:table-column-properties');
	$substyle->setAttribute("fo:break-before","auto");
	$substyle->setAttribute("style:column-width","1.452cm");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//co4
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","co4");
	$style->setAttribute("style:family","table-column");
	$substyle = $output->createElement('style:table-column-properties');
	$substyle->setAttribute("fo:break-before","auto");
	$substyle->setAttribute("style:column-width","1.452cm");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//ro1
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ro1");
	$style->setAttribute("style:family","table-row");
	$substyle = $output->createElement('style:table-row-properties');
	$substyle->setAttribute("style:row-height","0.453cm");
	$substyle->setAttribute("fo:break-before","auto");
	$substyle->setAttribute("style:use-optimal-row-height","true");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//ro2
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ro2");
	$style->setAttribute("style:family","table-row");
	$substyle = $output->createElement('style:table-row-properties');
	$substyle->setAttribute("style:row-height","0.453cm");
	$substyle->setAttribute("fo:break-before","auto");
	$substyle->setAttribute("style:use-optimal-row-height","true");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
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
	
	//ce1
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ce1");
	$style->setAttribute("style:family","table-cell");
	$style->setAttribute("style:parent-style-name","Default");
	$substyle = $output->createElement('style:table-cell-properties');
	$substyle->setAttribute("fo:wrap-option","wrap");
	$substyle->setAttribute("style:vertical-align","middle");
	$style->appendChild($substyle);
	$substyle = $output->createElement('style:text-properties');
	$substyle->setAttribute("fo:color","#ffffff");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//ce2
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ce2");
	$style->setAttribute("style:family","table-cell");
	$style->setAttribute("style:parent-style-name","Default");
	$substyle = $output->createElement('style:table-cell-properties');
	$substyle->setAttribute("fo:background-color","#2323dc");
	$substyle->setAttribute("fo:wrap-option","wrap");
	$substyle->setAttribute("style:vertical-align","middle");
	$style->appendChild($substyle);
	$substyle = $output->createElement('style:text-properties');
	$substyle->setAttribute("fo:color","#ffffff");
	$substyle->setAttribute("fo:font-weight","bold");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//ce3
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ce3");
	$style->setAttribute("style:family","table-cell");
	$style->setAttribute("style:parent-style-name","Default");
	$substyle = $output->createElement('style:table-cell-properties');
	$substyle->setAttribute("fo:wrap-option","wrap");
	$substyle->setAttribute("style:vertical-align","middle");
	$substyle->setAttribute("fo:background-color","#99ccff");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//ce4
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ce4");
	$style->setAttribute("style:family","table-cell");
	$style->setAttribute("style:parent-style-name","Default");
	$substyle = $output->createElement('style:table-cell-properties');
	$substyle->setAttribute("fo:wrap-option","wrap");
	$substyle->setAttribute("style:vertical-align","middle");
	$substyle->setAttribute("fo:background-color","#ccffff");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//ce5
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ce5");
	$style->setAttribute("style:family","table-cell");
	$style->setAttribute("style:parent-style-name","Default");
	$substyle = $output->createElement('style:table-cell-properties');
	$substyle->setAttribute("fo:background-color","#2323dc");
	$substyle->setAttribute("style:text-align-source","fix");
	$substyle->setAttribute("style:repeat-content","false");
	$substyle->setAttribute("style:vertical-align","middle");
	$style->appendChild($substyle);
	$substyle = $output->createElement('style:paragraph-properties');
	$substyle->setAttribute("fo:text-align","center");
	$substyle->setAttribute("fo:margin-left","0cm");
	$style->appendChild($substyle);
	$substyle = $output->createElement('style:text-properties');
	$substyle->setAttribute("fo:color","#ffffff");
	$substyle->setAttribute("fo:font-weight","bold");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//ce6
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ce6");
	$style->setAttribute("style:family","table-cell");
	$style->setAttribute("style:parent-style-name","Default");
	$substyle = $output->createElement('style:table-cell-properties');
	$substyle->setAttribute("fo:background-color","#99ccff");
	$substyle->setAttribute("style:text-align-source","fix");
	$substyle->setAttribute("style:repeat-content","false");
	$substyle->setAttribute("style:vertical-align","middle");
	$style->appendChild($substyle);
	$substyle = $output->createElement('style:paragraph-properties');
	$substyle->setAttribute("fo:text-align","center");
	$substyle->setAttribute("fo:margin-left","0cm");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//ce7
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ce7");
	$style->setAttribute("style:family","table-cell");
	$style->setAttribute("style:parent-style-name","Default");
	$substyle = $output->createElement('style:table-cell-properties');
	$substyle->setAttribute("fo:background-color","#ccffff");
	$substyle->setAttribute("style:text-align-source","fix");
	$substyle->setAttribute("style:repeat-content","false");
	$substyle->setAttribute("style:vertical-align","middle");
	$style->appendChild($substyle);
	$substyle = $output->createElement('style:paragraph-properties');
	$substyle->setAttribute("fo:text-align","center");
	$substyle->setAttribute("fo:margin-left","0cm");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//ce8
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ce8");
	$style->setAttribute("style:family","table-cell");
	$style->setAttribute("style:parent-style-name","Default");
	$substyle = $output->createElement('style:table-cell-properties');
	$substyle->setAttribute("style:text-align-source","fix");
	$substyle->setAttribute("style:repeat-content","false");
	$substyle->setAttribute("fo:wrap-option","wrap");
	$substyle->setAttribute("style:vertical-align","middle");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	//ce9
	$style = $output->createElement('style:style');
	$style->setAttribute("style:name","ce9");
	$style->setAttribute("style:family","table-cell");
	$style->setAttribute("style:parent-style-name","Default");
	$substyle = $output->createElement('style:table-cell-properties');
	$substyle->setAttribute("style:text-align-source","fix");
	$substyle->setAttribute("style:repeat-content","false");
	$substyle->setAttribute("style:vertical-align","middle");
	$style->appendChild($substyle);
	$substyle = $output->createElement('style:paragraph-properties');
	$substyle->setAttribute("fo:text-align","center");
	$substyle->setAttribute("fo:margin-left","0cm");
	$style->appendChild($substyle);
	$styles->appendChild($style);
	
	$document->appendChild($styles);
	
	//Document body
	$body = $output->createElement('office:body');
	$spreadsheet = $output->createElement('office:spreadsheet');

	//First sheet (Criteria)
	$table0 = $output->createElement('table:table');
	$table0->setAttribute("table:name","Criteria");
	$table0->setAttribute("table:style-name","ta1");
	$table0->setAttribute("table:print","false");

	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co0");
	$column->setAttribute("table:default-cell-style-name","ce4");
	$table0->appendChild($column);
	
	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co0");
	$column->setAttribute("table:default-cell-style-name","ce4");
	$table0->appendChild($column);

	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co0");
	$column->setAttribute("table:default-cell-style-name","ce4");
	$table0->appendChild($column);

	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co0");
	$column->setAttribute("table:default-cell-style-name","ce4");
	$table0->appendChild($column);

	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$row->setAttribute("table:number-rows-repeated","2");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","Default");
	$cell->setAttribute("table:number-columns-repeated","4");
	$row->appendChild($cell);
	$table0->appendChild($row);

	//Software family
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"Software family");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("qsosappfamily"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table0->appendChild($row);

	//QSOS version
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"QSOS version");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("qsosformat"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table0->appendChild($row);

	//Template version
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"Template version");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("qsosspecificformat"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table0->appendChild($row);

	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$row->setAttribute("table:number-rows-repeated","2");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","Default");
	$cell->setAttribute("table:number-columns-repeated","4");
	$row->appendChild($cell);
	$table0->appendChild($row);

	//Criteria
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p','Criterion');
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p','Score 0');
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p','Score 1');
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p','Score 2');
	$cell->appendChild($text);
	$row->appendChild($cell);
	$table0->appendChild($row);

	//Second sheet (Evaluation)
	$table1 = $output->createElement('table:table');
	$table1->setAttribute("table:name","Evaluation");
	$table1->setAttribute("table:style-name","ta1");
	$table1->setAttribute("table:print","false");
	
	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co1");
	$column->setAttribute("table:default-cell-style-name","ce4");
	$table1->appendChild($column);
	
	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co2");
	$column->setAttribute("table:default-cell-style-name","ce4");
	$table1->appendChild($column);
	
	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co3");
	$column->setAttribute("table:default-cell-style-name","ce7");
	$table1->appendChild($column);
	
	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co4");
	$column->setAttribute("table:default-cell-style-name","ce7");
	$table1->appendChild($column);
	
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$row->setAttribute("table:number-rows-repeated","2");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","Default");
	$cell->setAttribute("table:number-columns-repeated","4");
	$row->appendChild($cell);
	$table1->appendChild($row);

	//Header
	//Application
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"Application");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("appname"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table1->appendChild($row);

	//Release
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"Release");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("release"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table1->appendChild($row);

	//Software family
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"Software family");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("qsosappfamily"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table1->appendChild($row);

	//License
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"License");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("licensedesc"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table1->appendChild($row);

	//Url
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"Website");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("url"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table1->appendChild($row);

	//Description
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"Description");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("desc"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table1->appendChild($row);

	//Authors
	$authors = $input->getauthors();
	$list = "";
	for ($i=0; $i < count($authors); $i++) {
		if ($i != 0) {
			$list .= ", ";
		}
		$list .= $authors[$i]->name." (".$authors[$i]->email.")";
	}
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"Authors");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$list);
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table1->appendChild($row);

	//Creation date
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"Creation date");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("creation"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table1->appendChild($row);

	//Validation date
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',"Validation date");
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p',$input->getkey("validation"));
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce8");
	$cell->setAttribute("table:number-columns-repeated","2");
	$row->appendChild($cell);
	$table1->appendChild($row);

	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$row->setAttribute("table:number-rows-repeated","2");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","Default");
	$cell->setAttribute("table:number-columns-repeated","4");
	$row->appendChild($cell);
	$table1->appendChild($row);

	//Criteria
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p','Criterion');
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce2");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p','Comment');
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce5");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p','Score');
	$cell->appendChild($text);
	$row->appendChild($cell);
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","ce5");
	$cell->setAttribute("office:value-type","string");
	$text = $output->createElement('text:p','Weight');
	$cell->appendChild($text);
	$row->appendChild($cell);
	$table1->appendChild($row);
	
	//Init row counter
	$numrow = 14;
	
	//Init loop
	showtree($output, $input, $input->getTree(), $table0, $table1, 0);
	
	//Third sheet (Graph)
	$table2 = $output->createElement('table:table');
	$table2->setAttribute("table:name","Graph");
	$table2->setAttribute("table:style-name","ta1");
	$table2->setAttribute("table:print","false");

	$forms = $output->createElement('office:forms');
	$forms->setAttribute("form:automatic-focus","false");
	$forms->setAttribute("form:apply-design-mode","false");
	$table2->appendChild($forms);

	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co2");
	$column->setAttribute("table:default-cell-style-name","Default");
	$table2->appendChild($column);

	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$row->setAttribute("table:number-rows-repeated","2");
	$cell = $output->createElement('table:table-cell');
	$row->appendChild($cell);
	$table2->appendChild($row);

	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$cell = $output->createElement('table:table-cell');
	$frame = $output->createElement('draw:frame');
	$frame->setAttribute("table:end-cell-address","Graph.I32");
	$frame->setAttribute("table:end-x","0.449cm");
	$frame->setAttribute("table:end-y","0.104cm");
	$frame->setAttribute("draw:z-index","0");
	$frame->setAttribute("svg:width","17.669cm");
	$frame->setAttribute("svg:height","12.024cm");
	$frame->setAttribute("svg:x","0.844cm");
	$frame->setAttribute("svg:y","0.412cm");
	$object = $output->createElement('draw:object');
	$object->setAttribute("draw:notify-on-update-of-ranges","Criteria.A1:Criteria.A1");
	$object->setAttribute("xlink:href","./Object 1");
	$object->setAttribute("xlink:type","simple");
	$object->setAttribute("xlink:show","embed");
	$object->setAttribute("xlink:actuate","onLoad");
	$frame->appendChild($object);
	$image = $output->createElement('draw:image');
	$image->setAttribute("xlink:href","./ObjectReplacements/Object 1");
	$image->setAttribute("xlink:type","simple");
	$image->setAttribute("xlink:show","embed");
	$image->setAttribute("xlink:actuate","onLoad");
	$frame->appendChild($image);
	$cell->appendChild($frame);
	$row->appendChild($cell);
	$table2->appendChild($row);

	$spreadsheet->appendChild($table0);
	$spreadsheet->appendChild($table1);
	$spreadsheet->appendChild($table2);
	$body->appendChild($spreadsheet);
	$document->appendChild($body);
	$output->appendChild($document);
	
	$tempdir = $temp.uniqid();
	mkdir($tempdir, 0755);
	$output->save("$tempdir/content.xml");

	//Macro definition
	$output = new DOMDocument();
	$document = $output->createElement('script:module');
	$document->setAttribute("xmlns:script","http://openoffice.org/2000/script");
	$document->setAttribute("script:name","Module1");
	$document->setAttribute("script:language","StarBasic");

	$macro = "\n \nSub Main\n \n \nEnd Sub\n \n";
	$macro .= "sub Graph\n \n";
	$macro .= "dim document   as object\n";
	$macro .= "dim dispatcher as object\n \n";	
	$macro .= "document   = ThisComponent.CurrentController.Frame\n";
	$macro .= "dispatcher = createUnoService(\"com.sun.star.frame.DispatchHelper\")\n \n";
	$macro .= "dim args2(3) as new com.sun.star.beans.PropertyValue\n";
	$macro .= "args2(0).Name = \"Name\"\n";
	$macro .= "args2(0).Value = \"Object 1\"\n";
	$macro .= "args2(1).Name = \"Range\"\n";
	//$macro .= "args2(1).Value = \"\$Evaluation.\$A\$15;\$Evaluation.\$A\$74;\$Evaluation.\$A\$102;\$Evaluation.\$A\$108;\$Evaluation.\$C\$15;\$Evaluation.\$C\$74;\$Evaluation.\$C\$102;\$Evaluation.\$C\$108\"\n";
	$macro .= "args2(1).Value = \"$graph_formula_module\"\n";
	$macro .= "args2(2).Name = \"ColHeaders\"\n";
	$macro .= "args2(2).Value = false\n";
	$macro .= "args2(3).Name = \"RowHeaders\"\n";
	$macro .= "args2(3).Value = true\n \n";
	$macro .= "dispatcher.executeDispatch(document,\".uno:ChangeChartData\", \"\", 0, args2())\n \n";
	$macro .= "end sub";

	$document->appendChild($output->createTextNode($macro));
	$output->appendChild($document);

	$output->save("$tempdir/Module1.xml");
	
	copy("template.zip", "ods/$odsfile");
	
	$oofile = new PclZip("ods/$odsfile");
	$v_list = $oofile->add("$tempdir/content.xml", PCLZIP_OPT_REMOVE_PATH, $tempdir);
	if ($v_list == 0) {
		die("Error 01: ODS generation");
	}
	$v_list = $oofile->add("$tempdir/Module1.xml", PCLZIP_OPT_REMOVE_PATH, $tempdir,
                          PCLZIP_OPT_ADD_PATH, 'Basic/Standard');
	if ($v_list == 0) {
		die("Error 02: ODS generation");
	}
}

//Uncomment to manage a cache
//if (!(file_exists("ods/$odsfile"))) {
	createODS($file);
//}

//Return ODS file to the browser
header("Location: ods/$odsfile");
exit;

?>