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
 *  editor.js: functions associated with the editor.xul file
 *
**/

// Object "Document" representing data in the QSOS XML file
var myDoc;
// Indicator of document modification
var docChanged;
var evaluationOpen;
// id (actually "name" in the QSOS XML file) of the currently selected criteria in the tree
var id;
// Localized strings bundle
var strbundle;


// Objects to save/modify/empty cells and set/reset dates the easy way
// This is a dictionnary that gives the correpsondant xml path for each xul element
var textElements = new Object();
// Component
textElements["componentName"] = "component/name";

textElements["componentVersion"] = "component/version";
textElements["componentMainTech"] = "component/mainTech";
textElements["componentHomepage"] = "component/homepage";
textElements["componentTags"] = "component/tags";
// textElements["componentStatus"] = "component/status";
textElements["componentVendor"] = "component/vendor";
textElements["componentDescription"] = "component/description";

// License and Legal
textElements["licenseVersion"] = "openSourceCartouche/license/version";
textElements["licenseHomepage"] = "openSourceCartouche/license/homepage";

textElements["copyright"] = "openSourceCartouche/legal/copyright";

// Team
textElements["number"] = "team/number";

// Authors tab
// Evaluation
// textElements["evaluationVersion"] = "qsosMetadata/version";

textElements["evaluationReviewerName"] = "evaluation/reviewer/name";
textElements["evaluationReviewerEmail"] = "evaluation/reviewer/email";
textElements["evaluationReviewerComment"] = "evaluation/reviewer/comment";

// Open Source Cartouche Metadata
textElements["oscAuthorName"] = "openSourceCartouche/metadata/author/name";
textElements["oscAuthorEmail"] = "openSourceCartouche/metadata/author/email";
textElements["oscAuthorComment"] = "openSourceCartouche/metadata/author/comment";

textElements["oscReviewerName"] = "openSourceCartouche/metadata/reviewer/name";
textElements["oscReviewerEmail"] = "openSourceCartouche/metadata/reviewer/email";
textElements["oscReviewerComment"] = "openSourceCartouche/metadata/reviewer/comment";


var dateElements = new Object();
dateElements["componentReleaseDate"] = "component/releaseDate";

dateElements["evaluationReviewerDate"] = "evaluation/reviewer/reviewDate";

dateElements["evaluationCreationDate"] = "evaluation/dates/creation";
dateElements["evaluationUpdateDate"] = "evaluation/dates/update";
dateElements["evaluationValidationDate"] = "evaluation/dates/validation";

dateElements["oscReviewerDate"] = "openSourceCartouche/metadata/reviewer/reviewDate";

dateElements["oscCreationDate"] = "openSourceCartouche/metadata/dates/creation";
dateElements["oscUpdateDate"] = "openSourceCartouche/metadata/dates/update";
dateElements["oscValidationDate"] = "openSourceCartouche/metadata/dates/validation";


// Window initialization after loading
function init() {
  try{
    strbundle = document.getElementById("properties");
    docChanged = false;
    setStateEvalOpen(false);
    freezeScore("true");
    freezeComments("true");
  } catch (e) {
    alert("An error occured while setting up the editor: " + e.message);
  }

  // Parameters management
  var urlFirefox = window.arguments[1];
  if (urlFirefox) {
    // Case of a .qsos browsing redirection (cf. qsos-overlay.js)
    openRemoteFile(urlFirefox);
  } else {
    var cmdLine = window.arguments[0];
    cmdLine = cmdLine.QueryInterface(Components.interfaces.nsICommandLine); // FIXME Firefox shows an error here
    var uri = cmdLine.handleFlagWithParam("file", false);
    if (uri) {
      // Case of a .qsos file passed in parameter through commandline (xuleditor filename)
      uri = cmdLine.resolveURI(uri);
      try {
        // FIXME Open file with spaces
        openRemoteFile(uri.spec);
      } catch (e) {
        alert("init: fail to open file " + uri.spec + ": " + e.message);
        closeFile();
      }
    }
  }
}


// Get privilege to open windows
function getPrivilege() {
  try {
    netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
    return true;
  } catch (e) {
    alert("newFile: Permission to open file denied: " + e.message);
    return false;
  }
}


function exitConfirmDialog() {
  getPrivilege();
  try {
    var text = strbundle.getString("exitAnyway");
    window.openDialog('chrome://qsos-xuled/content/confirm.xul', 'Confirm', 'chrome,dialog,modal', text, saveFile, saveFileAs);
  } catch (e) {
    alert("There is a problem here: " + e.message);
  }
}


// Toogle the state of the editor between "eval opened" and "eval closed"
// The general, criteria and chart tabs are open only if a document is opened.
function setStateEvalOpen(state) {
  evaluationOpen = state;
  if (state) {
    var bool = "";
    var nbool= "true";
  } else {
    var bool = "true";
    var nbool = "";
  }
  // Settings tab disable as it's not currently functionnal
  document.getElementById("configTab").hidden = "true";
  document.getElementById("metadataTab").hidden = bool;
  document.getElementById("oscTab").hidden = bool;
  document.getElementById("criteriaTab").hidden = bool;
  // document.getElementById("chartTab").hidden = bool;

  document.getElementById("saveFile").disabled = "true";
  document.getElementById("saveFileAs").disabled = bool;
  document.getElementById("closeFile").disabled = bool;

  // Update old QSOS button is active only when the evaluation is closed
  document.getElementById("updateFromOldQSOS").disabled = nbool;

  // Remote saving is temporarily disabled
  document.getElementById("saveRemoteFile").disabled = "true";
  // Open Remote File button disable since it doesn't work for now
  document.getElementById("openRemoteFile").disabled = "true";

  document.getElementById("updateFromTemplate").disabled = bool;
  document.getElementById("exportOSC").disabled = bool;
  document.getElementById("exportToFreeMind").disabled = bool;

  document.getElementById('tabs').selectedIndex = 2;

  if (!state) {
    document.getElementById("oscLabel").label = strbundle.getString("oscAuthors");
  }
}


// Function to deal with date checkbox
function dateControl(checkbox, datepicker) {
  var datepickerElem = document.getElementById(datepicker);
  if (checkbox.checked) {
    datepickerElem.disabled = "";
  } else {
    datepickerElem.disabled = "true";
  }
}


// (Un)freezes the "Score" input files (current criteria properties)
// bool: "true" to freeze; "" to unfreeze
function freezeScore(bool) {
  document.getElementById("scoreRadiogroup").disabled = bool;
}


// (Un)freezes the "Comments" input file (current criteria property)
// bool: "true" to freeze; "" to unfreeze
function freezeComments(bool) {
  document.getElementById("criteriaComments").disabled = bool;
}


// Changes selected language and updates document
function changeLanguage(object) {
  myDoc.set("qsosMetadata/language", object.selectedItem.label);
  docHasChanged();
}


function changeArchetype(object) {
  myDoc.set("component/archetype", object.selectedItem.label);
  docHasChanged();
}


function changeLicense(object) {
  myDoc.set("openSourceCartouche/license/name", object.selectedItem.label);
  docHasChanged();
}

function changeStatus(object) {
  myDoc.set("component/status", object.selectedItem.label);
  docHasChanged();
}


function populateList(type) {
  var items = myDoc.getList(type);
  var listElement = document.getElementById(type + "Popup");
  for(var i = 0; i < items.length; ++i) {
    var menuitem = document.createElement("menuitem");
    menuitem.setAttribute("label", items[i]);
    listElement.appendChild(menuitem);
  }
}


function emptyList(list) {
  while(list.childElementCount > 0) {
    list.removeChild(list.firstChild);
  }
}


function selectElementInList(list, name) {
  for (var i = 0; i < list.itemCount; ++i) {
    var tmp = list.getItemAtIndex(i);
    if (tmp.label == name) {
      list.selectedItem = tmp;
      return;
    }
  }
  if ((list.itemCount > 0) && (list.getItemAtIndex(0).label == "")) {
    list.selectedItem = list.getItemAtIndex(0);
    return;
  }
  alert("Can't find " + name + " in the list!");
}


// Setup editor when opening a file
function setupEditorForEval() {
  // Check the QSOS version
  var QSOSVersion = myDoc.get("qsosMetadata/qsosVersion");
  var currentVersion = strbundle.getString("currentQSOSVersion");
  if (QSOSVersion != currentVersion) {
    alert("Warning: This is a " + QSOSVersion + " QSOS evaluation, but this editor only supports version " + currentVersion + ".\n\nUse it at your own risk!");
  }

  // Window's title
  document.title = strbundle.getString("QSOSEvaluation") + " " + myDoc.get("component/name") + " (" + myDoc.getfilename() + ")";

  // Display the OpenSource Cartouche version
  labelElem = document.getElementById("oscLabel");
  labelElem.label = strbundle.getString("oscAuthors") + " (" + myDoc.get("openSourceCartouche/metadata/cartoucheVersion") + ")";

  // Setting up text fields (see editor.js for details)
  for (var element in textElements) {
    document.getElementById(element).value = myDoc.get(textElements[element]);
  }

  // Setting up date fields
  for (var element in dateElements) {
    var tmp = myDoc.get(dateElements[element]);
    try {
      var tmpCb = document.getElementById(element + "Checkbox");
    } catch(e) {};
    if (tmpCb != null) {
      if (tmp == "") {
        document.getElementById(element + "Checkbox").checked = false;
        document.getElementById(element).disabled = "true";
        document.getElementById(element).value = resetDate();
      } else {
        document.getElementById(element + "Checkbox").checked = true;
        document.getElementById(element).disabled = "";
        document.getElementById(element).value = tmp;
      }
    } else {
//       alert(element + " " + dateElements[element]);
      document.getElementById(element).value = tmp;
    }
  }

//   alert("la");

  // Component & Status fields
  populateList("archetype");
  selectElementInList(document.getElementById("componentArchetype"), myDoc.get("component/archetype"));
  populateList("status");
  selectElementInList(document.getElementById("componentStatus"), myDoc.get("component/status"));

  // License and Legal
  populateList("license");
  selectElementInList(document.getElementById("licenseName"), myDoc.get("openSourceCartouche/license/name"));

  //   populateLanguage();
  //   selectElementInList(document.getElementById("evaluationLanguage"), myDoc.get("qsosMetadata/language"));

  // Authors (evaluation + Open Source Cartouche metadata), Contributors, Developers
  var authorsArray = new Array("evaluation");
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
