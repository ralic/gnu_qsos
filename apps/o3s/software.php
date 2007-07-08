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
** software.php: lists software in a given family
**
*/

session_start();
include("config.php");
include("fs.functions.php");
include("locales/$lang.php");

echo "<html>\n";
echo "<head>\n";
echo "<LINK REL=StyleSheet HREF='skins/$skin/o3s.css' TYPE='text/css'/>\n";
?>
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

function submitForm() {
	var ok = false;
	var inputs = document.getElementsByTagName("input");
	for(var i=0; i < inputs.length; i++) {
		if (inputs[i].type == "checkbox" && inputs[i].name == "f[]" && inputs[i].checked) {
			ok = true;
		}
	}

	if (ok == true) {
		myForm.submit();
	} else {
		alert("At least one product must be checked");
	}
}
</script>
<?php
echo "</head>\n";

echo "<body>\n";
echo "<center>\n";
echo "<img src='skins/$skin/o3s.png'/>\n";
echo "<br/><br/>\n";

$family = $_REQUEST['family'];

if (!isset($_SESSION["generic"])) {
	while (list($name, $value) = each($_REQUEST)) { 
		if (($name != 'f') && ($name != 'svg')) {
			$_SESSION[$name] = $value;
		}
	}
}

$tree = retrieveLocalizedTree($sheet.$delim.$family, $locale);
$keys = array_keys($tree);

echo "<div style='font-weight: bold'>".
	$msg['s3_title'].
	"<br/><br/>\n";
echo "<input type='button' 
	value='".$msg['s3_button_back']."' 
	onclick='location.href=\"set_weighting.php?family=$family\"'/><br/><br/>\n";
echo "<form id='myForm' action='show.php'>\n";
echo "<input type='hidden' name='family' value='$family'/>\n";
echo "<table>\n";
echo "<tr class='title'>
		<td>$family</td>
		<td align='center'>".$msg['s3_format_xml']."</td>
		<td align='center'>".$msg['s3_format_ods']."</td>
		<td><input type='button' value='".$msg['s3_button_next']."' onclick='submitForm()'></td>
	</tr>\n";
for ($i=0; $i<count($keys); $i++) {
	if (!is_int($keys[$i])) {
		echo "<tr class='level0'><td colspan='4'>$keys[$i]</td></tr>\n";
		for ($j=0; $j<count($tree[$keys[$i]]); $j++) {
			$file = $tree[$keys[$i]][$j];
			$link = $sheet.$delim.$family.$delim.$keys[$i].$delim.$file;
			$name = basename($file, ".qsos");
			$odsfile = $name.".ods";
			
			echo "<tr class='level1' 
				onmouseover=\"this.setAttribute('class','highlight')\" 
				onmouseout=\"this.setAttribute('class','level1')\">\n";
			echo "<td>$name</td>\n";
			echo "<td align='center'>
				<a href='$link'><img src='skins/$skin/xml.png' border='0'/></a>
				</td>\n";
			echo "<td align='center'>
				<a href='export_oo.php?f=$link'><img src='skins/$skin/ods.png' border='0'/></a>
				</td>\n";
			echo "<td align='center' class='html'>
				<span class='logo_html'/>
				<input type='checkbox' class='logo_html' name='f[]' value='$link'>
				</td></tr>\n";
		}
	}
}
echo "</table><br/>";
echo $msg['s3_check_svg'].
	" <input id='check' type='checkbox' name='svg' value='yes' onclick='toggleSVG()' svg='on' checked>";
echo "</form></div>\n";

echo "</center>\n";
echo "</body>\n";
echo "</html>\n";
?>