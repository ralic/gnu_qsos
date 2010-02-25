<?php
/*
**  Copyright (C) 2009 Atos Origin 
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
** quadrant.php: QSOS quadrant generation (in SVG format)
**
*/
session_start();

include("config.php");
include("lang.php");
include('libs/QSOSDocument.php');

//QSOS evaluations to display
$ids = $_REQUEST['id'];
//Weightings are stored in session
$weights = $_SESSION;

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

$myDoc = array();
$num = count($files);

header("Content-type: image/svg+xml");
echo "<?xml version='1.0' encoding='UTF-8' standalone='no'?>\n";

//$x, $y: position where to draw text (and ellipsis)
//$text: text to draw
//$i: id of software (for link)
//$ellipsis: shoul the texte be inserted in an ellipsis?
function draw($x,$y,$text,$i,$ellipsis=false) {
  echo("        <g transform='translate($x,$y)'>\n");
  echo("<a xlink:href='show.php?lang=$lang&amp;id[]=$i&amp;svg=yes'>");
  if ($ellipsis) {
    echo("         <path style='fill:#fcdea2;fill-opacity:0.5;stroke:#000000;stroke-width:2;stroke-opacity:1' d='M -57,0 A 57,21 0 1 1 57,0 A 57,21 0 1 1 -57,0 z' />\n");
    $fontSize = '10px';
  } else $fontSize = '14px';
  echo("         <text><tspan style='font-size:$fontSize;text-anchor:middle;font-family:Bitstream Vera Sans' y='2.7' x='0'>$text</tspan></text>\n");
  echo("        </a>\n");
  echo("        </g>\n");
}
?>
<svg
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:xlink="http://www.w3.org/1999/xlink"
   width="100%"
   height="100%">
  <defs>
    <linearGradient
       id="gradientBase">
      <stop
         style="stop-color:#ffffff;stop-opacity:1;"
         offset="0" />
      <stop
         style="stop-color:#a7e5e5;stop-opacity:1;"
         offset="1" />
    </linearGradient>
    <marker
       orient="270"
       refY="0.0"
       refX="0.0"
       id="Arrow2Mend"
       style="overflow:visible;">
      <path
         style="font-size:12;stroke-width:0.6"
         d="M 8,4 L -2,0 L 8,-4 C 6.9,-2 7,2 8,4 z"
         transform="scale(0.6) rotate(180) translate(0,0)" />
    </marker>
    <marker
       orient="0"
       refY="0.0"
       refX="0.0"
       id="Arrow2Lend"
       style="overflow:visible;">
      <path
         style="font-size:12;stroke-width:0.6"
         d="M 8,4 L -2,0 L 8,-4 C 6.9,-2 7,2 8,4 z"
         transform="scale(0.6) rotate(180) translate(1,0)" />
    </marker>
    <linearGradient
       xlink:href="#gradientBase"
       id="gradient"
       gradientUnits="userSpaceOnUse"
       x1="280"
       y1="157"
       x2="280"
       y2="487"
       gradientTransform="matrix(1,0,0,1,-2,-28)" />
  </defs>
  <g>
    <g
       transform="translate(100,200)">
      <g>
        <rect
           style="opacity:1;fill:url(#gradient);fill-opacity:1;stroke:#868686;stroke-width:3;stroke-opacity:1"
           width="500"
           height="340"
           x="0"
           y="0"
           ry="0" />
        <rect
           style="opacity:1;fill:none;fill-opacity:1;stroke:#868686;stroke-width:3;stroke-dasharray:6,6"
           width="500"
           height="170"
           x="0"
           y="0"
           ry="0" />
        <rect
           style="opacity:1;fill:none;fill-opacity:1;stroke:#868686;stroke-width:3;stroke-dasharray:6,6"
           width="250"
           height="340"
           x="0"
           y="0"
           ry="0" />
        <g
           transform="translate(-42,-112)">
          <text
             transform="matrix(0,-1,1,0,0,0)"><tspan
               style="font-size:14px;font-family:Bitstream Vera Sans"
               y="28"
               x="-318"><?php echo $msg['qq_maturity']; ?></tspan></text>
          <text><tspan
               style="font-size:14px;font-family:Bitstream Vera Sans"
               y="471"
               x="196"><?php echo $msg['qq_funccoverage']; ?></tspan></text>
          <path
             d="M 42,452 C 570,452 570,452 570,452"
             style="fill-opacity:0.75;stroke:#000000;stroke-width:3;marker-end:url(#Arrow2Lend);stroke-opacity:1" />
          <path
             d="M 42,452 C 42,82 42,82 42,82"
             style="fill-opacity:0.75;stroke:#000000;stroke-width:3;marker-end:url(#Arrow2Mend);stroke-opacity:1" />
        </g>
<?php
for($i=0; $i<$num; $i++) {
  $myDoc[$i] = new QSOSDocument($files[$i]);
  $tree = $myDoc[$i]->getWeightedTree($weights);
  $y = 340 - (($tree[0]->score)*340/2);
  $totalWeight = 0;
  $sum = 0;
  for($k=1; $k<count($tree); $k++) {
    $name = $tree[$k]->name;
    $weight = $weights[$name];
    if (!isset($weight)) $weight = 1;
    $totalWeight = $totalWeight + $weight;
    $sum += round(($tree[$k]->score)*$weight, 2);
  }
  if ($totalWeight == 0) $score = 0;
  $score = round(($sum/$totalWeight), 2);
  $x = $score*500/2;
  draw($x, $y, $myDoc[$i]->getkey("appname"), $ids[$i], true);
}
?>
      </g>
    </g>
  </g>
</svg>
