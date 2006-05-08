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
**	- Write a XMLSerializer to manage identation and generate <tag></tag> rather than <tag/>
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
//Creates a new local QSOS XML file and populates the window (tree and generic fields)
function newFile() {
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
		myDoc = new Template();
		myDoc.create(fp.file.path);
	
		//Window's title
		document.getElementById("QSOS").setAttribute("title", "New QSOS template");
		
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

//Checks Document's state before creating a new one
function checknewFile() {
	if (myDoc) {
		if (docChanged == "true") {
			confirmDialog("Document has been modified but not saved, close it anyway?", closeFile);
		}
		else {
			closeFile();
		}
	}
	newFile();
}

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
		if (i != 0) {
			treeitem = newtreeitem(criteria[i]);
			treechildren.appendChild(treeitem);
		}
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
	document.getElementById("element-popup").setAttribute("disabled", "true");
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

//(Un)freezes title field (criterion properties)
//bool: "true" to freeze; "" to unfreeze
function freezeTitle(bool) {
	document.getElementById("f-c-title").disabled = bool;
}

//(Un)freezes type fields (criterion properties)
//bool: "true" to freeze; "" to unfreeze
function freezeType(bool) {
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

//Triggered when an entry is selected in the tree
//Fills criteria's fields with new values
function treeselect(tree) {
	//Forces focus to trigger possible onchange event on another XUL element
	document.getElementById("mytree").focus();

	try {
		id = tree.view.getItemAtIndex(tree.currentIndex).firstChild.firstChild.getAttribute("id");
	} catch (e) {}

	document.getElementById("g-c-id").setAttribute("myid", id);
	
	if (myDoc.hassubelements(id)) freezeType("true");
	else freezeType("");
	switch (myDoc.getNodeType(id)) {
		case "section":
			document.getElementById("f-c-type").selectedIndex = -1;
			freezeType("true");
			freezeDesc("");
			freezeTitle("");
			document.getElementById("f-c-name").value = "UID: "+id;
			document.getElementById("f-c-title").value = myDoc.getkeytitle(id);
			document.getElementById("f-c-desc").value = myDoc.getkeydesc(id);
			freezeScores("true");
			break;
		case "info":
			document.getElementById("f-c-type").selectedIndex = 0;
			freezeDesc("");
			freezeTitle("");
			document.getElementById("f-c-name").value = "UID: "+id;
			document.getElementById("f-c-title").value = myDoc.getkeytitle(id);
			document.getElementById("f-c-desc").value = myDoc.getkeydesc(id);
			freezeScores("true");
			break;
		case "score":
			document.getElementById("f-c-type").selectedIndex = 1;
			freezeDesc("");
			freezeScores("");
			freezeTitle("");
			document.getElementById("f-c-name").value = "UID: "+id;
			document.getElementById("f-c-title").value = myDoc.getkeytitle(id);
			document.getElementById("f-c-desc").value = myDoc.getkeydesc(id);
			document.getElementById("f-c-score0").value = myDoc.getkeydesc0(id);
			document.getElementById("f-c-score1").value = myDoc.getkeydesc1(id);
			document.getElementById("f-c-score2").value = myDoc.getkeydesc2(id);
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

//Triggered when current criteria's title is modified
function changeTitle(xulelement) {
	docChanged = "true";
	myDoc.setkeytitle(id, xulelement.value);
	document.getElementById("file-save").setAttribute("disabled", "false");
	document.getElementById(id).setAttribute("label", xulelement.value);
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
			document.getElementById("f-c-desc").value = "";
			freezeScores("true");
			myDoc.setElementDesc(id);
			break;
		case "score":
			document.getElementById("f-c-desc").value = "";
			myDoc.setElementScore(id);
			freezeScores("");
			break;
	}
	document.getElementById("file-save").setAttribute("disabled", "false");
}

////////////////////////////////////////////////////////////////////
// Popup menu functions
////////////////////////////////////////////////////////////////////

//Determines which menu entries are (dis)abled
function displayPopup() {
	var menuSection = document.getElementById("element-new-section");
	var menuDesc = document.getElementById("element-new-desc");
	var menuScore = document.getElementById("element-new-score");
	var menuDelete = document.getElementById("element-delete");
	var menuMoveUp = document.getElementById("element-moveup");
	var menuMoveDown = document.getElementById("element-movedown");
	
	if (myDoc == null) {
		menuSection.setAttribute("disabled", "true");
		menuDesc.setAttribute("disabled", "true");
		menuScore.setAttribute("disabled", "true");
		menuDelete.setAttribute("disabled", "true");
		menuMoveUp.setAttribute("disabled", "true");
		menuMoveDown.setAttribute("disabled", "true");
	}
	else {
		if (!id) {
			menuSection.setAttribute("disabled", "false");
			menuDesc.setAttribute("disabled", "true");
			menuScore.setAttribute("disabled", "true");
			menuDelete.setAttribute("disabled", "true");
			menuMoveUp.setAttribute("disabled", "true");
			menuMoveDown.setAttribute("disabled", "true");
		}
		else {
			menuDelete.setAttribute("disabled", "false");
			menuMoveUp.setAttribute("disabled", "false");
			menuMoveDown.setAttribute("disabled", "false");
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
	}
}

//Callback function of the newsection.xul dialog window
function newSection(values) {
	//Creates new section element
	var section = myDoc.createSection(values.name, values.title, values.desc);
	myDoc.insertSection(section);
	
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
	
	document.getElementById("mytree").lastChild.appendChild(treeitem);
	document.getElementById("mytree").focus();
	
	document.getElementById("file-save").setAttribute("disabled", "false");
	docChanged = "true";
}

//Context menu "New section"
function openSectionDialog() {
	try {
		netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	} catch (e) {
		alert("Permission to read file was denied.");
	}
	window.openDialog('chrome://qsos-tpl-xuled/content/newsection.xul','New section','chrome,dialog,modal', myDoc, newSection);
}

//Callback function of the newdesc.xul dialog window
function newDesc(values) {
	docChanged = "true";
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

	document.getElementById("file-save").setAttribute("disabled", "false");
	docChanged = "true";
}

//Context menu "New information criterion"
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
	//Creates new Score element
	var criterion = myDoc.createElementScore(values.name, values.title, values.desc, values.desc0, values.desc1, values.desc2);
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

	//Adds it to the tree
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
	
	document.getElementById("file-save").setAttribute("disabled", "false");
	docChanged = "true";
}

//Context menu "New scored criterion"
function openScoreDialog() {
        try {
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        } catch (e) {
            alert("Permission to read file was denied.");
        }
	window.openDialog('chrome://qsos-tpl-xuled/content/newscore.xul','New criterion','chrome,dialog,modal', myDoc, newScore);
}

//Context menu "Move Up"
function moveUp() {
	var tree = document.getElementById("mytree");
	var currentItem = tree.view.getItemAtIndex(tree.currentIndex);

	//Close siblings that have children
	var siblings = currentItem.parentNode.getElementsByTagName("treeitem");
	for (i=0; i < siblings.length; i++) {
		if (siblings[i].getElementsByTagName("treechildren").length > 0)
			siblings[i].setAttribute("open", "false");
	}

	var previousItem = tree.view.getItemAtIndex(tree.currentIndex - 1);

	//Movement only allowed in the same branch of the tree
	if (previousItem.parentNode == currentItem.parentNode) {
		previousItem.parentNode.insertBefore(currentItem, previousItem);

		var currentId = currentItem.firstChild.firstChild.getAttribute("id");
		var previousId = previousItem.firstChild.firstChild.getAttribute("id");
		myDoc.insertNodeBefore(currentId, previousId);

		document.getElementById("file-save").setAttribute("disabled", "false");
		docChanged = "true";
	}
}

//Context menu "Move Down"
function moveDown() {
	var tree = document.getElementById("mytree");
	var currentItem = tree.view.getItemAtIndex(tree.currentIndex);

	//Close siblings that have children
	var siblings = currentItem.parentNode.getElementsByTagName("treeitem");
	for (i=0; i < siblings.length; i++) {
		if (siblings[i].getElementsByTagName("treechildren").length > 0)
			siblings[i].setAttribute("open", "false");
	}

	var nextItem = tree.view.getItemAtIndex(tree.currentIndex + 1);

	//Movement only allowed in the same branch of the tree
	if (nextItem.parentNode == currentItem.parentNode) {
		nextItem.parentNode.insertBefore(nextItem, currentItem);

		var currentId = currentItem.firstChild.firstChild.getAttribute("id");
		var nextId = nextItem.firstChild.firstChild.getAttribute("id");
		myDoc.insertNodeBefore(nextId, currentId);

		document.getElementById("file-save").setAttribute("disabled", "false");
		docChanged = "true"
	}
}

//Context menu "Delete"
function deleteCriterion() {
	var result = confirm("Do you confirm deletion of UID "+id+" criterion?");
	if (result) {
		myDoc.deleteNode(id);
		var node = document.getElementById(id).parentNode.parentNode;
		var parentNode = node.parentNode;
		parentNode.removeChild(node);
		var treeitems = parentNode.getElementsByTagName("treeitem");
		if (treeitems.length <= 0) {
			//Parent has no more children
			parentNode.parentNode.removeChild(parentNode);
		}
		
		document.getElementById("file-save").setAttribute("disabled", "false");
		docChanged = "true";
	}
}
