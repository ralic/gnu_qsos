/*
**  Copyright (C) 2006, 2007 Atos Origin
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
** QSOS XUL Editor
** load.js: functions associated with the load remote file dialog
*/

//QSOS backend containing the list of available evaluations
var xmlDoc;

//Connection to QSOS backend and generation of the tree view
function init() {
  try {
    netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
  } catch (e) {
    alert("Permission to open file was denied.");
  }

  var prefManager = Components.classes["@mozilla.org/preferences-service;1"]
          .getService(Components.interfaces.nsIPrefBranch);
  var loadremote = prefManager.getCharPref("extensions.qsos-xuled.loadremote");

  req = new XMLHttpRequest();
  req.open('GET', loadremote, false);
  //req.overrideMimeType('text/xml');
  req.send(null);

  var domParser = new DOMParser();
  xmlDoc = domParser.parseFromString(req.responseText, "text/xml");

  var criteria = getcomplextree();

  var evalTree = document.getElementById("evalTree");

  var treechildren = buildtree(criteria);
  evalTree.appendChild(treechildren);
}

//Generates a XUL tree from QSOS backend's list
function getcomplextree() {
  var criteria = new Array();
  var items = xmlDoc.evaluate("/children/item", xmlDoc, null, XPathResult.ANY_TYPE,null);
  var item = items.iterateNext();
  while (item) {
    var criterion = new Object();
    criterion.id = item.getAttribute("id");
    criterion.label =  item.getAttribute("label");
    criterion.children = getsubcriteria(criterion.id);
    criteria.push(criterion);
    item = items.iterateNext();
  }
  return criteria;
}

//Recursive function used by getcomplextree()
function getsubcriteria(id) {
  var subcriteria = new Array();
  var items = xmlDoc.evaluate("//*[@id='"+id+"']/children/item", xmlDoc, null, XPathResult.ANY_TYPE,null);
  var item = items.iterateNext();
  while (item) {
    var criterion = new Object();
    criterion.id = item.getAttribute("id");
    criterion.label =  item.getAttribute("label");
    criterion.children = getsubcriteria(criterion.id);
    subcriteria.push(criterion);
    item = items.iterateNext();
  }
  if (subcriteria.length > 0) {
    return subcriteria;
  } else {
    return "null";
  }
}

//XUL Tree recursive creation function
function buildtree(criteria) {
  var treechildren = document.createElement("treechildren");
  treechildren.setAttribute("id", "myTreechildren");
  for (var i=0; i < criteria.length; i++) {
    treeitem = newtreeitem(criteria[i]);
    treechildren.appendChild(treeitem);
  }
  return treechildren;
}

//XUL Tree recursive creation function
function newtreeitem(criterion) {
  var treeitem = document.createElement("treeitem");
  if (criterion.label.substr(-5) != ".qsos") {
    treeitem.setAttribute("container", "true");
    treeitem.setAttribute("open", "false");
  }
  var treerow = document.createElement("treerow");
  var treecell = document.createElement("treecell");
  treecell.setAttribute("id", criterion.id);
  treecell.setAttribute("label", criterion.label);
  treerow.appendChild(treecell);
  treeitem.appendChild(treerow);
  if (criterion.children != "null") treeitem.appendChild(buildsubtree(criterion.children));
  return treeitem;
}

//XUL Tree recursive creation function
function buildsubtree(criteria) {
  var treechildren = document.createElement("treechildren");
  for (var i=0; i < criteria.length; i++) {
    treeitem = newtreeitem(criteria[i]);
    treechildren.appendChild(treeitem);
  }
  return treechildren;
}

//Check if double click should fire something
function checkLabel() {
  var evalTree = document.getElementById("evalTree");
  var label = evalTree.view.getItemAtIndex(evalTree.currentIndex).firstChild.firstChild.getAttribute("label");
  if (label.substr(-5) == ".qsos") {
    document.getElementById("Load").acceptDialog();
  }
}

//Dialog's validation
function doOK() {
  var evalTree = document.getElementById("evalTree");
  var url = evalTree.view.getItemAtIndex(evalTree.currentIndex).firstChild.firstChild.getAttribute("id");

  if (url.substr(0, 7) != "http://") url = "";
  //Call window opener callback function
  window.arguments[1](url);
}