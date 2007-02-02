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
** show.php: show QSOS evaluation(s)
**
*/

session_start();
//Search pattern
$s = $_REQUEST['s'];
//If there is a search pattern, no weights are to be applied
$is_weighted = !isset($s);

include("config.php");
include("locales/$lang.php");

echo "<html>\n";
echo "<head>\n";
echo "<LINK REL=StyleSheet HREF='skins/$skin/o3s.css' TYPE='text/css'/>\n";
?>
<script src="commons.js" language="JavaScript" type="text/javascript"></script>
<script src="search.js" language="JavaScript" type="text/javascript"></script>
<script>
var size = 12;

function showComments() {
	var cells = document.getElementsByTagName("td");
	for (var i = 0; i < cells.length; i++) {
		var c = cells[i];
		if (c.id == 'comment') {
			if (document.all) c.style.display = "block"; //IE4+ specific code
    			else c.style.display = "table-row"; //Netscape and Mozilla
		}
	}
	document.getElementById("comment_selector").href = "javascript:hideComments();";
	document.getElementById("column").src = "<?php echo "skins/$skin/hide-comments.png"; ?>";
}

function hideComments() {
	var cells = document.getElementsByTagName("td");
	for (var i = 0; i < cells.length; i++) {
		var c = cells[i];
		if (c.id == 'comment') {
			c.style.display = "none";
		}
	}
	document.getElementById("comment_selector").href = "javascript:showComments();";
	document.getElementById("column").src = "<?php echo "skins/$skin/show-comments.png"; ?>";
}

function decreaseFontSize() {
	size--;
	document.getElementById("table").style.fontSize = size + "pt";
}

function increaseFontSize() {
	size++;
	document.getElementById("table").style.fontSize = size + "pt";
}

function submitForm(c) {
	document.getElementById("c").value = c;
	myForm.submit();
}
</script>
<?php
echo "</head>\n";

include("libs/QSOSDocument.php");

//Software family
$family = $_REQUEST['family'];
//QSOS XML files to be displayed
$files = $_REQUEST['f'];
//Are graphs to be generated in SVG?
$svg = $_REQUEST['svg'];

if (isset($s)) {
	echo "<body onload=\"highlightSearchTerms('$s');\">\n";
} else {
	echo "<body>\n";
}
echo "<center>\n";
echo "<img src='skins/$skin/o3s.png'/>\n";
echo "<br/><br/>\n";
echo "<div style='font-weight: bold'>".$msg['s4_title']."<br/><br/>\n";

echo "<form id='myForm' method='POST' action='radar.php'>\n";

$weights = $_SESSION;

foreach($files as $file) {
	echo "<input type='hidden' name='f[]' value='$file'/>\n";
}
echo "<input type='hidden' name='svg' value='$svg'/>\n";
echo "<input type='hidden' name='c' id='c' value=''/>\n";
echo "<input type='hidden' name='family' value='$family'/>\n";
echo "<input type='hidden' name='s' value='$s'/>\n";

$num = count($files);
$myDoc = array();
$app = array();
$trees = array();

$i = 0;
foreach($files as $file) {
	$myDoc[$i] = new QSOSDocument($file);
	$app[$i] = $myDoc[$i]->getkey("appname")." ".$myDoc[$i]->getkey("release");
	if ($is_weighted) {
		$trees[$i] = $myDoc[$i]->getWeightedTree($weights);
	} else {
		$trees[$i] = $myDoc[$i]->getTree();
	}
	$i++;
}

$familyname = $myDoc[0]->getkey("qsosappfamily");

$f = "";
foreach($files as $file) {
	$f .= "f[]=$file&";
}

echo "<table>\n";
echo "<tr width='100%' align='center'><td>\n";
echo "<a id='comment_selector' href='javascript:hideComments();'>";
echo "<img id='column' 
	src='skins/$skin/hide-comments.png' 
	border=0 
	onmouseover=\"return escape('Hide/Show comments')\"/>";
echo "</a>\n";
echo " <a href='javascript:decreaseFontSize();'>";
echo "<img src='skins/$skin/decrease-font.png' 
	border=0 
	onmouseover=\"return escape('Decrease font size')\"/>";
echo "</a>\n";
echo " <a href='javascript:increaseFontSize();'>";
echo "<img src='skins/$skin/increase-font.png' 
	border=0 
	onmouseover=\"return escape('Increase font size')\"/>";
echo "</a>\n";
if ($is_weighted) {
	echo " <a href='radar.php?family=$family&".$f."svg=$svg'>";
	echo "<img src='skins/$skin/graph.png' 
		border=0 onmouseover=\"return escape('Show graph')\"/>";
	echo "</a>\n";
}
echo "</td></tr>\n";
echo "<tr><td align='center'>\n";
if ($is_weighted) {
	echo "<input type='button' 
		value='".$msg['s4_button_back']
		."' onclick='location.href=\"software.php?family=".$family."&svg=$svg\"'><br/><br/>";
} else {
	echo "<input type='button' 
		value='".$msg['s4_button_back_alt']
		."' onclick='location.href=\"search.php?s=$s\"'><br/><br/>";
}
echo "</td></tr>\n";
echo "</table>\n";

echo "<table id='table' style='border-collapse: collapse; font-size: 12pt; table-layout: fixed'>\n";
echo "<tr class='title' style='width: 250px'>\n";
echo "<td rowspan='2'><div style='text-align: center'>$familyname</div></td>\n";
echo "<td style='width: 30px' rowspan='2'>";
if ($is_weighted) {
	echo "<img src='skins/$skin/graph.png' border='' style='cursor: pointer' onclick='submitForm(\"\")'/>";
}
echo "</td>\n";
for($i=0; $i<$num; $i++) {
	echo "<td colspan='2'><div style='width: 120px; text-align: center'>$app[$i]</div></td>\n";
	echo "<td id='comment' style='width: 300px'>".$msg['s4_comments']."</td>\n";
}
echo "</tr>\n";
echo "<tr class='title'>\n";
for($i=0; $i<$num; $i++) {
	echo "<td><div style='width: 60px; text-align: center'>".$msg['s4_score']."</div></td>\n";
	echo "<td><div style='width: 60px; text-align: center'>".$msg['s4_weight']."</div></td>\n";
	echo "<td id='comment' style='width: 300px'></td>\n";
}
echo "</tr>\n";

showtree($myDoc, $trees, 0, '', $weights);
echo "</table>\n";

function showtree($myDoc, $trees, $depth, $idP, $weights) {
	global $svg;
	global $is_weighted;
	global $skin;
	$new_depth = $depth + 1;
	$offset = $new_depth*10;
	$idF = 0;
	$tree = $trees[0];

	for($k=0; $k<count($tree); $k++) {
		$name = $tree[$k]->name;
		$title = $tree[$k]->title;
		$subtree = $tree[$k]->children;
		$subtrees = array();

		$idF++;
		if ($idP == '') {
			$id = $idF;
		} else  {
			$id = $idP."-".$idF;
		}

		echo "<tr id='$id' 
			name='$name' 
			class='level$depth' 
			onmouseover=\"this.setAttribute('class','highlight')\" 
			onmouseout=\"this.setAttribute('class','level$depth')\">\n";
		if ($subtree) {
			echo "<td style='width: 250px; text-indent: $offset'>
				<span onclick=\"collapse(this);\" class='expanded'>$title</span>
				</td>\n";
			echo "<td style='width: 30px'>";
			if ($myDoc[0]->hassubelements($name) > 2) {
				$files = $_REQUEST['f'];
				$f = "";
				foreach($files as $file) {
					$f .= "f[]=$file&";
				}
				if ($is_weighted) {
					echo "<img src='skins/$skin/graph.png' 
						border='' 
						style='cursor: pointer' onclick='submitForm(\"$name\")'/>\n";
				}
			}
		} else {
			echo "<td style='width: 250px; text-indent: $offset'>
				<span>$title</span>
				</td>\n";
			echo "<td style='width: 30px'></td>\n";;
		}

		for($i=0; $i<count($trees); $i++) {
			$desc = addslashes($myDoc[$i]->getgeneric($name, "desc".$trees[$i][$k]->score));
			if ($desc != "") {
				echo "<td class='score' 
					style='width: 60px; cursor:help' onmouseover=\"return escape('".$desc."')\">
					<div style='text-align: center'>"
						.$trees[$i][$k]->score
					."</div></td>\n";
			} else {
				echo "<td class='score' 
					style='width: 60px; text-align: center'>
					<div style='text-align: center'>"
						.$trees[$i][$k]->score
					."</div></td>\n";
			}
			if ($is_weighted) {
				echo "<td>
					<div style='text-align: center'>"
						.$weights[$name]
					."</div></td>\n";
			} else {
				echo "<td>
					<div style='text-align: center'>1</div>
					</td>\n";
			}
			echo "<td id='comment'>
					<div style='width: 300px'>"
						.$myDoc[$i]->getgeneric($name, "comment")
					."</div></td>\n";
		}
		echo "</tr>\n";
;
		if ($subtree) {
			for($i=0; $i<count($trees); $i++) {
				$subtrees[$i] = $trees[$i][$k]->children;
			}
			showtree($myDoc, $subtrees, $new_depth, $id, $weights);
		}
	}
}

echo "</form>";

echo "<br/>";
echo $msg['g_license_notice'];

echo "</center>\n";
echo "<script language='JavaScript' type='text/javascript' src='libs/wz_tooltip.js'></script>";
echo "</body>\n";
echo "</html>\n";
?>