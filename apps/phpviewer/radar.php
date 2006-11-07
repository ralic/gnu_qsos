<?php
include("config.php");
include ("$jpgraph_path/jpgraph.php");
include ("$jpgraph_path/jpgraph_radar.php");
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
	$plot = new RadarPlot($scores);
	$plot->SetLegend($myDoc->getkey("appname")." ".$myDoc->getkey("release"));
	$plot->SetColor(getColor()."@0.2");
	$plot->SetLineWeight(3);
	return $plot;
}

//Add them to the global graph
for($i=0; $i<$num; $i++) {
	$graph->Add(getPlot($scores[$i], $myDoc[$i]));
}

//Output the graph
$graph->Stroke();

?> 