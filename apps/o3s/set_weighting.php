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
** set_weighting.php: displays form to enter weigthings
**
*/

session_start();
session_unset();
session_destroy();
$_SESSION = array();

include("config.php");
include("fs.functions.php");
include("locales/$lang.php");

echo "<html>\n";
echo "<head>\n";
echo "<LINK REL=StyleSheet HREF='skins/$skin/o3s.css' TYPE='text/css'/>\n";
?>
<script src="commons.js" language="JavaScript" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">

function checkWeight(field) {
	if (isNaN(field.value)) {
		var oldValue = field.value;
		field.value = "1";
		alert(oldValue + " is not an applicable weight value,\n it has been set to 1.");
		field.focus();
	}
}

function save() {
	myForm.action = "save_weighting.php";
	myForm.submit();
}

function next() {
	myForm.action = "software.php";
	myForm.submit();
}

function upload() {
	var file = document.getElementById("weighting");
	if (file.value == "") {
		alert("No weight file is provided!");
		return;
	}
	myForm.action = "set_weighting.php";
	myForm.submit();
}
</script>
<?php
echo "</head>\n";

echo "<body>\n";
echo "<center>\n";
echo "<img src='skins/$skin/o3s.png'/>\n";
echo "<br/><br/>\n";

include("libs/QSOSDocument.php");

$family = $_REQUEST['family'];
$svg = $_REQUEST['svg'];

$upload = false;
$weights = array();

//Upload of weighting file
if (isset($_FILES['weighting'])) {
	$weighting = $_FILES['weighting'];
	$dir = $temp.uniqid();
	move_uploaded_file($weighting['tmp_name'], $dir);
	chmod ($dir, 0770);

	$doc = new DOMDocument();
	$doc->load($dir);
	$xpath =  new DOMXPath($doc);

	$nodes = $xpath->query("//family");
	$upload_family = $nodes->item(0)->nodeValue;

	if ($upload_family == $family) {
		$nodes = $xpath->query("//weight");
		foreach ($nodes as $node) {
			$name = $node->getAttribute('id');
			$value = $node->nodeValue;
			$weights[$name] = $value;
		}
		$text = "<div style='texte-align: center; color: red'>"
			.$weighting['name']
			.$msg['s2_loaded']
			."</div><br/>";
		$upload = true;
	} else {
		$text = "<div style='texte-align: center; color: red'>"
			.$family.$msg['s2_error1']
			.$upload_family.$msg['s2_error2']
			."</div><br/>";
	}
}

$tree= retrieveLocalizedTree($sheet.$delim.$family, $locale);
$keys = array_keys($tree);

$file = $tree[$keys[0]][0];
$file = $sheet.$delim.$family.$delim.$keys[0].$delim.$file;

$myDoc = new QSOSDocument($file);
$tree = $myDoc->getTree();
$familyname = $myDoc->getkey("qsosappfamily");

echo "<div style='font-weight: bold'>"
	.$msg['s2_title']
	."<br/><br/></div>\n";
echo $text;
echo "<form id='myForm' 
	enctype='multipart/form-data' 
	method='POST' 
	action='software.php'>\n";
echo "<input type='hidden' 
	name='family' 
	value='$family'/>\n";
echo "<table id='table' 
	style='border-collapse: collapse; font-size: 12pt; table-layout: fixed'>\n";
echo "<tr class='title' style='width: 400px'><td>$familyname</td>\n";
echo "<td><div style='width: 60px; text-align: center'>"
	.$msg['s2_weight']
	."</div></td>\n";
echo "</tr>\n";

showtree($myDoc, $tree, 0, '');

echo "<input type='button' 
	value='".$msg['s2_button_back']."' 
	onclick='location.href=\"index.php\"'> ";
echo " <input type='button' 
	value='".$msg['s2_button_save']."' 
	onclick='save()'> ";
echo " <input type='button' 
	value='".$msg['s2_button_next']."' 
	onclick='next()'><br/><br/>\n";
echo "<input type='file' 
	id='weighting' name='weighting'> 
      <input type='button' 
	value='".$msg['s2_button_upload']."' 
	onclick='upload()'><br/><br/>\n";
echo "</table>\n";
echo "</form>\n";

//Recursive function to display tree of criteria
function showtree($myDoc, $tree, $depth, $idP) {
	global $upload;
	global $weights;
	$new_depth = $depth + 1;
	$offset = $new_depth*10;
	$idF = 0;

	for($k=0; $k<count($tree); $k++) {
		$name = $tree[$k]->name;
		$title = $tree[$k]->title;
		$subtree = $tree[$k]->children;
	
		$idF++;
		if ($idP == '') {
			$id = $idF;
		} else  {
			$id = $idP."-".$idF;
		}
	
		echo "<tr id='$id' 
			class='level$depth' 
			onmouseover=\"this.setAttribute('class','highlight')\" 
			onmouseout=\"this.setAttribute('class','level$depth')\">\n";
		if ($subtree) {
			echo "<td style='width: 400px; text-indent: $offset'>
				<span onclick=\"collapse(this);\" class='expanded'>$title</span></td>\n";
		} else {
			echo "<td style='width: 400px; text-indent: $offset'>
				<span>$title</span></td>\n";
		}
	
		echo "<td><div style='width: 60px; text-align: center'>\n";
		//If a weighting file has been uploaded use $weights array, if not use default value 1
		echo "<input type='text' 
			name='$name' size='3' style='text-align: center' onblur='checkWeight(this)' 
			value='".(($upload)?$weights[$name]:1)."'/>\n";

		echo "</div></td>\n";
	
		echo "</tr>\n";

		if ($subtree) {
			showtree($myDoc, $subtree, $new_depth, $id);
		}
	}
}

echo "</center>\n";
echo "</body>\n";
echo "</html>\n";
?>