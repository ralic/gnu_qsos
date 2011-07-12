/**
 *  Copyright (C) 2006-2011 Atos
 *
 *  Authors: Raphael Semeteys <raphael.semeteys@atos.net>
 *           Timothée Ravier <travier@portaildulibre.fr>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 *  QSOS XUL Editor
 *  file.js: functions associated with the file tab
 *
**/


// Shows the new.xul window in modal mode
function newFileDialog() {
  if (checkCloseFile() == false) {
    return;
  }
  getPrivilege();
  window.openDialog('chrome://qsos-xuled/content/new.xul', 'Properties','chrome,dialog,modal', myDoc, openRemoteFile);
}


function populateLanguage() {
  var languages = myDoc.getLanguageList();
  var languageList = document.getElementById("languagePopup");
  for(var i = 0; i < languages.length; ++i) {
    var menuitem = document.createElement("menuitem");
    menuitem.setAttribute("label", languages[i]);
    languageList.appendChild(menuitem);
  }
}


function populateArchetype() {
  var archetypes = myDoc.getArchetypeList();
  var archetypeList = document.getElementById("archetypePopup");
  for(var i = 0; i < archetypes.length; ++i) {
    var menuitem = document.createElement("menuitem");
    menuitem.setAttribute("label", archetypes[i]);
    archetypeList.appendChild(menuitem);
  }
}


function populateLicense() {
  var licenses = myDoc.getLicenseList();
  var licenseList = document.getElementById("licensePopup");
  for(var i = 0; i < licenses.length; ++i) {
    var menuitem = document.createElement("menuitem");
    menuitem.setAttribute("label", licenses[i]);
    licenseList.appendChild(menuitem);
  }
}


function emptyList(list) {
  while(list.childElementCount > 0) {
    list.removeChild(list.firstChild);
  }
}


function selectElementInList(list, name) {
  for (var i = 0; i < list.itemCount; ++i) {
    if (list.getItemAtIndex(i).label == name) {
      list.selectedItem = list.getItemAtIndex(i);
      return;
    }
  }
  alert("Can't find " + name + " in the list!");
}


// Setup editor when opening a file
function setupEditorForEval() {
  // Window's title
  document.title = strbundle.getString("QSOSEvaluation") + " " + myDoc.get("component/name") + " (" + myDoc.getfilename() + ")";

  // Setting up text fields (see editor.js for details)
  for (var element in textElements) {
    document.getElementById(element).value = myDoc.get(textElements[element]);
  }

  // Setting up date fields
  for (var element in dateElements) {
    var tmp = myDoc.get(dateElements[element]);
    if (tmp == "") {
      document.getElementById(element).value = resetDate();
    } else {
      document.getElementById(element).value = tmp;
    }
  }

  // Component fields
  populateArchetype();
  selectElementInList(document.getElementById("componentArchetype"), myDoc.get("component/archetype"));

  // License and Legal
  populateLicense();
  selectElementInList(document.getElementById("licenseName"), myDoc.get("openSourceCartouche/license/name"));

  populateLanguage();
  selectElementInList(document.getElementById("evaluationLanguage"), myDoc.get("qsosMetadata/language"));

  // Authors (evaluation + Open Source Cartouche metadata), Contributors, Developers
  var authorsArray = new Array("evaluation","osc");
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

  var teamArray = new Array("developer", "contributor");
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
  try {
    var tree = document.getElementById("criteriaTree");
    var treechildren = buildtree();
    tree.appendChild(treechildren);
  } catch (e) {
    alert(e.message);
  }

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
//   alert("buildTree: begin");
  var treechildren = document.createElement("treechildren");
  treechildren.setAttribute("id", "myTreechildren");
  var criteria = myDoc.getcomplextree();
  for (var i=0; i < criteria.length; i++) {
    var treeitem = newtreeitem(criteria[i]);
    treechildren.appendChild(treeitem);
  }
//   alert("buildTree: end");
  return treechildren;
}


// XUL Tree recursive creation function
function newtreeitem(criterion) {
//   alert("newtreeitem: begin");
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
//   alert("newtreeitem: end");
  return treeitem;
}


// XUL Tree recursive creation function
function buildsubtree(criteria) {
//   alert("buildsubtree: begin");
  var treechildren = document.createElement("treechildren");
  for (var i=0; i < criteria.length; i++) {
    var treeitem = newtreeitem(criteria[i]);
    treechildren.appendChild(treeitem);
  }
//   alert("buildsubtree: end");
  return treechildren;
}


// Saves modifications to the QSOS XML file
function saveFile() {
  try {
    if (myDoc) {
      // Updating evaluation content
      // Checking component fields
      toCheck = new Array("componentName", "componentReleaseDate", "componentVersion", "componentMainTech", "componentHomepage", "componentType", "componentStatus", "componentVendor");
      for(var i = 0; i < toCheck.length; ++i) {
        if (document.getElementById(toCheck[i]).value == ""){
          alert(strbundle.getString("componentEmpty") + " " + toCheck[i]);
          break;
        }
      }

      for (var element in textElements) {
        myDoc.set(textElements[element], document.getElementById(element).value);
      }

      // Setting up date fields
      for (var element in dateElements) {
        myDoc.set(dateElements[element], document.getElementById(element).value);
      }

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
  } catch (e) {
    alert("An error occured while saving file: " + e.message);
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
  return "2000-01-01";
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

  for (var element in textElements) {
    document.getElementById(element).value = "";
  }

  // Setting up date fields
  for (var element in dateElements) {
    document.getElementById(element).value = resetDate();
  }

  // Component fields
  emptyList(document.getElementById("archetypePopup"));

  // License and Legal
  emptyList(document.getElementById("licensePopup"));

  // Metadata
  emptyList(document.getElementById("languagePopup"));

  // Reset authors & contributors lists
  lists = new Array("oscAuthors","evaluationAuthors","developerTeam","contributorTeam");
  for (var i = 0; i < lists.length; ++i) {
    var list = document.getElementById(lists[i]);
    while (list.childElementCount > 1) {
      list.removeChild(list.lastChild);
    }
  }

  // Resets the criteria tab
  document.getElementById("criteriaDescription").value = "";
  document.getElementById("scoreDescription0").label = strbundle.getString("score0Label");
  document.getElementById("scoreDescription1").label = strbundle.getString("score1Label");
  document.getElementById("scoreDescription2").label = strbundle.getString("score2Label");
  document.getElementById("scoreRadiogroup").selectedIndex = -1;
  document.getElementById("criteriaComments").value = "";

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
