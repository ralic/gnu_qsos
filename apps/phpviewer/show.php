<html>
<head>
<LINK REL=StyleSheet HREF="phpviewer.css" TYPE="text/css"/>
<script language="JavaScript" type="text/javascript">
var size = 12;

function matchStart(target, pattern) {
	var pos = target.indexOf(pattern);
	if (pos == 0) {
		return true;
	} else {
		return false;
	}
}

function expand(div) {
	var rows = document.getElementsByTagName("tr");
	var id = div.parentNode.parentNode.id + "-";
	for (var i = 0; i < rows.length; i++) {
		var r = rows[i];
		if (matchStart(r.id, id)) {
			if (document.all) r.style.display = "block"; //IE4+ specific code
    			else r.style.display = "table-row"; //Netscape and Mozilla
		}
	}
	div.className = "expanded";
	div.onclick = function () {
		collapse(this);
	}
}

function collapse(div) {
	var rows = document.getElementsByTagName("tr");
	var id = div.parentNode.parentNode.id + "-";
	for (var i = 0; i < rows.length; i++) {
		var r = rows[i];
		if (matchStart(r.id, id)) {
			r.style.display = "none";
		}
	}
	div.className = "collapsed";
	div.onclick = function () {
		expand(this);
	}
}

function collapseAll() {
	var rows = document.getElementsByTagName("TR");
		for (var j = 0; j < rows.length; j++) {
		var r = rows[j];
		if (r.id.indexOf("-") >= 0) {
			r.style.display = "none";
		}
	}
	document.getElementById("all_selector").href = "javascript:expandAll();";
	document.getElementById("all_selector").firstChild.nodeValue = "Expand All";
}

function expandAll() {
	var rows = document.getElementsByTagName("TR");
		for (var j = 0; j < rows.length; j++) {
		var r = rows[j];
		if (r.id.indexOf("-") >= 0) {
			if (document.all) r.style.display = "block"; //IE4+ specific code
    			else r.style.display = "table-row"; //Netscape and Mozilla
		}
	}
	document.getElementById("all_selector").href = "javascript:collapseAll();";
	document.getElementById("all_selector").firstChild.nodeValue = "Collapse All";
}

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
	document.getElementById("column").src = "hide-comments.png"
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
	document.getElementById("column").src = "show-comments.png";
}

function decreaseFontSize() {
	size--;
	document.getElementById("table").style.fontSize = size + "pt";
}

function increaseFontSize() {
	size++;
	document.getElementById("table").style.fontSize = size + "pt";
}
</script>
</head>
<body>
<center>
<img src="qsos.png"/>
<br/><br/>
<?php
include("QSOSDocument.php");

$files = $_GET['f'];
$svg = $_GET['svg'];

$num = count($files);
$myDoc = array();
$app = array();
$trees = array();

$i = 0;
foreach($files as $file) {
	$myDoc[$i] = new QSOSDocument($file);
	$app[$i] = $myDoc[$i]->getkey("appname")." ".$myDoc[$i]->getkey("release");
	$trees[$i] = $myDoc[$i]->getTree();
	$i++;
}

$family = $myDoc[0]->getkey("qsosappfamily");

$f = "";
foreach($files as $file) {
	$f .= "f[]=$file&";
}

echo "<table>";
echo "<tr width='100%'><td>";
//echo "<a id='all_selector' href='javascript:collapseAll();'><img src='all.png' border=0 onmouseover=\"return escape('Expand/collapse all')\"/></a>";
echo "<a id='comment_selector' href='javascript:hideComments();'><img id='column' src='hide-comments.png' border=0 onmouseover=\"return escape('Hide/Show comments')\"/></a>";
echo " <a href='javascript:decreaseFontSize();'><img src='decrease-font.png' border=0 onmouseover=\"return escape('Decrease font size')\"/></a>";
echo " <a href='javascript:increaseFontSize();'><img src='increase-font.png' border=0 onmouseover=\"return escape('Increase font size')\"/></a>";
echo " <a href='radar.php?".$f."svg=$svg'><img src='graph.png' border=0 onmouseover=\"return escape('Show graph')\"/></a></td></tr></table>";

echo "<table id='table' style='border-collapse: collapse; font-size: 12pt; table-layout: fixed'>\n";
echo "<tr class='title' style='width: 250px'><td>$family</td>";
echo "<td style='width: 30px'><a href='radar.php?".$f."svg=$svg'><img src='graph.png' border=''/></a></td>";
for($i=0; $i<$num; $i++) {
	echo "<td><div style='width: 100px'>$app[$i]</div></td><td id='comment' style='width: 300px'>Comments</td>";
}
echo "</tr>\n";

showtree($myDoc, $trees, 0, '');
echo "</table>\n";

function showtree($myDoc, $trees, $depth, $idP) {
	global $svg;
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

		echo "<tr id='$id' name='$name' class='level$depth' onmouseover=\"this.setAttribute('class','highlight')\" onmouseout=\"this.setAttribute('class','level$depth')\">\n";
		if ($subtree) {
			echo "<td style='width: 250px; text-indent: $offset'><span onclick=\"collapse(this);\" class='expanded'>$title</span></td><td style='width: 30px'>";
			if ($myDoc[0]->hassubelements($name) > 2) {
				$files = $_GET['f'];
				$f = "";
				foreach($files as $file) {
					$f .= "f[]=$file&";
				}
				echo "<a href='radar.php?".$f."c=$name&svg=$svg'><img src='graph.png' border=''/></a></td>";
			}
		} else {
			echo "<td style='width: 250px; text-indent: $offset'><span>$title</span></td><td style='width: 30px'></td>\n";;
		}

		for($i=0; $i<count($trees); $i++) {
			$desc = addslashes($myDoc[$i]->getgeneric($name, "desc".$trees[$i][$k]->score));
			if ($desc != "") {
				echo "<td class='score' style='width: 100px; cursor:help'' onmouseover=\"return escape('".$desc."')\">".$trees[$i][$k]->score."</td>\n";
			} else {
				echo "<td class='score' style='width: 100px'>".$trees[$i][$k]->score."</td>\n";
			}
			echo "<td id='comment'><div style='width: 300px'>".$myDoc[$i]->getgeneric($name, "comment")."</div></td>\n";
		}
		echo "</tr>\n";
;
		if ($subtree) {
			for($i=0; $i<count($trees); $i++) {
				$subtrees[$i] = $trees[$i][$k]->children;
			}
			showtree($myDoc, $subtrees, $new_depth, $id);
		}
	}
}

?>
</center>
<script language="JavaScript" type="text/javascript" src="wz_tooltip.js"></script>
</body>
</html>