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
** editor.js: functions associated with the editor.xul file
**
** TODO:
**	- Load remote QSOS XML file
*/

//Object "Document" representing data in the QSOS XML file
var myDoc;
//Indicator of document modification 
var docChanged;
//id (actually "name" in the QSOS XML file) of the currently selected criteria in the tree
var id;

//Window initialization after loading
function init() {
	docChanged = "false";
	freezeGeneric("true");
	freezeType("true");
	freezeDesc("true");
	freezeScores("true");
	//Menu management
	document.getElementById("file-save").setAttribute("disabled", "true");
	document.getElementById("file-saveas").setAttribute("disabled", "true");
	document.getElementById("file-close").setAttribute("disabled", "true");
}

////////////////////////////////////////////////////////////////////
// Menu "File" functions
////////////////////////////////////////////////////////////////////

//////////////////////////
//Submenu "File/Open"
//////////////////////////
//Opens a local QSOS XML file and populates the window (tree and generic fields)
function openFile() {
    try {
        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
    } catch (e) {
        alert("Permission to open file was denied.");
    }
    var nsIFilePicker = Components.interfaces.nsIFilePicker;
    var fp = Components.classes["@mozilla.org/filepicker;1"]
            .createInstance(nsIFilePicker);
    fp.init(window, "Select a file", nsIFilePicker.modeOpen);
    fp.appendFilter("QSOS file","*.qsos");
    var res = fp.show();
    
    if (res == nsIFilePicker.returnOK) {
        myDoc = new Template();
        myDoc.load(fp.file.path);
        
        //Window's title
        document.getElementById("QSOS").setAttribute("title", "QSOS template: "+myDoc.getqsosappfamily());
        
        //Tree population
        var tree = document.getElementById("mytree");
	var treechildren = buildtree();
        tree.appendChild(treechildren);
	
        //Other fields
	document.getElementById("f-softwarefamily").value = myDoc.getqsosappfamily();
	document.getElementById("f-version").value = myDoc.getqsosspecificformat();
        
        freezeGeneric("");
	//Menu management
        document.getElementById("file-close").setAttribute("disabled", "false");
	document.getElementById("file-saveas").setAttribute("disabled", "false");
    }
}

//Checks Document's state before opening a new one
function checkopenFile() {
	if (myDoc) {
		if (docChanged == "true") {
			confirmDialog("Document has been modified but not saved, close it anyway?", closeFile);
		}
		else {
			closeFile();
		}
	}
	openFile();
}

//XUL Tree recursive creation function
function buildtree() {
	var treechildren = document.createElement("treechildren");
	treechildren.setAttribute("id", "myTreechildren");
	var criteria = myDoc.getcomplextree();
	for (var i=0; i < criteria.length; i++) {
		treeitem = newtreeitem(criteria[i]);
		treechildren.appendChild(treeitem);
	}
	return treechildren;
}

//XUL Tree recursive creation function
function newtreeitem(criterion) {
	var treeitem = document.createElement("treeitem");
	treeitem.setAttribute("container", "true");
	treeitem.setAttribute("open", "true");
	var treerow = document.createElement("treerow");
	var treecell = document.createElement("treecell");
	treecell.setAttribute("id", criterion.name);
	treecell.setAttribute("label", criterion.title);
	treerow.appendChild(treecell);
	treeitem.appendChild(treerow);
	if (criterion.children != "null")
		treeitem.appendChild(buildsubtree(criterion.children));
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

//////////////////////////
//Submenu "File/Save"
//////////////////////////
//Saves modifications to the QSOS XML file
function saveFile() {
    if (myDoc) {
    	myDoc.write();
	    docChanged = "false";
	    //Menu management
        document.getElementById("file-save").setAttribute("disabled", "true");
    }
}

//////////////////////////
//Submenu "File/Save As"
//////////////////////////
//Saves modifications to a new QSOS XML file
function saveFileAs() {
	try {
		netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	} catch (e) {
		alert("Permission to open file was denied.");
	}
	var nsIFilePicker = Components.interfaces.nsIFilePicker;
	var fp = Components.classes["@mozilla.org/filepicker;1"]
		.createInstance(nsIFilePicker);
	fp.init(window, "Save the file as", nsIFilePicker.modeSave);
	fp.appendFilter("QSOS file","*.qsos");
	var res = fp.show();
	if (res == nsIFilePicker.returnOK) {
		myDoc.setfilename(fp.file.path);
		myDoc.write();
		docChanged = "false";
	}
}

//////////////////////////
//Submenu "File/Close"
//////////////////////////
//Closes the QSOS XML file and resets window
function closeFile() {
	document.getElementById("QSOS").setAttribute("title", "QSOS XUL Editor");
	document.getElementById("f-softwarefamily").value = "";
	document.getElementById("f-version").value = "";

	document.getElementById("g-c-id").setAttribute("myid", "");
	document.getElementById("f-c-desc").value = "";
	document.getElementById("f-c-score0").value = "";
	document.getElementById("f-c-score1").value = "";
	document.getElementById("f-c-score2").value = "";
    
	init();
	myDoc = null;
	id = null;
	
	var tree = document.getElementById("mytree");
	var treechildren = document.getElementById("myTreechildren");
	tree.removeChild(treechildren);
}

//Checks Document's state before closing it
function checkcloseFile() {
	if (docChanged == "true") {
		confirmDialog("Document has been modified, save it before?", saveFile);
	}
	closeFile();
}

//////////////////////////
//Submenu "File/Exit"
//////////////////////////
//Exits application
function exit() {
	self.close();
}

//Checks Document's state before exiting
function checkexit() {
	if (docChanged == "true") {
		confirmDialog("Document has been modified but not saved, exit anyway?", exit);
		return;
	}
	else {
		exit();
	}
}

////////////////////////////////////////////////////////////////////
// Menu "Tree" function
////////////////////////////////////////////////////////////////////

//Submenus "Tree/Expand All" and "Tree/Collapse All"
//Expands or collapses the tree
//bool: "false" dans collapse, "true" to expand
function expandTree(bool) {
	var treeitems = document.getElementsByTagName("treeitem");
	for (var i = 0; i < treeitems.length ; i++) {
		var children = treeitems[i].getElementsByTagName("treeitem");
		if (children.length > 0) treeitems[i].setAttribute("open", bool);
        }
}

////////////////////////////////////////////////////////////////////
// Menu "Help" function
////////////////////////////////////////////////////////////////////

//Submenu "Help/About"
//Shows the about.xul window in modal mode
function aboutDialog() {
    try {
        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
    } catch (e) {
        alert("Permission to open file was denied.");
    }
    window.openDialog('chrome://qsos-tpl-xuled/content/about.xul','About','chrome,dialog,modal');
}

////////////////////////////////////////////////////////////////////
// Helper functions
////////////////////////////////////////////////////////////////////

//Generic call to a confirmation dialog window in modal mode
//content: question to be asked ti the user
//doaction: callback function to trigger if user answers "yes" to the question
function confirmDialog(content, doaction) {
	try {
	    netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	} catch (e) {
	    alert("Permission to open file was denied.");
	}
	window.openDialog('chrome://qsos-tpl-xuled/content/confirm.xul','Confirm','chrome,dialog,modal',content,doaction);
}

//(Un)freezes generic template fields (template properties)
//bool: "true" to freeze; "" to unfreeze
function freezeGeneric(bool) {
	document.getElementById("f-softwarefamily").disabled = bool;
	document.getElementById("f-version").disabled = bool;
}

//(Un)freezes type fields (criterion properties)
//bool: "true" to freeze; "" to unfreeze
function freezeType(bool) {
	document.getElementById("f-c-type").selectedIndex = -1;
	document.getElementById("f-c-type").disabled = bool;
}

//(Un)freezes description field (criterion properties)
//bool: "true" to freeze; "" to unfreeze
function freezeDesc(bool) {
	document.getElementById("f-c-desc").value = "";
	document.getElementById("f-c-desc").disabled = bool;
}

//(Un)freezes scores fields (criterion properties)
//bool: "true" to freeze; "" to unfreeze
function freezeScores(bool) {
	document.getElementById("f-c-score0").value = "";
	document.getElementById("f-c-score1").value = "";
	document.getElementById("f-c-score2").value = "";

	document.getElementById("f-c-score0").disabled = bool;
	document.getElementById("f-c-score1").disabled = bool;
	document.getElementById("f-c-score2").disabled = bool;
}

////////////////////////////////////////////////////////////////////
// Event functions
////////////////////////////////////////////////////////////////////

//Triggered when a new criterion is selected in the tree
//Fills criteria's fields with new values
function treeselect(tree) {
	//Forces focus to trigger possible onchange event on another XUL element
	document.getElementById("mytree").focus();
	id = tree.view.getItemAtIndex(tree.currentIndex).firstChild.firstChild.getAttribute("id");
	document.getElementById("g-c-id").setAttribute("myid", id);

	//TODO: title
	document.getElementById("f-c-title").value = myDoc.getkeytitle(id);
	if (myDoc.hassubelements(id)) freezeType("true");
	else freezeType("");
	switch (myDoc.getNodeType(id)) {
		case "section":
			freezeType("true");
			freezeDesc("");
			document.getElementById("f-c-desc").value = myDoc.getkeydesc(id);
			freezeScores("true");
			break;
		case "info":
			document.getElementById("f-c-type").selectedIndex = 0;
			freezeDesc("");
			document.getElementById("f-c-desc").value = myDoc.getkeydesc(id);
			freezeScores("true");
			break;
		case "score":
			document.getElementById("f-c-type").selectedIndex = 1;
			freezeScores("");
			document.getElementById("f-c-score0").value = myDoc.getkeydesc0(id);
			document.getElementById("f-c-score1").value = myDoc.getkeydesc1(id);
			document.getElementById("f-c-score2").value = myDoc.getkeydesc2(id);
			freezeDesc("true");
			break;
	}
}

//Triggered when software family is modified
function changeSoftwareFamily(xulelement) {
	docChanged = "true";
	myDoc.setqsosappfamily(xulelement.value);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when template version is modified
function changeVersion(xulelement) {
	docChanged = "true";
	myDoc.setqsosspecificformat(xulelement.value);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when current criteria's description is modified
function changeDesc(xulelement) {
	docChanged = "true";
	myDoc.setkeydesc(id, xulelement.value);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when current criteria's score 0 description is modified
function changeScore0(score) {
	docChanged = "true";
	myDoc.setkeydesc0(id, score);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when current criteria's score 1 description is modified
function changeScore1(score) {
	docChanged = "true";
	myDoc.setkeydesc1(id, score);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when current criteria's score 2 description is modified
function changeScore2(score) {
	docChanged = "true";
	myDoc.setkeydesc2(id, score);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when current criteria's type is modified
function changeType(type) {
	docChanged = "true";
	switch (type) {
		case "desc":
			freezeScores("true");
			myDoc.setElementDesc(id);
			freezeDesc("");
			break;
		case "score":
			freezeDesc("true");
			myDoc.setElementScore(id);
			freezeScores("");
			break;
	}
	document.getElementById("file-save").setAttribute("disabled", "false");
}

function displayPopup() {
	var menuSection = document.getElementById("element-new-section");
	var menuDesc = document.getElementById("element-new-desc");
	var menuScore = document.getElementById("element-new-score");
	switch (myDoc.getNodeType(id)) {
		case "section":
			menuSection.setAttribute("disabled", "false");
			menuDesc.setAttribute("disabled", "false");
			menuScore.setAttribute("disabled", "false");
			break;
		case "info":
			menuSection.setAttribute("disabled", "true");
			menuDesc.setAttribute("disabled", "false");
			menuScore.setAttribute("disabled", "false");
			break;
		case "score":
			menuSection.setAttribute("disabled", "true");
			menuDesc.setAttribute("disabled", "true");
			menuScore.setAttribute("disabled", "true");
			break;
	}
}

function newSection() {
	var sectionId = prompt("Enter an id the new section", "");
	alert(sectionId);
}

////////////////////////////////////////////////////////////////////
// New criteria creation
////////////////////////////////////////////////////////////////////

//Callback function of the newdesc.xul dialog window
function newDesc(values) {
	//Creates new Information element
	var criterion = myDoc.createElementDesc(values.name, values.title, values.desc);
	myDoc.insertSubelement(criterion, id);

	//Creates new tree entry
	var treeitem = document.createElement("treeitem");
	treeitem.setAttribute("container", "true");
	treeitem.setAttribute("open", "true");
	var treerow = document.createElement("treerow");
	var treecell = document.createElement("treecell");
	treecell.setAttribute("id", values.name);
	treecell.setAttribute("label", values.title);
	treerow.appendChild(treecell);
	treeitem.appendChild(treerow);

	var tree = document.getElementById("mytree");
	var treechildren = tree.view.getItemAtIndex(tree.currentIndex).getElementsByTagName("treechildren");

	if (treechildren.length > 0) {
		//Parent already has children
		treechildren[0].appendChild(treeitem);
	}
	else {
		//Creates the first child
		var newchildren = document.createElement("treechildren");
		newchildren.appendChild(treeitem);
		tree.view.getItemAtIndex(tree.currentIndex).appendChild(newchildren);
	}
}

function openDescDialog() {
        try {
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        } catch (e) {
            alert("Permission to read file was denied.");
        }
	window.openDialog('chrome://qsos-tpl-xuled/content/newdesc.xul','New criterion','chrome,dialog,modal', myDoc, newDesc);
}

//Callback function of the newdesc.xul dialog window
function newScore(values) {
	//Creates new Information element
	var criterion = myDoc.createElementScore(values.name, values.title, values.desc0, values.desc1, values.desc2);
	myDoc.insertSubelement(criterion, id);

	//Creates new tree entry
	var treeitem = document.createElement("treeitem");
	treeitem.setAttribute("container", "true");
	treeitem.setAttribute("open", "true");
	var treerow = document.createElement("treerow");
	var treecell = document.createElement("treecell");
	treecell.setAttribute("id", values.name);
	treecell.setAttribute("label", values.title);
	treerow.appendChild(treecell);
	treeitem.appendChild(treerow);

	var tree = document.getElementById("mytree");
	var treechildren = tree.view.getItemAtIndex(tree.currentIndex).getElementsByTagName("treechildren");

	if (treechildren.length > 0) {
		//Parent already has children
		treechildren[0].appendChild(treeitem);
	}
	else {
		//Creates the first child
		var newchildren = document.createElement("treechildren");
		newchildren.appendChild(treeitem);
		tree.view.getItemAtIndex(tree.currentIndex).appendChild(newchildren);
	}
}

function openScoreDialog() {
        try {
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        } catch (e) {
            alert("Permission to read file was denied.");
        }
	window.openDialog('chrome://qsos-tpl-xuled/content/newscore.xul','New criterion','chrome,dialog,modal', newScore);
}
