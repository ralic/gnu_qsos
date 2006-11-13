<?php
header("Content-type: image/svg+xml");
include('QSOSDocument.php');

$criterion = $_GET['c'];

$SCALE = 100; //1 QSOS unit in pixels
$FONT_SIZE = 14; //$SCALE/10;
$myDoc = new QSOSDocument('mysql-5.0.qsos');
$doc = new DOMDocument('1.0');

//draw "n" equidistant axis
function drawAxis($n) {
	global $SCALE;
	drawCircle(0.5*$SCALE);
	drawCircle($SCALE);
	drawCircle(1.5*$SCALE);
	drawCircle(2*$SCALE);
	
	for ($i=1; $i < $n+1; $i++) {
		drawSingleAxis(2*$i*pi()/$n);
	}
}

//draw a single axis at "angle" (in radians) from angle 0	
function drawSingleAxis($angle) {
	global $SCALE;
	$x2 = 2*$SCALE*cos($angle);
	$y2 = 2*$SCALE*sin($angle);
	drawLine(0, 0, $x2, $y2);
}

//draw a circle of "r" radius
function drawCircle($r) {
	global $doc;
	global $g;
	$circle = $doc->createElement("circle");
	$circle->setAttribute("cx", 0);
	$circle->setAttribute("cy", 0);
	$circle->setAttribute("r", $r);
	$circle->setAttribute("fill", "none");
	$circle->setAttribute("stroke", "blue");
	$circle->setAttribute("stroke-width", "1");
	$g->appendChild($circle);
}

//draw a line between two points
function drawLine($x1, $y1, $x2, $y2) {
	global $doc;
	global $g;
	$line = $doc->createElement("line");
	$line->setAttribute("x1", $x1);
	$line->setAttribute("y1", $y1);
	$line->setAttribute("x2", $x2);
	$line->setAttribute("y2", $y2);
	$line->setAttribute("stroke", "green");
	$line->setAttribute("stroke-width", "1");
	$g->appendChild($line);
}

//draw an axis legend
//x, y: coordinates
//element : element which title is to be displayed
function drawText($x, $y, $element) {
	global $doc;
	global $g;
	global $FONT_SIZE;
	$text = $doc->createElement("text");
	$text->setAttribute("x", $x);
	$text->setAttribute("y", $y);
	$text->setAttribute("font-family", "Verdana");
	$text->setAttribute("font-size", $FONT_SIZE);

	$text->setAttribute("fill", "green");
	$text->appendChild($doc->createTextNode($element->title));
	
	if ($element->children) {
		$text->setAttribute("fill", "green");
		$a = $doc->createElement("a");
		$a->setAttribute("xlink:href", 'SVGRadar.php?c='.$element->name);
		$a->appendChild($text);
		$g->appendChild($a);
	} else {
		$text->setAttribute("fill", "black");
		$g->appendChild($text);
	}
	
	//text position is ajusted to be outside the circle shape
	//8 here is empiric data :)
	$textLength = strlen($element->title)*8;
	$myX = (abs($x)==$x)?$x:$x-$textLength;
	$myY = (abs($y)==$y)?$y+$FONT_SIZE:$y;
	$text->setAttribute("x", $myX);
	$text->setAttribute("y", $myY);
}

//Text
$svg = $doc->createElement('svg');
$svg->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
$svg->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
$svg->setAttribute('width', '100%');
$svg->setAttribute('height', '100%');

//Graph element
$g = $doc->createElement('g');
$g->setAttribute('transform', 'translate(500,300)');

//draw path between points on each axis
$path = $doc->createElement("path");
$myD = "";

if (isset($criterion)) {
	$tree = $myDoc->getSubTree($criterion);
} else {
	$tree = $myDoc->getTree();
}

drawAxis(count($tree));
for ($i=0; $i < count($tree); $i++) {
	$myD .= ($i==0)?"M":"L";
	$angle = ($i+1)*2*pi()/(count($tree));
	$myD .= " " . ($tree[$i]->score)*$SCALE*cos($angle) . " " . ($tree[$i]->score)*$SCALE*sin($angle) . " ";
	//2.1 = 2 + 0.1 of padding before actual text display
	drawText(2.1*$SCALE*cos($angle), 2.1*$SCALE*sin($angle), $tree[$i]);
}
$myD .= "z";
$path->setAttribute("d", $myD);
$path->setAttribute("fill", "none");
$path->setAttribute("stroke", "red");
$path->setAttribute("stroke-width", "2");
$g->appendChild($path);

$svg->appendChild($g);
$doc->appendChild($svg);

echo $doc->saveXML();
?>