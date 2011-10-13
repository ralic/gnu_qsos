/*
 **  Copyright (C) 2006-2011 Atos Origin
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
 **  file.js: functions associated with the file tab
 **
 */


// Shows the new.xul window in modal mode
function newFileDialog() {
  if (checkCloseFile() == false) {
    return;
  }
  getPrivilege();
  window.openDialog('chrome://qsos-xuled/content/new.xul', 'Properties','chrome,dialog,modal', myDoc, openRemoteFile);
}


// Setup editor when opening a file
function setupEditorForEval() {
  // Window's title
  document.title = strbundle.getString("QSOSEvaluation") + "  " + myDoc.getappname();

  // Tree population
  var tree = document.getElementById("criteriaTree");
  var treechildren = buildtree();
  tree.appendChild(treechildren);

  // License setup and checks
  var licenses = myDoc.getlicenselist();
  var mypopuplist = document.getElementById("f-license-popup");
  for(var i=0; i < licenses.length; i++) {
    var menuitem = document.createElement("menuitem");
    menuitem.setAttribute("label", licenses[i]);
    mypopuplist.appendChild(menuitem);
  }

  var licenseIdFromDesc = -1;
  var licenseDesc = myDoc.getlicensedesc();
  for (var i=0; i < licenses.length; ++i){
    if (licenses[i] == licenseDesc) {
      var licenseIdFromDesc = i;
      break;
    }
  }
  var licenseId = myDoc.getlicenseid();
  var licenseList = document.getElementById("f-license");
  if (licenseIdFromDesc != -1){
    licenseList.selectedIndex = licenseIdFromDesc;
  } else {
    licenseList.selectedIndex = licenseId;
  }

  // Other fields
  document.getElementById("f-software").value = myDoc.getappname();
  document.getElementById("f-release").value = myDoc.getrelease();
  document.getElementById("f-sotwarefamily").value = myDoc.getqsosappfamily();
  document.getElementById("f-desc").value = myDoc.getdesc();
  document.getElementById("f-url").value = myDoc.geturl();
  document.getElementById("f-demourl").value = myDoc.getdemourl();

  // Authors
  var authors = myDoc.getauthors();
  var mylist = document.getElementById("f-a-list");
  for(var i=0; i < authors.length; i++) {
    var listitem = document.createElement("listitem");
    listitem.setAttribute("label", authors[i].name);
    listitem.setAttribute("value", authors[i].email);
    mylist.appendChild(listitem);
  }

  setStateEvalOpen(true);

  // Draw top-level SVG chart
  drawChart();

  // Select the General tab
  document.getElementById('tabs').selectedIndex = 1;
}


// Opens a local QSOS XML file and populates the window (tree and generic fields)
function openFile() {
  if (checkCloseFile() == false) {
    return;
  }
  getPrivilege();
  var nsIFilePicker = Components.interfaces.nsIFilePicker;
  var fp = Components.classes["@mozilla.org/filepicker;1"].createInstance(nsIFilePicker);
  fp.init(window, strbundle.getString("selectFile"), nsIFilePicker.modeOpen);
  fp.appendFilter(strbundle.getString("QSOSFile"), "*.qsos");
  var res = fp.show();

  if (res == nsIFilePicker.returnOK) {
    myDoc = new Document();
    myDoc.filename = fp.file.path;
    myDoc.load();

    setupEditorForEval();
  }
}


// Shows the load.xul window in modal mode
function loadRemoteDialog() {
//   var filebox = document.getElementById("fileHBox");
//   tree = document.createElement("tree");
//   treecols = document.createElement("treecols");
//   treecol = document.createElement("treecol");
//   tree.setAttribute("id", "evalTree");
//   tree.setAttribute("flex", "2");
//   treecol.setAttribute("id", "name");
//   treecol.setAttribute("width", "500px");
//   treecol.setAttribute("label", "&label1.value;");
//   treecol.setAttribute("primary", "true");
//   treecol.setAttribute("flex", "2");
//
//   var vbox = document.createElement("vbox");
//   var hbox = document.createElement("hbox");
//   var buttonOK = document.createElement("button");
//   var buttonCancel = document.createElement("button");
//   buttonOk.setAttribute("label", "Ok");
//   buttonCancel.setAttribute("label", "Cancel");
//
//   filebox.appendChild(vbox);
//   vbox.appendChild(tree);
//   tree.appendChild(treecols);
//   treecols.appendChild(treecol);
//   vbox.appendChild(hbox);
//   hbox.appendChild(buttonCancel);
//   hbox.appendChild(buttonOk);

  if (checkCloseFile() == false) {
    return;
  }
  getPrivilege();
  window.openDialog('chrome://qsos-xuled/content/load.xul', 'Properties', 'chrome,dialog,modal', myDoc, openRemoteFile);
}


function openRemoteFile(url) {
  if (url == "") return;

  myDoc = new Document();
  myDoc.loadremote(url);

  setupEditorForEval();

  // If we're creating a new file, set docHasChanged();
  if (myDoc.filename == null) {
    docHasChanged();
  }
}


// XUL Tree recursive creation function
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


// XUL Tree recursive creation function
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
  if (criterion.children != "null") {
    treeitem.setAttribute("open", "false");
    treeitem.appendChild(buildsubtree(criterion.children));
  }
  return treeitem;
}


// XUL Tree recursive creation function
function buildsubtree(criteria) {
  var treechildren = document.createElement("treechildren");
  for (var i=0; i < criteria.length; i++) {
    treeitem = newtreeitem(criteria[i]);
    treechildren.appendChild(treeitem);
  }
  return treechildren;
}


// Saves modifications to the QSOS XML file
function saveFile() {
  if (myDoc) {
    if (myDoc.filename != null) {
      myDoc.write();
      docHasChanged(false);
      return true;
    } else {
      if (saveFileAs() == true) {
        docHasChanged(false);
        return true;
      }
    }
  }
  return false;
}


// Saves modifications to a new QSOS XML file
function saveFileAs() {
  getPrivilege();
  var nsIFilePicker = Components.interfaces.nsIFilePicker;
  var fp = Components.classes["@mozilla.org/filepicker;1"].createInstance(nsIFilePicker);
  fp.init(window, strbundle.getString("saveFileAs"), nsIFilePicker.modeSave);
  fp.appendFilter(strbundle.getString("QSOSFile"),"*.qsos");
  var res = fp.show();
  if ((res == nsIFilePicker.returnOK) || (res == nsIFilePicker.returnReplace)) {
    myDoc.setfilename(fp.file.path);
    myDoc.write();
    docHasChanged(false);
    return true;
  }
  return false;
}


// Saves modifications to a new QSOS XML file
function saveRemote() {
  var prefManager = Components.classes["@mozilla.org/preferences-service;1"]
  .getService(Components.interfaces.nsIPrefBranch);
  var saveremote = prefManager.getCharPref("extensions.qsos-xuled.saveremote");

  myDoc.writeremote(saveremote);
}


// Closes the QSOS XML file and resets window
function closeFile() {
  document.getElementById("QSOS").setAttribute("title", strbundle.getString("QSOSEditor"));
  document.getElementById("f-software").value = "";
  document.getElementById("f-release").value = "";
  document.getElementById("f-sotwarefamily").value = "";
  document.getElementById("f-desc").value = "";
  document.getElementById("f-url").value = "";
  document.getElementById("f-demourl").value = "";

  var myList = document.getElementById("f-a-list");
  while (myList.hasChildNodes()) {
    myList.removeChild(myList.childNodes[0]);
  }

  var licensePopupList = document.getElementById("f-license-popup");
  while (licensePopupList.hasChildNodes()) {
    licensePopupList.removeChild(licensePopupList.childNodes[0]);
  }
//   document.getElementById("f-license").removeAllItems();

  document.getElementById("f-a-name").value = "";
  document.getElementById("f-a-email").value = "";
  document.getElementById("f-c-desc0").setAttribute("label", strbundle.getString("score0Label"));
  document.getElementById("f-c-desc1").setAttribute("label", strbundle.getString("score1Label"));
  document.getElementById("f-c-desc2").setAttribute("label", strbundle.getString("score2Label"));
  document.getElementById("f-c-score").selectedIndex = -1;
  document.getElementById("f-c-comments").value = "";

  myDoc = null;
  id = null;

  setStateEvalOpen(false);
  freezeScore("true");
  freezeComments("true");

  var tree = document.getElementById("criteriaTree");
  var treechildren = document.getElementById("myTreechildren");
  tree.removeChild(treechildren);
  clearChart();
  clearLabels();
}

// Checks Document's state before closing it
function checkCloseFile() {
  if (myDoc) {
    if (docChanged == true) {
      if(confirm(strbundle.getString("closeAnyway")) == false) {
        return false;
      }
    }
    closeFile();
  }
  return true;
}


// Exits application
function exit() {
  if (myDoc) {
    if (docChanged == true) {
      exitConfirmDialog()
    }
  }
  self.close();
}
