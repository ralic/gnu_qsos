<?php
include("config.php");
$output = new DOMDocument();

/*
function newtreeitem($path, $file) {
	global $output;

	$treeitem = $output->createElement("treeitem");
	$treeitem->setAttribute("container", "true");
	$treeitem->setAttribute("open", "true");
	$treerow = $output->createElement("treerow");
	$treecell = $output->createElement("treecell");
	$treecell->setAttribute("id", "http://localhost:88/test/OOo/".$path);
	$treecell->setAttribute("label", $file);
	$treerow->appendChild($treecell);
	$treeitem->appendChild($treerow);
	return $treeitem;
}
*/

function buildtree($path) {
	global $output;
	global $delim;

	$children = $output->createElement("children");
	if (is_dir($path) && $dh = opendir($path)) {
		while (($file = readdir($dh)) !== false) {
			$subpath = $path.$delim.$file;
			if (is_dir($subpath) && ($file != 'CVS') && ($file != '.') && ($file != '..') && ($file != 'include') && ($file != 'template') && ($file != 'templates') && ($file != '.svn')) {
				$newtreeitem = $output->createElement("item");
				$newtreeitem->setAttribute("id", $subpath);
				$newtreeitem->setAttribute("label", $file);
				$newtreeitem->appendChild(buildtree($subpath, $file));
				$children->appendChild($newtreeitem);
			} elseif (substr($file, -5) == ".qsos") {
				$newtreeitem = $output->createElement("item");
				$newtreeitem->setAttribute("id", "http://localhost:88/test/OOo/".$subpath);
				$newtreeitem->setAttribute("label", $file);
				$children->appendChild($newtreeitem);
			}
		}
		closedir($dh);
	}
	return $children;
}

$doc = $output->createElement("Document");
$output->appendChild(buildtree($sheet));

header('Content-type: text/xml');
echo $output->saveXML();

?>