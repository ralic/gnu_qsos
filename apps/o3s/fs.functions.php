<?php
/**
 *  Copyright (C) 2007-2011 Atos
 *
 *  Author: Raphael Semeteys <raphael.semeteys@atos.net>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 *  O3S
 *  fs.functions.php: common FileSystem functions
 *
**/


//Returns multidimentional array of software families and software evaluations, filtered by locale
//if $locale == "" then english evaluation are returned
function retrieveLocalizedTree($path, $locale) {
	global $delim;

	if ($dir=@opendir($path)) {
	while (($element=readdir($dir))!== false) {
		if (is_dir($path.$delim.$element)
		&& $element != "."
		&& $element != ".."
		&& $element != "CVS"
		&& $element != "template"
		&& $element != "templates"
		&& $element != ".svn") {
			$subarray = retrieveLocalizedTree($path.$delim.$element, $locale);
			if (count($subarray) > 0) $array[$element] = $subarray;
		} elseif (isLocalizedName($element, $locale)) {
			$array[] = $element;
		}
	}
	closedir($dir);
	}
	return $array;
}

function isLocalizedName($name, $locale) {
	if (
		(substr($name, -5) == ".qsos")
		&&
		(
			($locale != "" && substr($name, -8, -5) == "_$locale")
			||
			($locale == "" && !strpos($name, '_'))
		)
	) {
		return true;
	} else {
		return false;
	}
}

?>