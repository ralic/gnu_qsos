<?php
include ("jpgraph-2.1.3/src/jpgraph.php");
include ("jpgraph-2.1.3/src/jpgraph_radar.php");
include("QSOSDocument.php");

$file = $_GET['f'];
$name = $_GET['c'];

// Get QSOS data
$myDoc = new QSOSDocument($file);
if (isset($name)) {
    $tree = $myDoc->getSubTree($name);
    $title = $myDoc->getkeytitle($name);
} else {
    $tree = $myDoc->getTree();
    $title = $myDoc->getkey("qsosappfamily");
}
$titles = array();
$scores = array();
foreach($tree as $element) {
    array_push($titles, $element->title);
    array_push($scores, $element->score);
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
$graph->axis->SetColor("darkgray"); 

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

// Create the first radar plot        
$plot = new RadarPlot($scores);
$plot->SetLegend($myDoc->getkey("appname")." ".$myDoc->getkey("release"));
$plot->SetColor("red@0.2");
$plot->SetFillColor("red@0.7");
$plot->SetLineWeight(3);

// Add the plots to the graph
$graph->Add($plot);

// And output the graph
$graph->Stroke();

?> 