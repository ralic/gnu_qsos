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
    freezeScore("true");
    freezeComments("true");
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
        myDoc = new Document(fp.file.path);
        myDoc.load();
        
        //Window's title
        document.getElementById("QSOS").setAttribute("title", "QSOS evaluation: "+myDoc.getappname());
        
        //Tree population
        var tree = document.getElementById("mytree");
	var treechildren = buildtree();
        tree.appendChild(treechildren);
	
	//License
	var licenses = myDoc.getlicenselist();
	var mypopuplist = document.getElementById("f-license-popup");
	for(var i=0; i < licenses.length; i++) {
		var menuitem = document.createElement("menuitem");
		menuitem.setAttribute("label", licenses[i]);
		mypopuplist.appendChild(menuitem);
	}
			 
	var licenseid = myDoc.getlicenseid();
	var mylist = document.getElementById("f-license");
        mylist.selectedIndex = licenseid;
        
        //Other fields
        document.getElementById("f-software").value = myDoc.getappname();
	document.getElementById("f-release").value = myDoc.getrelease();
	document.getElementById("f-sotwarefamily").value = myDoc.getqsosappfamily();
	document.getElementById("f-desc").value = myDoc.getdesc();
	document.getElementById("f-url").value = myDoc.geturl();
	document.getElementById("f-demourl").value = myDoc.getdemourl();
        
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
	document.getElementById("f-software").value = "";
	document.getElementById("f-release").value = "";
	document.getElementById("f-sotwarefamily").value = "";
	document.getElementById("f-desc").value = "";
	document.getElementById("f-url").value = "";
	document.getElementById("f-demourl").value = "";

    	document.getElementById("f-c-title").value = "";
	document.getElementById("f-c-id").setAttribute("myid", "");
	document.getElementById("f-c-desc0").setAttribute("label", "Score 0");
	document.getElementById("f-c-desc1").setAttribute("label", "Score 1");
	document.getElementById("f-c-desc2").setAttribute("label", "Score 2");
    	document.getElementById("f-c-score").selectedIndex = -1;
	document.getElementById("f-c-comments").value = "";
    
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
// Menu "Edit" function
////////////////////////////////////////////////////////////////////

function updateDoc(newDoc) {
	if (newDoc != "null") {
		myDoc = newDoc;
		docChanged = "true";
		document.getElementById("file-save").setAttribute("disabled", "false");
	}
}

//Submenu "Edit/Authors"
//Shows the authors.xul window in modal mode
function authorsDialog() {
	try {
		netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	} catch (e) {
		alert("Permission to open file was denied.");
	}
	window.openDialog('chrome://qsos-xuled/content/authors.xul','Properties','chrome,dialog,modal',myDoc,updateDoc);
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
    window.openDialog('chrome://qsos-xuled/content/about.xul','About','chrome,dialog,modal');
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
	window.openDialog('chrome://qsos-xuled/content/confirm.xul','Confirm','chrome,dialog,modal',content,doaction);
}

//(Un)freezes generic input files (software properties)
//bool: "true" to freeze; "" to unfreeze
function freezeGeneric(bool) {
	document.getElementById("f-software").disabled = bool;
	document.getElementById("f-release").disabled = bool;
	document.getElementById("f-sotwarefamily").disabled = bool;
	document.getElementById("f-license").disabled = bool;
	document.getElementById("f-desc").disabled = bool;
	document.getElementById("f-url").disabled = bool;
	document.getElementById("f-demourl").disabled = bool;    
}

//(Un)freezes the "Score" input files (current criteria properties)
//bool: "true" to freeze; "" to unfreeze
function freezeScore(bool) {
	document.getElementById("f-c-score").disabled = bool;    
}

//(Un)freezes the "Comments" input file (current criteria property)
//bool: "true" to freeze; "" to unfreeze
function freezeComments(bool) {
	document.getElementById("f-c-comments").disabled = bool;    
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
	document.getElementById("f-c-title").value = myDoc.getkeytitle(id);
	document.getElementById("f-c-id").setAttribute("myid", id);
	
	document.getElementById("f-c-desc0").setAttribute("label", "0: "+myDoc.getkeydesc0(id));
	document.getElementById("f-c-desc1").setAttribute("label", "1: "+myDoc.getkeydesc1(id));
	document.getElementById("f-c-desc2").setAttribute("label", "2: "+myDoc.getkeydesc2(id));
	var score = myDoc.getkeyscore(id);
    if (score == "-1") {
    	document.getElementById("f-c-deck").selectedIndex = "0";
	document.getElementById("f-c-desc").value = myDoc.getkeydesc(id);
        freezeScore("true");
    }
    else {
	document.getElementById("f-c-score").selectedIndex = score;
	document.getElementById("f-c-deck").selectedIndex = "1";
        freezeScore("");
    }
    
	document.getElementById("f-c-comments").value = myDoc.getkeycomment(id);
    freezeComments("");
}

//Triggered when software name is modified
function changeAppName(xulelement) {
	docChanged = "true";
	myDoc.setappname(xulelement.value);
    document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when software release is modified
function changeRelease(xulelement) {
	docChanged = "true";
	myDoc.setrelease(xulelement.value);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when software family is modified
function changeSoftwareFamily(xulelement) {
	docChanged = "true";
	myDoc.setqsosappfamily(xulelement.value);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when software license is modified
function changeLicense(list, id) {
	docChanged = "true";
	myDoc.setlicenseid(id);
	myDoc.setlicensedesc(list.selectedItem.getAttribute("label"));
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when software description is modified
function changeDesc(xulelement) {
	docChanged = "true";
	myDoc.setdesc(xulelement.value);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when software URL is modified
function changeUrl(xulelement) {
	docChanged = "true";
	myDoc.seturl(xulelement.value);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when software demo URL is modified
function changeDemoUrl(xulelement) {
	docChanged = "true";
	myDoc.setdemourl(xulelement.value);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when current criteria's comments are modified
function changeComments(xulelement) {
	docChanged = "true";
	myDoc.setkeycomment(id, xulelement.value);
	document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when current criteria's score is modified
function changeScore(score) {
	docChanged = "true";
	myDoc.setkeyscore(id, score);
	document.getElementById("file-save").setAttribute("disabled", "false");
}
