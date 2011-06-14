/*
**  Copyright (C) 2006 Atos Origin
**
**  Author: Raphaël Semeteys <raphael.semeteys@atosorigin.com>
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
** authors.js: functions associated with the authors.xul file
**
*/

var myDoc;
var docChanged;

function init() {
	docChanged = "false";
	myDoc = window.arguments[0];
	var authors = myDoc.getauthors();
	var mylist = document.getElementById("f-a-list");
	for(var i=0; i < authors.length; i++) {
		var listitem = document.createElement("listitem");
		listitem.setAttribute("label", authors[i].name);
		listitem.setAttribute("value", authors[i].email);
		mylist.appendChild(listitem);
	}
}

function changeAuthor(author) {
	document.getElementById("f-a-name").value = author.label;
	document.getElementById("f-a-email").value = author.value;
}

function addAuthor() {
	var mylist = document.getElementById("f-a-list");
	var listitem = document.createElement("listitem");
	listitem.setAttribute("label", document.getElementById("f-a-name").value);
	listitem.setAttribute("value", document.getElementById("f-a-email").value);
	mylist.appendChild(listitem);
	myDoc.addauthor(document.getElementById("f-a-name").value, document.getElementById("f-a-email").value);
	docChanged = "true";
}

function deleteAuthor() {
	var mylist = document.getElementById("f-a-list");
	mylist.removeChild(mylist.selectedItem);
	alert(document.getElementById("f-a-name").value);
	myDoc.delauthor(document.getElementById("f-a-name").value);
	document.getElementById("f-a-name").value = "";
	document.getElementById("f-a-email").value = "";
	docChanged = "true";
}

function doOK() {
	//Call window opener callback function
	if (docChanged == "true")
		window.arguments[1](myDoc);
	else
		window.arguments[1]("null");
	return true;
}
