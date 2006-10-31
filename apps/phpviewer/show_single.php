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

$file = $_GET['f'];

$myDoc = new QSOSDocument($file);
$family = $myDoc->getkey("qsosappfamily");
$app = $myDoc->getkey("appname")." ".$myDoc->getkey("release");


echo "[<a id='comment_selector' href='javascript:hideComments();'>Hide comments</a>]";
echo "<table style='border-collapse: collapse; table-layout: fixed;'>\n";
echo "<tr class='title'><td>$family</td><td>$app</td><td id='comment'>Comments</td></tr>\n";
showtree($myDoc, $file, $myDoc->getTree(), 0, '');
echo "</table>\n";

function showtree($myDoc, $file, $tree, $depth, $idP) {
	$new_depth = $depth + 1;
	$offset = $new_depth*10;
	$idF = 0;

	foreach($tree as $element) {
		$name = $element->name;
		$title = $element->title;
		$subtree = $element->children;
		$comment = $myDoc->getgeneric($name, "comment");

		$idF++;
		if ($idP == '') {
			$id = $idF;
		} else  {
			$id = $idP."-".$idF;
		}

		echo "<tr id='$id' name='$name' class='level$depth'>\n";
		if ($subtree) {
			echo "<td><span style='position:relative; left:$offset' onclick=\"collapse(this);\" class='expanded'>$title";
			if ($myDoc->hassubelements($name) > 2) {
				echo " [<a href='radar_single.php?f=$file&c=$name'>Schema</a>]";
			}
			echo "</span></td>\n";
		} else {
			echo "<td><span style='position:relative; left:$offset'>$title</span></td>\n";
		}
		echo "<td class='score'>".$element->score."</td>\n";
		echo "<td id='comment'>".$comment."</td></tr>\n";
		if ($subtree) {
			showtree($myDoc, $file, $subtree, $new_depth, $id);
		}
	}
}

?>
<script language="JavaScript" type="text/javascript" src="wz_tooltip.js"></script>
</body>
</html>