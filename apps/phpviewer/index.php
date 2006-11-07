<html>
<head>
<LINK REL=StyleSheet HREF="phpviewer.css" TYPE="text/css"/>
</head>

<body>
<?php

include("config.php");

$family = $_GET['f'];

function getFamilies($dir) {
   	global $delim;
	$families = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (is_dir($dir.$delim.$file) && ($file != 'CVS') && ($file != '.') && ($file != '..') && ($file != 'include')) {
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
		if (is_dir($path.$delim.$element) && $element!= "." && $element!= ".." && $element!= "CVS" && $element!= "template") {
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
	echo "<div>Select a software family:<br/>\n";
	echo "<table style='border-collapse: collapse;'>\n";
	echo "<tr class='title'><td>Software families</td></tr>\n";
	for ($i=0; $i<count($families); $i++) {
		echo "<tr class='level1'><td><a href='index.php?f=$families[$i]'>$families[$i]</a></td></tr>\n";
	}
	echo "</table></div>\n";
} else {
	$tree= retrieveTree($sheet.$delim.$family); 
	$keys = array_keys($tree);
	
	echo "<div>Select a software:<br/>\n";
	echo "<form action='show.php'>\n";
	echo "<table style='border-collapse: collapse;'>\n";
	echo "<tr class='title'><td colspan='2'>$family</td></tr>\n";
	for ($i=0; $i<count($keys); $i++) {
		if (!is_int($keys[$i])) {
			echo "<tr class='level0'><td colspan='2'>$keys[$i]</td></tr>\n";
			for ($j=0; $j<count($tree[$keys[$i]]); $j++) {
				$file = $tree[$keys[$i]][$j];
				$link = $sheet.$delim.$family.$delim.$keys[$i].$delim.$file;
				echo "<tr class='level1'><td><a href='show_single?f=$link'>$file</a></td>\n";
				echo "<td><input type='checkbox' name='files[]' value='$link'></td></tr>\n";
			}
		}
	}
	echo "</table>";
	echo "<input type='submit' value='Compare'></form></div>\n";
}

?>
</body>
</html>