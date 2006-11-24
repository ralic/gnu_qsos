<?php

$svg = $_GET['svg'];

if(isset($svg) && ($svg == "yes")) {
//Graph generated in SVG format
	header("Content-type: image/svg+xml");
	include('QSOSDocument.php');
	
	$files = $_GET['f']; //QSOS files to display
	$name = $_GET['c']; //Criterion to detail
	
	if (!(isset($files))) {
		die("No QSOS file provided !");
	}
	
	$SCALE = 100; //1 QSOS unit in pixels
	$FONT_SIZE = 14; //$SCALE/10;
	$doc = new DOMDocument('1.0');
	
	$myDoc = array();
	$num = count($files);
	
	//Initialization of data arrays
	for($i=0; $i<$num; $i++) {
		$myDoc[$i] = new QSOSDocument($files[$i]);
	}
	
	//draw $n equidistant axis
	function drawAxis($n) {
		global $SCALE;
		drawCircle(0.5*$SCALE);
		drawMark(0.5*$SCALE-25, 15, "0.5");
		drawCircle($SCALE);
		drawMark($SCALE-15, 15, "1");
		drawCircle(1.5*$SCALE);
		drawMark(1.5*$SCALE-25, 15, "1.5");
		drawCircle(2*$SCALE);
		drawMark(2*$SCALE-15, 15, "2");
		
		for ($i=1; $i < $n+1; $i++) {
			drawSingleAxis(2*$i*pi()/$n);
		}
	}
	
	//draw a single axis at $angle (in radians) from angle 0	
	function drawSingleAxis($angle) {
		global $SCALE;
		$x2 = 2*$SCALE*cos($angle);
		$y2 = 2*$SCALE*sin($angle);
		drawLine(0, 0, $x2, $y2);
	}
	
	//draw a circle of $r radius
	function drawCircle($r) {
		global $doc;
		global $g;
		$circle = $doc->createElement("circle");
		$circle->setAttribute("cx", 0);
		$circle->setAttribute("cy", 0);
		$circle->setAttribute("r", $r);
		$circle->setAttribute("fill", "none");
		$circle->setAttribute("stroke", "lightgrey");
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
		$line->setAttribute("stroke", "lightgrey");
		$line->setAttribute("stroke-width", "1");
		$g->appendChild($line);
	}
	
	//draw scale mark on the radar
	//$x, $y: coordinates
	//$mark : text to be displayed
	function drawMark($x, $y, $mark) {
		global $doc;
		global $g;
		global $FONT_SIZE;
		$text = $doc->createElement("text");
		$text->setAttribute("x", $x);
		$text->setAttribute("y", $y);
		$text->setAttribute("font-family", "Verdana");
		$text->setAttribute("font-size", $FONT_SIZE);
	
		$text->setAttribute("fill", "lightgrey");
		$text->appendChild($doc->createTextNode($mark));
		$g->appendChild($text);
	}
	
	//draw an axis legend
	//$x, $y: coordinates
	//$element : element which title is to be displayed
	function drawText($x, $y, $element) {
		global $files;
		global $doc;
		global $g;
		global $FONT_SIZE;
		$text = $doc->createElement("text");
		$text->setAttribute("x", $x);
		$text->setAttribute("y", $y);
		$text->setAttribute("font-family", "Verdana");
		$text->setAttribute("font-size", $FONT_SIZE);
		$text->appendChild($doc->createTextNode($element->title));
		
		if ($element->children) {
			$text->setAttribute("fill", "green");
			$a = $doc->createElement("a");
			$f = "";
			foreach($files as $file) {
				$f .= "f[]=$file&";
			}
			$a->setAttribute("xlink:href", $_SERVER['PHP_SELF']."?".$f."c=".$element->name."&svg=yes");
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
	
	//draw the graph's title including software name and release and navigation tree
	//$name : name of the current criterion
	function drawTitle($name) {
		global $doc;
		global $myDoc;
		global $FONT_SIZE;
		$title = $myDoc[0]->getkeytitle($name);
		$node = $name;
	
		$text = $doc->createElement("text");
		$text->setAttribute("font-family", "Verdana");
		$text->setAttribute("font-weight", "bold");
		$text->setAttribute("font-size", $FONT_SIZE);
		$text->setAttribute("x", -475);
		$text->setAttribute("y", -275);
	
		while ($myDoc[0]->getParent($node)) {
			$title = $myDoc[0]->getParent($node)->getAttribute("title") . " > ". $title;
			$node = $myDoc[0]->getParent($node)->getAttribute("name");
		}
		
		for ($i=0; $i < count($myDoc); $i++) {
			$tspan = $doc->createElement("tspan");
			$tspan->setAttribute("fill", getcolor($i));
			$tspan->appendChild($doc->createTextNode($myDoc[$i]->getkey("appname")." ".$myDoc[$i]->getkey("release")." "));
			$text->appendChild($tspan);
		}
	
		$text->appendChild($doc->createTextNode($title));
	
		return $text;
	}
	
	//draw path between points on each axis
	//$myDoc : QSOSDocument concerned
	//$name : name of the criteria regrouping subcriteria to be displayed
	//	if $name is not set, gobal sectiosn are displayed
	//$n : position of the software to display in the list (used for coloring)
	function drawPath($myDoc, $name, $n) {
		global $doc;
		global $SCALE;
		global $num;
		$path = $doc->createElement("path");
		$myD = "";
		
		if (isset($name)) {
			$tree = $myDoc->getSubTree($name);
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
		$path->setAttribute("fill", getColor($n));
		$path->setAttribute("fill-opacity", "0.2");
		$path->setAttribute("stroke-width", "3");
		$path->setAttribute("stroke", getColor($n));
	
		return $path;
	}
	
	$colors = array('red', 'blue', 'green', 'purple');
	//Return drawing color depending on software position in the list
	function getColor($i) {
		global $colors;
		if($i < count($colors)) {
			return $colors[$i];
		} else {
			return "black";
		}
	}
	
	//svg element
	$svg = $doc->createElement('svg');
	$svg->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
	$svg->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
	$svg->setAttribute('width', '100%');
	$svg->setAttribute('height', '100%');
	
	//Graph element
	$g = $doc->createElement('g');
	$g->setAttribute('transform', 'translate(500,300)');
	$g->appendChild(drawTitle($name));
	//display each software on teh graph
	for($i=0; $i<$num; $i++) {
		$g->appendChild(drawPath($myDoc[$i], $name, $i));
	}
	$svg->appendChild($g);
	$doc->appendChild($svg);
	
	echo $doc->saveXML();
} else {
//Graph generated with jpgraph
	include("config.php");
	include ($jpgraph_path."jpgraph.php");
	include ($jpgraph_path."jpgraph_radar.php");
	include("QSOSDocument.php");
	
	$files = $_GET['f'];
	$name = $_GET['c'];
	
	$myDoc = array();
	$app = array();
	$trees = array();
	$scores = array();
	$titles = array();
	
	$i = 0;
	$num = count($files);
	
	//Initialization of data arrays
	for($i=0; $i<$num; $i++) {
		$myDoc[$i] = new QSOSDocument($files[$i]);
		$trees[$i] = array();
		if (isset($name)) {
			$trees[$i] = $myDoc[$i]->getSubTree($name);
		} else {
			$trees[$i] = $myDoc[$i]->getTree();
		}
	
		$scores[$i] = array();
		foreach($trees[$i] as $element) {
			array_push($scores[$i], $element->score);
		}
	}
	
	//Graph's title
	if (isset($name)) {
		$title = $myDoc[0]->getkeytitle($name);
	} else {
		$title = $myDoc[0]->getkey("qsosappfamily");
	}
	
	//Axis titles
	foreach($trees[0] as $element) {
		array_push($titles, $element->title);
	}
	
	// Create the basic radar graph
	$graph = new RadarGraph(700,500,"auto");
	
	// Set background color and shadow
	$graph->SetColor("white");
	$graph->SetFrame(false,'',0);
	
	// Position the graph
	$graph->SetCenter(0.4,0.55);
	$graph->SetPos(0.5,0.6);
	
	// Setup the axis formatting  
	$graph->SetScale('lin',0,2);
	$graph->axis->SetFont(FF_ARIAL,FS_BOLD);
	$graph->axis->title->SetFont(FF_ARIAL,FS_BOLD);
	$graph->axis->title->SetMargin(5);
	$graph->axis->SetWeight(1);
	$graph->axis->SetColor('darkgray'); 
	
	// Setup the grid lines
	$graph->grid->SetLineStyle("longdashed");
	$graph->grid->SetColor("darkgray");
	$graph->grid->Show();
	$graph->HideTickMarks();
		
	// Setup graph titles
	$graph->title->Set($title);
	$graph->title->SetFont(FF_ARIAL,FS_BOLD,12);
	$graph->SetTitles($titles);
	
	// Setup graph legend
	$graph->legend->SetFont(FF_ARIAL,FS_BOLD);
	
	function getColor($b_safe = TRUE) {
		//if a browser safe color is requested then set the array up
		//so that only a browser safe color can be returned
		if($b_safe) {
			$ary_codes = array('00','33','66','99','CC','FF');
			$max = 5; //the highest array offest
			//if a browser safe color is not requested then set the array
			//up so that any color can be returned.
		} else {
			$ary_codes = array();
			for($i=0;$i<16;$i++) {
				$t_1 = dechex($i);
				for($j=0;$j<16;$j++) {
					$t_2 = dechex($j);
					$ary_codes[] = "$t_1$t_2";
				}
			}
			$max = 256; //the highest array offset
		}
		$retVal = '';
		
		//generate a random color code
		for($i=0;$i<3;$i++) {
			$offset = rand(0,$max);
			$retVal .= $ary_codes[$offset];
		} //end for i
		
		return "#".$retVal;
	}
	
	//Generate graph for each software
	function getPlot($scores, $myDoc) {
		global $num;
		$plot = new RadarPlot($scores);
		$plot->SetLegend($myDoc->getkey("appname")." ".$myDoc->getkey("release"));
		$color = getColor();
		$plot->SetColor("$color@0.2");
		if ($num == 1) $plot->SetFillColor("$color@0.7");
		$plot->SetLineWeight(3);
		return $plot;
	}
	
	//Add them to the global graph
	for($i=0; $i<$num; $i++) {
		$graph->Add(getPlot($scores[$i], $myDoc[$i]));
	}
	
	//Output the graph
	$graph->Stroke();
}

?> 
