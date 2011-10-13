/*
 * *  Copyright (C) 2006-2011 Atos Origin
 **
 **  Author: Raphael Semeteys <raphael.semeteys@atosorigin.com>
 **          Timothée Ravier <timothee.ravier@atosorigin.com>
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
 **  QSOS XUL Editor
 **  new.js: functions associated with the new file dialog
 **
 */

var xmlDoc;


function init() {
  try {
    netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
  } catch (e) {
    alert("newFile: Permission to open file denied: " + e.message);
    return false;
  }

  var prefManager = Components.classes["@mozilla.org/preferences-service;1"]
    .getService(Components.interfaces.nsIPrefBranch);
  var loadremote = prefManager.getCharPref("extensions.qsos-xuled.loadremote-tpl");

  req = new XMLHttpRequest();
  req.open('GET', loadremote, false);
  //req.overrideMimeType('text/xml');
  req.send(null);

  var domParser = new DOMParser();
  xmlDoc = domParser.parseFromString(req.responseText, "text/xml");

  var evalTree = document.getElementById("evalTree");

  var treechildren = getlist();
  evalTree.appendChild(treechildren);
}


function doOK() {
  var evalTree = document.getElementById("evalTree");
  var url = evalTree.view.getItemAtIndex(evalTree.currentIndex).firstChild.firstChild.getAttribute("id");

  if (url.substr(0, 7) != "http://") url = "";
  //Call window opener callback function
  window.arguments[1](url);
}


function getlist() {
  var treechildren = document.createElement("treechildren");
  treechildren.setAttribute("id", "myTreechildren");

  var items = xmlDoc.evaluate("/templates/item", xmlDoc, null, XPathResult.ANY_TYPE,null);
  var item = items.iterateNext();
  while (item) {
    var treeitem = document.createElement("treeitem");
    //treeitem.setAttribute("container", "true");
    //treeitem.setAttribute("open", "true");
    var treerow = document.createElement("treerow");
    var treecell = document.createElement("treecell");
    treecell.setAttribute("id", item.getAttribute("id"));
    treecell.setAttribute("label", item.getAttribute("label"));
    treerow.appendChild(treecell);
    treeitem.appendChild(treerow);
    treechildren.appendChild(treeitem);
    item = items.iterateNext();
  }
  return treechildren;
}
