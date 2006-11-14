<html>
<head>
<LINK REL=StyleSheet HREF="phpviewer.css" TYPE="text/css"/>
<script>
function toggleSVG() {
	var svg = document.getElementById("check").getAttribute("svg");
	var links = document.getElementsByTagName("a");
	for(var i=0; i < links.length; i++) {
		var ref = links[i].getAttribute("href");
		if (svg == "on") {
			if (ref.search(/&svg=yes/) != -1) ref = ref.split("&svg=")[0];
			document.getElementById("check").setAttribute("svg", "off");
		} else {
			if (ref.search(/&svg=yes/) == -1) ref += "&svg=yes";
			document.getElementById("check").setAttribute("svg", "on");
		}
		
		links[i].setAttribute("href", ref);
	}
	
}
</script>
</head>

<body>
<center>
<img src="qsos.png"/>
<br/><br/>
<?php

include("config.php");

$family = $_GET['f'];

function getFamilies($dir) {
   	global $delim;
	$families = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (is_dir($dir.$delim.$file) && ($file != 'CVS') && ($file != '.') && ($file != '..') && ($file != 'include') && ($file != 'template') && ($file != 'templates')) {
					array_push($families, $file);
				}
			}
			closedir($dh);
		}
	}
	return (isset($families) ? $families : false);
}

function retrieveTree($path)  {
	global $delim;
	
	if ($dir=@opendir($path)) {
	while (($element=readdir($dir))!== false) {
		if (is_dir($path.$delim.$element) && $element != "." && $element != ".." && $element != "CVS" && $element != "template" && $element != "templates") {
			$array[$element] = retrieveTree($path.$delim.$element);
		} elseif (substr($element, -5) == ".qsos") {
			$array[] = $element;
		}
	}
	closedir($dir);
	}
	return (isset($array) ? $array : false);
}

if (!isset($family)) {
	$families = getFamilies($sheet);
	echo "<div>Select a software family:<br/><br/>\n";
	echo "<table style='border-collapse: collapse'>\n";
	echo "<tr class='title'><td>Software families</td></tr>\n";
	for ($i=0; $i<count($families); $i++) {
		echo "<tr class='level1'><td><a href='index.php?f=$families[$i]'>$families[$i]</a></td></tr>\n";
	}
	echo "</table></div>\n";
} else {
	$tree= retrieveTree($sheet.$delim.$family); 
	$keys = array_keys($tree);
	
	echo "<div>Select a software:<br/><br/>\n";
	echo "<form action='show.php'>\n";
	echo "<table>\n";
	echo "<tr class='title'><td>$family</td><td align='center'>HTML</td><td align='center'>OpenDocument</td><td><input type='submit' value='Compare'></td></tr>\n";
	for ($i=0; $i<count($keys); $i++) {
		if (!is_int($keys[$i])) {
			echo "<tr class='level0'><td colspan='4'>$keys[$i]</td></tr>\n";
			for ($j=0; $j<count($tree[$keys[$i]]); $j++) {
				$file = $tree[$keys[$i]][$j];
				$link = $sheet.$delim.$family.$delim.$keys[$i].$delim.$file;
				$name = basename($file, ".qsos");
				$odsfile = $name.".ods";
				
				echo "<tr class='level1'>\n";
				echo "<td>$name</td>\n";
				echo "<td align='center'><a href='show.php?f[]=$link&svg=yes'><img src='html.png' border='0'/></a></td>\n";
				echo "<td align='center'><a href='export_oo.php?f=$link'><img src='ods.png' border='0'/></a></td>\n";
				echo "<td align='center'><input type='checkbox' name='f[]' value='$link'></td></tr>\n";
			}
		}
	}
	echo "</table><br/>";
	echo "My brower supports SVG <input id='check' type='checkbox' name='svg' value='yes' onclick='toggleSVG()' svg='on' checked>";
	echo "</form></div>\n";
}

?>
</center>
</body>
</html>