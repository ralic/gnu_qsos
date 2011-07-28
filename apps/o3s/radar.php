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
 *  radar.php: graph generation (in SVG or PNG format)
 *
**/


session_start();

include("config.php");
include("lang.php");

//Is the graph to be generated in SVG?
$svg = $_REQUEST['svg'];
//Weightings are stored in session
$weights = $_SESSION;
//QSOS evaluations to display
$ids = $_REQUEST['id'];
//Criterion to detail
$name = $_REQUEST['c'];

$IdDB = mysql_connect($db_host ,$db_user, $db_pwd);
mysql_select_db($db_db);

$query = "SELECT id FROM evaluations WHERE appname <> '' AND language = '$lang'";
$IdReq = mysql_query($query, $IdDB);
$allIds = array();
while($row = mysql_fetch_row($IdReq)) {
  array_push($allIds, $row[0]);
}

$files = array();
foreach($ids as $id) {
  if (!(in_array($id,$allIds))) die("<error>".$id.$msg['s4_err_no_id']."</error>");
  $query = "SELECT file FROM evaluations WHERE id = \"$id\"";
  $IdReq = mysql_query($query, $IdDB);
  $result = mysql_fetch_row($IdReq);
  array_push($files, $result[0]);
}

$f = "";
foreach($ids as $id) {
  $f .= "id[]=$id&";
}

if(isset($svg) && ($svg == "yes")) {
//Graph generated in SVG format
  header("Content-type: image/svg+xml");
  include('libs/QSOSDocument.php');

  if (!(isset($files))) {
    die("<error>No QSOS evaluation provided!</error>");
  }

  $SCALE = 70; //1 QSOS unit in pixels
  $FONT_SIZE = 12; //$SCALE/10;
  $dx = 500; // X offset
  $dy = 300; // Y offset
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

    //N: should be commented
    for ($i=1; $i < $n+1; $i++) {
      drawSingleAxis(2*$i*pi()/$n);
    }
  }

  //draw a single axis at $angle (in radians) from angle 0
  function drawSingleAxis($angle) {
    global $SCALE, $dx, $dy;
    $x2 = 2*$SCALE*cos($angle) + $dx;
    $y2 = 2*$SCALE*sin($angle) + $dy;
    drawLine($dx, $dy, $x2, $y2);
  }

  //draw a circle of $r radius
  function drawCircle($r) {
    global $doc;
    global $g, $dx, $dy;
    $circle = $doc->createElement("circle");
    $circle->setAttribute("cx", $dx);
    $circle->setAttribute("cy", $dy);
    $circle->setAttribute("r", $r);
    $circle->setAttribute("fill", "none");
    $circle->setAttribute("stroke", "lightgrey");
    $circle->setAttribute("stroke-width", "1");
    $g->appendChild($circle);
  }

  //draw a line between two points
  function drawLine($x1, $y1, $x2, $y2) {
    global $doc;
    global $g, $dx, $dy;
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
    global $g, $dx, $dy;
    global $FONT_SIZE;
    $text = $doc->createElement("text");
    $text->setAttribute("x", $x + $dx);
    $text->setAttribute("y", $y + $dy);
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
    global $f;
    global $doc;
    global $g, $dx, $dy;
    global $FONT_SIZE;
    global $lang;
    $text = $doc->createElement("text");
    $text->setAttribute("x", $x + $dx);
    $text->setAttribute("y", $y + $dy);
    $text->setAttribute("font-family", "Verdana");
    $text->setAttribute("font-size", $FONT_SIZE);
    $text->appendChild($doc->createTextNode($element->title));

    if ($element->children) {
      $text->setAttribute("fill", "green");
      $a = $doc->createElement("a");
      $a->setAttribute("xlink:href", $_SERVER['PHP_SELF']."?lang=$lang&".$f."c=".$element->name."&svg=yes");
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
    $text->setAttribute("x", $myX + $dx);
    $text->setAttribute("y", $myY + $dy);
  }

  //draw "Up" and "Back" links under the navigation tree
  //$name : name of the current criterion
  function drawNavBar($name) {
    global $doc;
    global $myDoc;
    global $f;
    global $g;
    global $msg;
    global $lang;

    $a = $doc->createElement("a");
    $a->setAttribute("xlink:href","show.php?lang=$lang&".$f."svg=yes");
    $text = $doc->createElement("text");
    $text->setAttribute("x", 0);
    $text->setAttribute("y", 25);
    $text->setAttribute("fill", "green");
    $text->appendChild($doc->createTextNode($msg['s5_back']));
    $a->appendChild($text);
    $g->appendChild($a);

    if ($myDoc[0]->getParent($name)) {
      $a = $doc->createElement("a");
      $a->setAttribute("xlink:href", $_SERVER['PHP_SELF']."?lang=$lang&".$f."c=".$myDoc[0]->getParent($name)->getAttribute("name")."&svg=yes");
      $text = $doc->createElement("text");
      $text->setAttribute("x", strlen($msg['s5_back'])*12);
      $text->setAttribute("y", 25);
      $text->setAttribute("fill", "green");
      $text->appendChild($doc->createTextNode($msg['s5_up']));
      $a->appendChild($text);
      $g->appendChild($a);
    }
  }

  //draw the graph's title including software name and release and navigation tree
  //$name : name of the current criterion
  function drawTitle($name) {
    global $doc;
    global $myDoc;
    global $FONT_SIZE;

    $text = $doc->createElement("text");
    $text->setAttribute("font-family", "Verdana");
    $text->setAttribute("font-weight", "bold");
    $text->setAttribute("font-size", $FONT_SIZE);

    $tspan = $doc->createElement("tspan");
    $tspan->appendChild($doc->createTextNode($title = $myDoc[0]->getkeytitle($name)));
    $text->appendChild($tspan);

    $lasttspan = $tspan;

    $node = $name;
    while ($myDoc[0]->getParent($node)) {
      $tspan = $doc->createElement("tspan");
      $tspan->appendChild($doc->createTextNode($myDoc[0]->getParent($node)->getAttribute("title") . " > "));
      $text->insertBefore($tspan, $lasttspan);
      $node = $myDoc[0]->getParent($node)->getAttribute("name");
      $lasttspan = $tspan;
    }

    for ($i=0; $i < count($myDoc); $i++) {
      $tspan = $doc->createElement("tspan");
      $tspan->setAttribute("fill", getcolor($i));
      $tspan->appendChild($doc->createTextNode($myDoc[$i]->getkey("appname")." ".$myDoc[$i]->getkey("release")." "));
      $text->insertBefore($tspan, $lasttspan);
    }

    return $text;
  }

  //draw path between points on each axis
  //$myDoc : QSOSDocument concerned
  //$name : name of the criteria regrouping subcriteria to be displayed
  //	if $name is not set, gobal sectiosn are displayed
  //$n : position of the software to display in the list (used for coloring)
  //$weights: array of weights for the scores
  function drawPath($myDoc, $name, $n, $weights) {
    global $doc;
    global $SCALE, $dx, $dy;
    global $num;
    $path = $doc->createElement("path");
    $myD = "";

    if (isset($name) && $name != "") {
      $tree = $myDoc->getWeightedSubTree($name, $weights);
    } else {
      $tree = $myDoc->getWeightedTree($weights);
    }

    /*N
    $totalWeight = 0;
    for ($i=0; $i < count($tree); $i++) {
      $totalWeight = $totalWeight + $weights[$tree[$i]->name];
    }*/

    drawAxis(count($tree));
    $angle = 0;
    for ($i=0; $i < count($tree); $i++) {
      /*N $delta = $weights[$tree[$i]->name]*2*pi()/$totalWeight;
      $angle_text = $angle + $delta/2;
      $angle = $angle + $delta;
      drawSingleAxis($angle);*/

      $myD .= ($i==0)?"M":"L";
      //N: should be commented
      $angle = ($i+1)*2*pi()/(count($tree));
      $x = ($tree[$i]->score)*$SCALE*cos($angle);
      $x = $x + $dx;
      $y = ($tree[$i]->score)*$SCALE*sin($angle);
      $y = $y + $dy;
      $myD .= " $x $y ";
      //2.1 = 2 + 0.1 of padding before actual text display
      //N: drawText(2.1*$SCALE*cos($angle_text), 2.1*$SCALE*sin($angle_text), $tree[$i]);
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
  //$g->setAttribute('transform', 'translate(500,300)');
  $g->setAttribute('transform', 'translate(50,50)');
  $g->appendChild(drawTitle($name));
  drawNavBar($name);
  //display each software on teh graph
  for($i=0; $i<$num; $i++) {
    $g->appendChild(drawPath($myDoc[$i], $name, $i, $weights));
  }
  $svg->appendChild($g);
  $doc->appendChild($svg);

  echo $doc->saveXML();
} else {
//Graph generated with jpgraph
  include("config.php");
  include ($jpgraph_path."jpgraph.php");
  include ($jpgraph_path."jpgraph_radar.php");
  include("libs/QSOSDocument.php");

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
    if (isset($name) && $name != "") {
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
  if (isset($name) && $name != "") {
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
