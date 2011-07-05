/*
 **  Copyright (C) 2006-2011 Atos Origin
 **
 **  Author: Raphael Semeteys <raphael.semeteys@atosorigin.com>
 **          Timothée Ravier <travier@portaildulibre.fr>
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
  document.title = strbundle.getString("QSOSEvaluation") + " " + myDoc.get("component/name") + " (" + myDoc.getfilename() + ")";

  // Component fields
  document.getElementById("componentName").value = myDoc.get("component/name");
  document.getElementById("componentReleaseDate").value = myDoc.get("component/releaseDate");
  document.getElementById("componentVersion").value = myDoc.get("component/version");
  document.getElementById("componentMainTech").value = myDoc.get("component/mainTech");
  document.getElementById("componentArchetype").value = myDoc.get("component/archetype");
  document.getElementById("componentHomepage").value = myDoc.get("component/homepage");
  document.getElementById("componentType").value = myDoc.get("component/type");
  document.getElementById("componentStatus").value = myDoc.get("component/status");
  document.getElementById("componentVendor").value = myDoc.get("component/vendor");
  document.getElementById("componentDescription").value = myDoc.get("component/description");

  // License and Legal
  document.getElementById("licenseName").value = myDoc.get("openSourceCartouche/license/name");
  document.getElementById("licenseVersion").value = myDoc.get("openSourceCartouche/license/version");
  document.getElementById("licenseHomepage").value = myDoc.get("openSourceCartouche/license/homepage");

  document.getElementById("copyright").value = myDoc.get("openSourceCartouche/legal/copyright");

  // Team
  document.getElementById("number").value = myDoc.get("team/number");

  // Authors tab
  // Template
  document.getElementById("templateReviewerName").value = myDoc.get("template/reviewer/name");
  document.getElementById("templateReviewerEmail").value = myDoc.get("template/reviewer/email");
  document.getElementById("templateReviewerDate").value = myDoc.get("template/reviewer/date");
  document.getElementById("templateReviewerComment").value = myDoc.get("template/reviewer/comment");

  document.getElementById("templateCreationDate").value = myDoc.get("template/dates/creation");
  document.getElementById("templateUpdateDate").value = myDoc.get("template/dates/update");
  document.getElementById("templateValidationDate").value = myDoc.get("template/dates/validation");

  // Evaluation
  document.getElementById("version").value = myDoc.get("qsosMetadata/version");
  document.getElementById("language").value = myDoc.get("qsosMetadata/language");

  document.getElementById("reviewerName").value = myDoc.get("evaluation/reviewer/name");
  document.getElementById("reviewerEmail").value = myDoc.get("evaluation/reviewer/email");
  document.getElementById("reviewerDate").value = myDoc.get("evaluation/reviewer/date");
  document.getElementById("reviewerComment").value = myDoc.get("evaluation/reviewer/comment");

  document.getElementById("creationDate").value = myDoc.get("evaluation/dates/creation");
  document.getElementById("updateDate").value = myDoc.get("evaluation/dates/update");
  document.getElementById("validationDate").value = myDoc.get("evaluation/dates/validation");

  // Authors (template + evaluation), Contributors, Developers
  authorsArray = new Array("template","evaluation");
  for (var i = 0; i < authorsArray.length; ++i) {
    try {
      var authors = myDoc.getAuthors(authorsArray[i]);
    } catch (e) {
      alert("setupEditorForEval: couldn't get " + authorsArray[i] + " authors: " + e.message);
    }
    var authorList = document.getElementById(authorsArray[i] + "Authors");
    for(var j = 0; j < authors.length; ++j) {
      var listitem = document.createElement("listitem");
      var listcellName = document.createElement("listcell");
      var listcellEmail = document.createElement("listcell");
      var listcellComment = document.createElement("listcell");
      listcellName.setAttribute("label", authors[j].name);
      listcellEmail.setAttribute("label", authors[j].email);
      listcellComment.setAttribute("label", authors[j].comment);
      listitem.appendChild(listcellName);
      listitem.appendChild(listcellEmail);
      listitem.appendChild(listcellComment);
      authorList.appendChild(listitem);
    }
  }

  teamArray = new Array("developer", "contributor");
  for (var i = 0; i < teamArray.length; ++i) {
    try {
      var authors = myDoc.getTeam(teamArray[i]);
    } catch (e) {
      alert("setupEditorForEval: couldn't get " + teamArray[i] + " team: " + e.message);
    }
    var authorList = document.getElementById(teamArray[i] + "Team");
    for(var j = 0; j < authors.length; ++j) {
      var listitem = document.createElement("listitem");
      var listcellName = document.createElement("listcell");
      var listcellEmail = document.createElement("listcell");
      var listcellCompany = document.createElement("listcell");
      listcellName.setAttribute("label", authors[j].name);
      listcellEmail.setAttribute("label", authors[j].email);
      listcellCompany.setAttribute("label", authors[j].company);
      listitem.appendChild(listcellName);
      listitem.appendChild(listcellEmail);
      listitem.appendChild(listcellCompany);
      authorList.appendChild(listitem);
    }
  }

  // Tree population
  /*  var tree = document.getElementById("criteriaTree");
   v ar treechildren = buildtree(); * *
   tree.appendChild(treechildren);*/

  // Draw top-level SVG chart
  drawChart();

  setStateEvalOpen(true);

  // Select the General tab
  document.getElementById('tabs').selectedIndex = 3;
}


function openLocalFile(filename) {
  try {
    myDoc = new Document();
  } catch (e) {
    alert("openLocalFile: new Document(): " + e.message);
    closeFile();
    return;
  }
  myDoc.filename = filename;
  if (myDoc.load() == false) {
    closeFile();
    return;
  }

  try {
    setupEditorForEval();
  } catch (e) {
    alert("openFile: an error occured while setting up the editor " + e.message);
    closeFile();
    return;
  }
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
    openLocalFile(fp.file.path);
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
  if (url.search("file:///") != -1) {
    url = url.replace(/file\:\/\//g, '');
    openLocalFile(url);
  } else {
    myDoc = new Document();
    myDoc.loadremote(url);

    try {
      setupEditorForEval();
    } catch (e) {
      alert("openRemoteFile: an error occured while setting up the editor " + e.message);
      closeFile();
      return;
    }

    // If we're creating a new file, set docHasChanged();
    if (myDoc.filename == null) {
      docHasChanged();
    }
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
//   try {
  if (myDoc) {
    // Updating evaluation content

    // Component fields
    myDoc.set("component/name", document.getElementById("componentName").value);
    myDoc.set("component/releaseDate", document.getElementById("componentReleaseDate").value);
    myDoc.set("component/version", document.getElementById("componentVersion").value);
    myDoc.set("component/mainTech", document.getElementById("componentMainTech").value);
    myDoc.set("component/archetype", document.getElementById("componentArchetype").value);
    myDoc.set("component/homepage", document.getElementById("componentHomepage").value);
    myDoc.set("component/type", document.getElementById("componentType").value);
    myDoc.set("component/status", document.getElementById("componentStatus").value);
    myDoc.set("component/vendor", document.getElementById("componentVendor").value);
    myDoc.set("component/description", document.getElementById("componentDescription").value);

    // License and Legal
    myDoc.set("openSourceCartouche/license/name", document.getElementById("licenseName").value);
    myDoc.set("openSourceCartouche/license/version", document.getElementById("licenseVersion").value);
    myDoc.set("openSourceCartouche/license/homepage", document.getElementById("licenseHomepage").value);

    myDoc.set("openSourceCartouche/legal/copyright", document.getElementById("copyright").value);

    // Team
    myDoc.set("team/number", document.getElementById("number").value);

    // Authors tab
    // Template
    myDoc.set("template/reviewer/name", document.getElementById("templateReviewerName").value);
    myDoc.set("template/reviewer/email", document.getElementById("templateReviewerEmail").value);
    myDoc.set("template/reviewer/date", document.getElementById("templateReviewerDate").value);
    myDoc.set("template/reviewer/comment", document.getElementById("templateReviewerComment").value);

    myDoc.set("template/dates/creation", document.getElementById("templateCreationDate").value);
    myDoc.set("template/dates/update", document.getElementById("templateUpdateDate").value);
    myDoc.set("template/dates/validation", document.getElementById("templateValidationDate").value);

    // Evaluation
    myDoc.set("qsosMetadata/version", document.getElementById("version").value);
    myDoc.set("qsosMetadata/language", document.getElementById("language").value);

    myDoc.set("evaluation/reviewer/name", document.getElementById("reviewerName").value);
    myDoc.set("evaluation/reviewer/email", document.getElementById("reviewerEmail").value);
    myDoc.set("evaluation/reviewer/date", document.getElementById("reviewerDate").value);
    myDoc.set("evaluation/reviewer/comment", document.getElementById("reviewerComment").value);

    myDoc.set("evaluation/dates/creation", document.getElementById("creationDate").value);
    myDoc.set("evaluation/dates/update", document.getElementById("updateDate").value);
    myDoc.set("evaluation/dates/validation", document.getElementById("validationDate").value);

    alert("Save: Warning: Authors aren't saved yet!");

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
//   } catch(e) { alert(e.message); }
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
    return saveFile();
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


// FIXME Find a ay to reset datepickers properly
function resetDate() {
//   var today = Date.now();
//   function pad(n){return n<10 ? '0'+n : n}
//   return today.getUTCFullYear() + "-" + pad(today.getUTCMonth() + 1) + "-" + pad(today.getUTCDate());
// //     function pad(n){return n<10 ? '0'+n : n}
// //     return d.getUTCFullYear()+'-'
// //     + pad(d.getUTCMonth()+1)+'-'
// //     + pad(d.getUTCDate())+'T'
// //     + pad(d.getUTCHours())+':'
// //     + pad(d.getUTCMinutes())+':'
// //     + pad(d.getUTCSeconds())+'Z'}
  return "2011-01-01";
}


// Closes the QSOS XML file and resets window
function closeFile() {
  if (myDoc == null) return;
  myDoc = null;
  id = null;

  setStateEvalOpen(false);
  freezeScore("true");
  freezeComments("true");

  // Resetting interface :
  document.getElementById("QSOS").setAttribute("title", strbundle.getString("QSOSEditor"));

  // Component fields
  document.getElementById("componentName").value = "";
  document.getElementById("componentReleaseDate").value = resetDate();
  document.getElementById("componentVersion").value = "";
  document.getElementById("componentMainTech").value = "";
  document.getElementById("componentArchetype").value = "";
  document.getElementById("componentHomepage").value = "";
  document.getElementById("componentType").value = "";
  document.getElementById("componentStatus").value = "";
  document.getElementById("componentVendor").value = "";
  document.getElementById("componentDescription").value = "";

  // License and Legal
  document.getElementById("licenseName").value = "";
  document.getElementById("licenseVersion").value = "";
  document.getElementById("licenseHomepage").value = "";

  document.getElementById("copyright").value = "";

  // Team
  document.getElementById("number").value = "";

  // Authors tab
  // Template
  document.getElementById("templateReviewerName").value = "";
  document.getElementById("templateReviewerEmail").value = "";
  document.getElementById("templateReviewerDate").value = resetDate();
  document.getElementById("templateReviewerComment").value = "";

  document.getElementById("templateCreationDate").value = resetDate();
  document.getElementById("templateUpdateDate").value = resetDate();
  document.getElementById("templateValidationDate").value = resetDate();

  // Evaluation
  document.getElementById("version").value = "";
  document.getElementById("language").value = "";

  document.getElementById("authorName").value = "";
  document.getElementById("authorEmail").value = "";
  document.getElementById("authorComment").value = "";

  document.getElementById("reviewerName").value = "";
  document.getElementById("reviewerEmail").value = "";
  document.getElementById("reviewerDate").value = resetDate();
  document.getElementById("reviewerComment").value =  "";

  document.getElementById("creationDate").value = resetDate();
  document.getElementById("updateDate").value = resetDate();
  document.getElementById("validationDate").value = resetDate();

  // Reset authors lists, contributors lists, ...
//   var myList = document.getElementById("f-a-list");
//   while (myList.hasChildNodes()) {
//     myList.removeChild(myList.childNodes[0]);
//   }

  // Resets the criteria tab
  document.getElementById("criteriaDescription").value = "";
  document.getElementById("scoreDescription0").label = strbundle.getString("score0Label");
  document.getElementById("scoreDescription1").label = strbundle.getString("score1Label");
  document.getElementById("scoreDescription2").label = strbundle.getString("score2Label");
  document.getElementById("scoreRadiogroup").selectedIndex = -1;
  document.getElementById("criteriaComments").value = "";

//   var tree = document.getElementById("criteriaTree");
//   var treechildren = document.getElementById("myTreechildren");
//   tree.removeChild(treechildren);
//   clearChart();
//   clearLabels();
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
