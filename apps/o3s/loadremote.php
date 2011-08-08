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
 *  O3S - Backend for remote clients
 *  loadremote.php: lists software families and shows search box
 *
**/


include("config.php");

$output = new DOMDocument();

//Should we return the templates list or the sheets list?
if (isset($_REQUEST["tpl"]) && (strcmp($_REQUEST["tpl"], "yes") == 0) {
  $list_templates = true;
}

function getListTemplates($path, $webpath) {
	global $output;
	global $delim;

	$templates = $output->createElement("templates");
	if (is_dir($path) && $dh = opendir($path)) {
		while (($file = readdir($dh)) !== false) {
			if (substr($file, -5) == ".qsos") {
				$newtreeitem = $output->createElement("item");
				$newtreeitem->setAttribute("id", $webpath.$delim.$file);
				$newtreeitem->setAttribute("label", $file);
				$templates->appendChild($newtreeitem);
			}
		}
		closedir($dh);
	}
	return $templates;
}

function buildTreeSheets($path, $webpath) {
	global $output;
	global $delim;

	$children = $output->createElement("children");
	if (is_dir($path) && $dh = opendir($path)) {
		while (($file = readdir($dh)) !== false) {
			$subpath = $path.$delim.$file;
			$newwebpath = $webpath.$delim.$file;
			if (is_dir($subpath) && ($file != 'CVS') && ($file != '.') && ($file != '..') && ($file != 'include') && ($file != 'template') && ($file != 'templates') && ($file != '.svn')) {
				$newtreeitem = $output->createElement("item");
				$newtreeitem->setAttribute("id", $newwebpath);
				$newtreeitem->setAttribute("label", $file);
				$newtreeitem->appendChild(buildTreeSheets($subpath, $newwebpath));
				$children->appendChild($newtreeitem);
			} elseif (substr($file, -5) == ".qsos") {
				$newtreeitem = $output->createElement("item");
				$newtreeitem->setAttribute("id", $newwebpath);
				$newtreeitem->setAttribute("label", $file);
				$children->appendChild($newtreeitem);
			}
		}
		closedir($dh);
	}
	return $children;
}

$doc = $output->createElement("Document");
if ($list_templates) {
	$output->appendChild(getListTemplates($template, $template_web));
} else {
	$output->appendChild(buildTreeSheets($sheet, $sheet_web));
}

header('Content-type: text/xml');
echo $output->saveXML();

?>