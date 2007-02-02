<?php
/*
**  Copyright (C) 2007 Atos Origin 
**
**  Author: Raphael Semeteys <raphael.semeteys@atosorigin.com>
**
**  This program is free software; you can redistribute it and/or modify
**  it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
**  This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
**  along with this program; if not, write to the Free Software
**  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
**
**
** QSOSDocument.php: PHP classes to access and manipulate QSOS documents
**
*/

//Class representing a QSOS criterion (<section/> or <element/>)
class QSOSCriterion {
	var $name;
	var $title;
	var $children;
	var $score;
}

//Class representing a QSOS author (<author/>)
class Author {
	var $name;
	var $email;
}

//Class representing a QSOS document
class QSOSDocument {
	var $doc;
	var $xpath;

    //$file: filename of the QSOS document to load
	function __construct($file) {
		if (file_exists($file)) {
			$this->doc = new DOMDocument();
			$this->doc->load($file);
			$this->xpath = new DOMXPath($this->doc);
		} else {
			return 'Failed to open file '.$file;
		}
	}

    //$name: name of the tested element
    //Returns: true if element has children elements
	public function hassubelements($name) {
		$query = "//*[@name='".$name."']/element";
		$nb = $this->xpath->query($query);
		return $nb->length;
	}

    //$element: name of the XML header tag
    //Returns: the value of a header tag (like appname, release, ...)
	public function getkey($element) {
		$nodes = $this->xpath->query("//".$element);
		if ($nodes->length != 0) {
			return $nodes->item(0)->nodeValue;
		} else {
			return "";
		}
	}
	
    //$element: name of the element
    //$subelement: name of the XML tag
    //Returns: value of the XML tag included in the element
	public function getgeneric($element, $subelement) {
		$nodes = $this->xpath->query("//*[@name='".$element."']/".$subelement);
		if ($nodes->length != 0) {
			return $nodes->item(0)->nodeValue;
		} else {
			return "";
		}
	}

    //$element: name of the element
    //Returns: value of the <score/> tag included in the element
	public function getkeyscore($element) {
		$nodes = $this->xpath->query("//*[@name='".$element."']/score");
		if ($nodes->length != 0) {
			return $nodes->item(0)->nodeValue;
		} else {
			return -1;
		}
	}

    //$element: name of the element (<section/> or <element/>)
    //Returns: value of the "title" attribute of the element
	public function getkeytitle($element) {
		$nodes = $this->xpath->query("//*[@name='".$element."']");
		if ($nodes->length != 0) {
			return $nodes->item(0)->getAttribute('title');
		} else {
			return "";
		}
	}

    //Returns: array of Author objects (cf. Author class above)
	public function getauthors() {
		$authors = array();	

		$nodes = $this->xpath->query("//author");
		for ($i=0; $i < $nodes->length; $i++) {
			$author = new Author();

			$names = $nodes->item($i)->getElementsByTagName("name");
			if ($names->length > 0) {
				$author->name = $names->item(0)->textContent;
			} else {
				$author->name = "";
			}

			$titles = $nodes->item($i)->getElementsByTagName("email");
			if ($titles->length > 0) {
				$author->email = $titles->item(0)->textContent;
			} else {
				$author->email = "";
			}
			array_push($authors, $author);
		}

		return $authors;
	}

    //Returns the name of a criterion's parent
	function getParent($name) {
		$nodes = $this->xpath->query("//*[@name='".$name."']");
		if ($nodes->length > 0) {
			return $nodes->item(0)->parentNode;
		}
		else {
			return null;
		}
	}

    //Returns: tree of QSOSCriterion objects representing the scored criteria of the QSOS document
	public function getTree() {
		$tree = array();
		$sections = $this->xpath->query("//section");
		foreach ($sections as $section) {
			$criterion = new QSOSCriterion();
			$criterion->name = $section->getAttribute('name');
			$criterion->title = $section->getAttribute('title');
			$criterion->children = $this->getSubTree($criterion->name);
			$criterion->score = $this->renderScore($criterion->children);
			array_push($tree, $criterion);
		}
		return $tree;
	}

    //Recursive function
    //$name: name of the element
    //Returns: tree of QSOSCriterion objects representing the scored criteria of the element
	public function getSubTree($name) {
		$tree = array();
		$elements = $this->xpath->query("//*[@name='".$name."']/element");
		foreach ($elements as $element) {
			$criterion = new QSOSCriterion();
			$criterion->name = $element->getAttribute('name');
			$criterion->title = $element->getAttribute('title');

			if ($this->hassubelements($criterion->name)) {
				$criterion->children = $this->getSubTree($criterion->name);
				$criterion->score = $this->renderScore($criterion->children);
				array_push($tree, $criterion);
			} else {
				$criterion->children = null;
				$criterion->score = $this->getkeyscore($criterion->name);
				if ($criterion->score == "") $criterion->score = null;
				if ($criterion->score != -1) array_push($tree, $criterion);
			}
		}
		return $tree;
	}

    //Returns: tree of QSOSCriterion objects representing the scored criteria of the QSOS document
	public function getWeightedTree($weights) {
		$tree = array();
		$sections = $this->xpath->query("//section");
		foreach ($sections as $section) {
			$criterion = new QSOSCriterion();
			$criterion->name = $section->getAttribute('name');
			$criterion->title = $section->getAttribute('title');
			$criterion->children = $this->getWeightedSubTree($criterion->name, $weights);
			$criterion->score = $this->renderWeightedScore($criterion->children, $weights);
			array_push($tree, $criterion);
		}
		return $tree;
	}

    //Recursive function
    //$name: name of the element
    //Returns: tree of QSOSCriterion objects representing the scored criteria of the element
	public function getWeightedSubTree($name, $weights) {
		$tree = array();
		$elements = $this->xpath->query("//*[@name='".$name."']/element");
		foreach ($elements as $element) {
			$criterion = new QSOSCriterion();
			$criterion->name = $element->getAttribute('name');
			$criterion->title = $element->getAttribute('title');

			if ($this->hassubelements($criterion->name)) {
				$criterion->children = $this->getWeightedSubTree($criterion->name, $weights);
				$criterion->score = $this->renderWeightedScore($criterion->children, $weights);
				array_push($tree, $criterion);
			} else {
				$criterion->children = null;
				$criterion->score = $this->getkeyscore($criterion->name);
				if ($criterion->score == "") $criterion->score = null;
				if ($criterion->score != -1) array_push($tree, $criterion);
			}
		}
		return $tree;
	}

    //$tree: tree of QSOSCriterion objects to render
    //Returns: the rendered score of the single QSOScriterion in $tree
    //Recursive function
	public function renderScore($tree) {
		$score = 0;
		$sum = 0;
		$totalWeight = 0;

		//[FIXME] desc element with only desc subelement(s) shoul be properly managed
		if (count($tree) == 0) return "NA";

		for ($i=0; $i < count($tree); $i++) {
			$totalWeight++;
			if ($tree[$i]->score == null) {
				$isRenderable = false;
			}
			$sum += round($tree[$i]->score, 2);
		}

		$score = round(($sum/$totalWeight), 2);
		
		return $score;
	}

    //$tree: tree of QSOSCriterion objects to render
    //Returns: the rendered score of the single QSOScriterion in $tree
    //Recursive function
	public function renderWeightedScore($tree, $weights) {
		$score = 0;
		$sum = 0;
		$totalWeight = 0;

		//[FIXME] desc element with only desc subelement(s) shoul be properly manage
		if (count($tree) == 0) return "NA";

		for ($i=0; $i < count($tree); $i++) {
			$name = $tree[$i]->name;
			$weight = $weights[$name];
			$totalWeight = $totalWeight + $weight;
			if ($tree[$i]->score == null) {
				$isRenderable = false;
			}
			$sum += round(($tree[$i]->score)*$weight, 2);
		}

		$score = round(($sum/$totalWeight), 2);
		
		return $score;
	}
}
?>