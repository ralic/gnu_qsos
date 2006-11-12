<?php
include("QSOSDocument.php");
require_once('pclzip.lib.php');

$file = $_GET['f'];
$odsfile = basename($file, ".qsos").".ods";

//Global variable
$numrow;

//loop
function showtree($output, $input, $tree, $table, $depth) {
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

		//New row
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
		$cell->setAttribute("office:value","1");
		$text = $output->createElement('text:p',"1");
		$cell->appendChild($text);
		$row->appendChild($cell);

		$table->appendChild($row);

		if ($subtree) {
			//Subcriteria regrouping
			$group = $output->createElement('table:table-row-group');
			$return = showtree($output, $input, $subtree, $group, $new_depth);
			//Set score formula
			$score->setAttribute("table:formula",getFormula($return));
			$table->appendChild($group);
		}
	}

	if ($depth == 0) {
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
	$document->setAttribute("xmlns:scrip","urn:oasis:names:tc:opendocument:xmlns:script:1.0");
	$document->setAttribute("xmlns:ooo","http://openoffice.org/2004/office");
	$document->setAttribute("xmlns:ooow","http://openoffice.org/2004/writer");
	$document->setAttribute("xmlns:oooc","http://openoffice.org/2004/calc");
	$document->setAttribute("xmlns:dom","http://www.w3.org/2001/xml-events");
	$document->setAttribute("xmlns:xforms","http://www.w3.org/2002/xforms");
	$document->setAttribute("xmlns:xsd","http://www.w3.org/2001/XMLSchema");
	$document->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
	$document->setAttribute("office:version","1.0");
	
	$document->appendChild($output->createElement('office:scripts'));
	
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
	
	//First sheet
	$table = $output->createElement('table:table');
	$table->setAttribute("table:name","Evaluation");
	$table->setAttribute("table:style-name","ta1");
	$table->setAttribute("table:print","false");
	
	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co1");
	$column->setAttribute("table:default-cell-style-name","ce4");
	$table->appendChild($column);
	
	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co2");
	$column->setAttribute("table:default-cell-style-name","ce4");
	$table->appendChild($column);
	
	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co3");
	$column->setAttribute("table:default-cell-style-name","ce7");
	$table->appendChild($column);
	
	$column = $output->createElement('table:table-column');
	$column->setAttribute("table:style-name","co4");
	$column->setAttribute("table:default-cell-style-name","ce7");
	$table->appendChild($column);
	
	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$row->setAttribute("table:number-rows-repeated","2");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","Default");
	$cell->setAttribute("table:number-columns-repeated","4");
	$row->appendChild($cell);
	$table->appendChild($row);

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
	$table->appendChild($row);

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
	$table->appendChild($row);

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
	$table->appendChild($row);

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
	$table->appendChild($row);

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
	$table->appendChild($row);

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
	$table->appendChild($row);

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
	$table->appendChild($row);

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
	$table->appendChild($row);

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
	$table->appendChild($row);

	$row = $output->createElement('table:table-row');
	$row->setAttribute("table:style-name","ro1");
	$row->setAttribute("table:number-rows-repeated","2");
	$cell = $output->createElement('table:table-cell');
	$cell->setAttribute("table:style-name","Default");
	$cell->setAttribute("table:number-columns-repeated","4");
	$row->appendChild($cell);
	$table->appendChild($row);

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
	$table->appendChild($row);
	
	//Init row counter
	$numrow = 14;
	
	//Init loop
	showtree($output, $input, $input->getTree(), $table, 0);
	
	$spreadsheet->appendChild($table);
	$body->appendChild($spreadsheet);
	$document->appendChild($body);
	$output->appendChild($document);
	
	$tempdir = "/tmp/".uniqid();
	mkdir($tempdir, 0770);
	$output->save("$tempdir/content.xml");
	
	copy("template.zip", "ods/$odsfile");
	
	$oofile = new PclZip("ods/$odsfile");
	$v_list = $oofile->add("$tempdir/content.xml", PCLZIP_OPT_REMOVE_PATH, $tempdir);
	if ($v_list == 0) {
		die("Error interne");
	}
}

if (!(file_exists("ods/$odsfile"))) {
	createODS($file);
}

header("Location: ods/$odsfile");
exit;

?>