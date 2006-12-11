<?php
include("config.php");
$output = new DOMDocument();

$selector = $_REQUEST["tpl"];
if (isset($selector) && $selector == "yes") $list_templates = true;

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