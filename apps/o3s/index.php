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
** index.php: lists software families and shows search box
**
*/

include("config.php");
include("fs.functions.php");
include("locales/$lang.php");

echo "<html>\n";
echo "<head>\n";
echo "<LINK REL=StyleSheet HREF='skins/$skin/o3s.css' TYPE='text/css'/>\n";
echo "</head>\n";

echo "<body>\n";
echo "<center>\n";
echo "<img src='skins/$skin/o3s.png'/>\n";
echo "<br/><br/>\n";

echo "<div style='font-weight: bold'>".$msg['s1_title']."<br/><br/>\n";

echo "<table style='border-collapse: collapse'>\n";

$tree = retrieveLocalizedTree($sheet, $locale);
if (count($tree)) {
	echo "<tr class='title'>\n";
	echo "<td>".$msg['s1_table_title']."</td>\n";
	echo "</tr>\n";
	
	$families = array_keys(retrieveLocalizedTree($sheet, $locale));
	for ($i=0; $i<count($families); $i++) {
		echo "<tr class='level1' 
			onmouseover=\"this.setAttribute('class','highlight')\" 
			onmouseout=\"this.setAttribute('class','level1')\">\n";
		echo "<td><a href='set_weighting.php?family=$families[$i]'>$families[$i]</a></td>\n";
		echo "</tr>\n";
	}
} else {
	echo "<tr class='title'>\n";
	echo "<td>".$msg['s1_no_evaluations']."</td>\n";
	echo "</tr>\n";
}

echo "</table>\n";

echo "<p>".$msg['s1_search']."<br/><form action='search.php' method='post'>
	<input type='text' name='s' size='20' maxlength='30'/>
	<input type='submit' value='".$msg['s1_button']."'/>
</form></p>";
echo "</div>\n";

echo "</center>\n";
echo "</body>\n";
echo "</html>\n";
?>