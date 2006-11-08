<html>
<head>
<LINK REL=StyleSheet HREF="phpviewer.css" TYPE="text/css"/>
<script language="JavaScript" type="text/javascript">
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
	document.getElementById("comment_selector").firstChild.nodeValue = "Hide comments";
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
	document.getElementById("comment_selector").firstChild.nodeValue = "Show comments";
}
</script>
</head>
<body>
<?php
include("QSOSDocument.php");

$files = $_GET['files'];

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


echo "[<a id='comment_selector' href='javascript:hideComments();'>Hide comments</a>]";
echo " - Click on the <img src='graph.png' border=''/> icon to see the radar graph";
echo "<table style='border-collapse: collapse; table-layout: fixed;'>\n";

echo "<tr class='title'><td>$family ";

$f = "";
foreach($files as $file) {
	$f .= "f[]=$file&";
}
echo " <a href='radar.php?".$f."'><img src='graph.png' border=''/></a></td>";

for($i=0; $i<$num; $i++) {
	echo "<td>$app[$i]</td><td id='comment'>Comments</td>";
}
echo "</tr>\n";

showtree($myDoc, $trees, 0, '');
echo "</table>\n";

function showtree($myDoc, $trees, $depth, $idP) {
	$new_depth = $depth + 1;
	$offset = $new_depth*10;
	$idF = 0;
	$tree = $trees[0];

	for($k=0; $k<count($tree); $k++) {
	//foreach($trees[0] as $element) {
		//$name = $element->name;
		//$title = $element->title;
		//$subtree = $element->children;
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

		echo "<tr id='$id' name='$name' class='level$depth'>\n";
		if ($subtree) {
			echo "<td><span style='position:relative; left:$offset' onclick=\"collapse(this);\" class='expanded'>$title";
			if ($myDoc[0]->hassubelements($name) > 2) {
				$files = $_GET['files'];
				$f = "";
				foreach($files as $file) {
					$f .= "f[]=$file&";
				}
				echo " <a href='radar.php?".$f."c=$name'><img src='graph.png' border=''/></a>";
			}
			echo "</span></td>\n";
		} else {
			echo "<td><span style='position:relative; left:$offset'>$title</span></td>\n";
		}

		for($i=0; $i<count($trees); $i++) {
			echo "<td class='score'>".$trees[$i][$k]->score."</td>\n";
			echo "<td id='comment'>".$myDoc[$i]->getgeneric($name, "comment")."</td>\n";
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
</body>
</html>