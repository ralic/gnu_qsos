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


// Reads a file and return its content as a string
// Returns null if the file can't be found/opened
function readFile(filename) {
  var file = Components.classes["@mozilla.org/file/local;1"]
  .createInstance(Components.interfaces.nsILocalFile);
  try {
    file.initWithPath(filename);
    if (file.exists() == false) {
      alert("readFile: " + filename + " doesn't exist");
      return null;
    }
  } catch(e) {
    alert("readFile: can't open file " + filename);
    return null;
  }

  var is = Components.classes["@mozilla.org/network/file-input-stream;1"]
  .createInstance(Components.interfaces.nsIFileInputStream);
  is.init(file, 0x01, 00004, null);

  var sis = Components.classes["@mozilla.org/scriptableinputstream;1"]
  .createInstance(Components.interfaces.nsIScriptableInputStream);
  sis.init(is);

  var output = sis.read(sis.available());

  var converter = Components.classes["@mozilla.org/intl/scriptableunicodeconverter"]
  .createInstance(Components.interfaces.nsIScriptableUnicodeConverter);
  converter.charset = "UTF-8";
  output = converter.ConvertToUnicode(output);

  return output;
}


// Open a dialog box to pick a file with the extension 'ext', and type 'type'
// Returns the complete file as a string
function pickAFile(ext, type) {
  getPrivilege();
  var nsIFilePicker = Components.interfaces.nsIFilePicker;
  var fp = Components.classes["@mozilla.org/filepicker;1"].createInstance(nsIFilePicker);
  fp.init(window, strbundle.getString("selectFile"), nsIFilePicker.modeOpen);
  fp.appendFilter(type, "*" + ext);
  var res = fp.show();

  if (res != nsIFilePicker.returnOK) {
    return "";
  }
  return fp.file.path;
}


// Parse an XML string read from a file
// Returns an XML Object
function parseXML(string) {
  var domParser = new DOMParser();
  return domParser.parseFromString(string, "text/xml");
}


// Serialize an XML Object to a string for printing and file writing purposes
function serializeXML(xmlObject) {
  var serializer = new XMLSerializer();
  return serializer.serializeToString(xmlObject);
}


// Load an XML file
function loadFile(filename) {
  if (filename == "") {
    return null;
  }
  var fileContent = readFile(filename);
  if (fileContent == "") {
    alert("loadFile: file empty");
    return null;
  }
  var xml = parseXML(fileContent);
  var error = xml.getElementsByTagName("parsererror");
  if (error.length == 1) {
    alert("loadFile: " + strbundle.getString("parsingError") + "\n\n" + error[0].textContent);
    return null;
  }
  return xml;
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
    if (setupEditorForEval() == false) {
      closeFile();
      return;
    }
  } catch (e) {
    alert("openLocalFile: an error occured while setting up the editor " + e.message + "\n\n" + strbundle.getString("advice"));
    closeFile();
    return;
  }
}


// Opens a local QSOS XML file and populates the window (tree and generic fields)
function openFile() {
  if (checkCloseFile() == false) {
    return;
  }
  var res = pickAFile(".qsos", strbundle.getString("QSOSFile"));
  if (res == "") {
    return;
  }
  openLocalFile(res);
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
  var treechildren = document.createElement("treechildren");
  treechildren.setAttribute("id", "myTreechildren");
  var criteria = myDoc.getcomplextree();
  for (var i=0; i < criteria.length; i++) {
    var treeitem = newtreeitem(criteria[i]);
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
    var treeitem = newtreeitem(criteria[i]);
    treechildren.appendChild(treeitem);
  }
  return treechildren;
}


// Saves modifications to the QSOS XML file
function saveFile() {
  try {
    if (myDoc) {
      // Updating evaluation content
      // Checking component fields
      toCheck = new Array("componentName", "componentReleaseDate", "componentVersion", "componentMainTech", "componentHomepage", "componentTags", "componentVendor");
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
        try {
        var tmpCb = document.getElementById(element + "Checkbox");
        } catch(e) {};
        if ((tmpCb != null) && (tmpCb.checked == false)) {
          myDoc.set(dateElements[element], "");
        } else {
          myDoc.set(dateElements[element], document.getElementById(element).value);
        }
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


// FIXME Find a ay to reset datepickers properly (We are using checkboxes for now)
function resetDate() {
  return "2000-01-01";
}


// Closes the QSOS XML file and resets window
function closeFile() {
  try{
    if (myDoc == null) return;
    myDoc = null;
    id = null;

    setStateEvalOpen(false);
    freezeScore("true");
    freezeComments("true");
  } catch (e) {
    alert("closeFile: problem in first interface stuff: " + e.message);
  }

  try {
    // Resetting interface :
    document.getElementById("QSOS").setAttribute("title", strbundle.getString("QSOSEditor"));

    // Resetting basic text elements
    for (var element in textElements) {
      document.getElementById(element).value = "";
    }

    // Resetting date fields
    for (var element in dateElements) {
      document.getElementById(element).value = resetDate();
      document.getElementById(element).disabled = false;
      try {
      document.getElementById(element + "Checkbox").checked = false;
      } catch(e) {};
    }
  } catch (e) {
    alert("closeFile: problem in interface reset stuff: " + e.message);
  }

  try {
    // Component fields
    emptyList(document.getElementById("archetypePopup"));
    // License
    emptyList(document.getElementById("licensePopup"));
    // Status
    emptyList(document.getElementById("statusPopup"));

    // Reset authors & contributors lists
    lists = new Array("evaluationAuthors","developerTeam","contributorTeam");
    for (var i = 0; i < lists.length; ++i) {
      var list = document.getElementById(lists[i]);
      while (list.childElementCount > 1) {
        list.removeChild(list.lastChild);
      }
    }

    // Resets authors & team stuff
    var toBeCleared = new Array("evaluationAuthorName", "evaluationAuthorEmail", "evaluationAuthorComment", "developerName", "developerEmail", "developerCompany", "contributorName", "contributorEmail", "contributorCompany");
    for (var element in toBeCleared) {
      document.getElementById(toBeCleared[element]).value = "";
    }
  } catch (e) {
    alert("closeFile: problem in interface lists reset stuff: " + e.message);
  }

  try {
    // Resets the criteria tab
    document.getElementById("criteriaDescription").value = "";
    document.getElementById("scoreDescription0").label = strbundle.getString("score0Label");
    document.getElementById("scoreDescription1").label = strbundle.getString("score1Label");
    document.getElementById("scoreDescription2").label = strbundle.getString("score2Label");
    document.getElementById("scoreRadiogroup").selectedIndex = -1;
    document.getElementById("criteriaComments").value = "";

    var tree = document.getElementById("criteriaTree");
    var treechildren = document.getElementById("myTreechildren");
    if (tree.firstChild) {
      tree.removeChild(treechildren);
    }
    clearChart();
    clearLabels();
  } catch(e) {}
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
