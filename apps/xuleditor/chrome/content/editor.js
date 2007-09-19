/*
**  Copyright (C) 2006, 2007 Atos Origin 
**
**  Author: Raphael Semeteys <raphael.semeteys@atosorigin.com>
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
**    - Chat: remove unavailable users from roster
**    - Chat: smileys replacement
*/

//Object "Document" representing data in the QSOS XML file
var myDoc;
//Indicator of document modification 
var docChanged;
//id (actually "name" in the QSOS XML file) of the currently selected criteria in the tree
var id;
//Localized strings bundle
var strbundle;

//Chat variables
var nick;
var chatroom;
var con;

//Window initialization after loading
function init() {
  strbundle = document.getElementById("properties");
  docChanged = "false";
  freezeGeneric("true");
  freezeScore("true");
  freezeComments("true");
  //Menu management
  document.getElementById("file-save").setAttribute("disabled", "true");
  document.getElementById("file-saveas").setAttribute("disabled", "true");
  document.getElementById("file-close").setAttribute("disabled", "true");

  //Case of a .qsos browsing redirection (cf. qsos-overlay.js)
  var url = window.arguments[1];
  if (url) {
    openRemoteFile(url)
  }
}

////////////////////////////////////////////////////////////////////
// Chat event functions
////////////////////////////////////////////////////////////////////

//Initializes chat session
function startChat() {
  var prefManager = Components.classes["@mozilla.org/preferences-service;1"]
            .getService(Components.interfaces.nsIPrefBranch);
  chatroom = prefManager.getCharPref("extensions.qsos-xuled.chatroom");
  nick = prefManager.getCharPref("extensions.qsos-xuled.nick", "_");
  if (nick == "_") {
    //If nickname is not stored in preferences, ask for it and store it
    nick = window.prompt(strbundle.getString("nick"));
    prefManager.setCharPref("extensions.qsos-xuled.nick", nick);
  }
  document.getElementById("chat-start").setAttribute("disabled", "true");
  document.getElementById("chat-end").setAttribute("disabled", "false");
  document.getElementById("chat-nick").setAttribute("disabled", "true");
  document.getElementById('t-s').selectedIndex = 3;
  doLogin();
}

//Replace some HTML reserved characters
function htmlEnc(str) {
  str = str.replace(/&/g,"&amp;");
  str = str.replace(/</g,"&lt;");
  str = str.replace(/>/g,"&gt;");
  str = str.replace(/\"/g,"&quot;");
  str = str.replace(/\n/g,"<html:br />");
  return str;
}

//Returns nickname from user's JID
function getUserNick(jid) {
  var splitted_jid = jid.split("/");
  if (splitted_jid.length > 1) {
    return splitted_jid[1];
  } else {
    return "Chatroom";
  }
}

//Put the selected nick in input field
function talkToUser() {
  var listbox = document.getElementById('roster');
  var inputText = document.getElementById('input');
  if (listbox.selectedItem) {
    inputText.value += listbox.selectedItem.getAttribute('id').substring(1) + " ";
  }
}

//JSJaC IQ event handler (debug)
//Remove the "hidden" attribute in "err" textbox in editor.xul file to visualize debug messages
function handleEvent(aJSJaCPacket) {
  document.getElementById('err').value += "IN (raw): " + aJSJaCPacket.xml() + '\n';
}

//JSJaC error handler (debug)
//Remove the "hidden" attribute in "err" textbox in editor.xul file to visualize error messages
function handleError(e) {
  document.getElementById('err').value = "An error occured: Code: " + e.getAttribute('code') + "\nType:" + e.getAttribute('type') + "\nCondition: " + e.firstChild.nodeName; 
}

//JSJaC message handler
function handleMessage(aJSJaCPacket) {
  var conversation = document.getElementById('conversation');
  var usernick = getUserNick(aJSJaCPacket.getFrom());
  var msg = htmlEnc(aJSJaCPacket.getBody());

  var nickcolor;
  switch(usernick) {
    case nick:
      nickcolor = 'blue';
      break
    case 'Chatroom':
      nickcolor = 'red';
      break
    default:
      nickcolor = 'black';
  }

  var msgcolor;
  if (msg.indexOf(nick) != -1) {
    msgcolor = 'green';
  } else {
    msgcolor = 'black';
  }

  conversation.innerHTML += 
    "<html:span style='font-weight: bold; color: " + nickcolor + "'>" + 
    usernick + ": </html:span>" + 
    "<html:span style='color: " + msgcolor + "'>" + msg + "</html:span>" +
    "<html:br />";

  //Scroll to the bottom of the conversation view
  conversation.scrollTop = conversation.scrollHeight;
}

//JSJaC presence handler
function handlePresence(aJSJaCPacket) {
  //Text to be inserted in roster
  var item = '';
  if (!aJSJaCPacket.getType() && !aJSJaCPacket.getShow()) {
    item += getUserNick(aJSJaCPacket.getFrom()) + ' (available';
  } else {
    item += getUserNick(aJSJaCPacket.getFrom()) + ' (';
    if (aJSJaCPacket.getType()) {
      item += aJSJaCPacket.getType();
    } else {
      item += aJSJaCPacket.getShow();
    }
    if (aJSJaCPacket.getStatus()) {
      item += ' ' + htmlEnc(aJSJaCPacket.getStatus());
    }
  }
  item += ')';

  var lid = '_'+getUserNick(aJSJaCPacket.getFrom());
  var listbox = document.getElementById('roster');
  var entry = document.getElementById(lid);
  
  if (entry) {
    //if nick already in roster, update it
    entry.setAttribute('label', item);
  } else {
    //or create a new entry
    var listitem = document.createElement('listitem');
    listitem.setAttribute('label', item);
    listitem.setAttribute('id', lid);
    listbox.appendChild(listitem);
  }
}

//JSJaC connection handler
function handleConnected() {
  //Must send special Presence message before entering the chatroom
  var aPresence = new JSJaCPresence();
  aPresence.setTo(chatroom + '/' + nick);
  aPresence.setFrom('muckl@semeteys.org');
  
  var x = aPresence.getDoc().createElement('x');
  x.setAttribute('xmlns','http://jabber.org/protocol/muc');
  aPresence.getNode().appendChild(x);
  con.send(aPresence);
}

//JSJaC status change handler (TODO: decide if it sould be used)
function handleStatusChange(status) {
}

//Connects user to groupchat
function doLogin() {
  try {
    //Setup args for contructor
    oArgs = new Object();
    oArgs.httpbase = 'http://www.semeteys.org/chat/http-poll/';
    oArgs.timerval = 2000;

    //Events handlers
    con = new JSJaCHttpPollingConnection(oArgs);
    con.registerHandler('message',handleMessage);
    con.registerHandler('presence',handlePresence);
    con.registerHandler('iq',handleEvent);
    con.registerHandler('onconnect',handleConnected);
    con.registerHandler('onerror',handleError);
    con.registerHandler('status_changed',handleStatusChange);

    //Setup args for connect method
    oArgs = new Object();
    oArgs.domain = 'semeteys.org';
    oArgs.username = 'muckl';
    oArgs.resource = 'xuleditor';
    oArgs.pass = 'muckl';
    //oArgs.register = false;
    con.connect(oArgs);

  } catch (e) {
    document.getElementById('err').value = e.toString();
  } finally {
    return false;
  }
}

//Sends content of "input" textbox to the groupchat
function sendMsg() {
  var msg = document.getElementById('input').value;
  if (msg == '') return false;

  var aMsg = new JSJaCMessage();
  aMsg.setType('groupchat');
  aMsg.setTo(chatroom);
  aMsg.setBody(msg);
  con.send(aMsg);

  document.getElementById('input').value = '';

  return false;
}

//Disconnect from groupchat
function doLogout() {
  if (con && con.connected()) {
    con.disconnect();
    document.getElementById('input').value = '';
    document.getElementById('conversation').innerHTML = '';
    var myRoster = document.getElementById('roster');
    while (myRoster.firstChild) {
      myRoster.removeChild(myRoster.firstChild);
    }
    document.getElementById("chat-start").setAttribute("disabled", "false");
    document.getElementById("chat-end").setAttribute("disabled", "true");
    document.getElementById("chat-nick").setAttribute("disabled", "false");
  }
}

//Let user chances it's nickname
function changeNick() {
  var prefManager = Components.classes["@mozilla.org/preferences-service;1"]
            .getService(Components.interfaces.nsIPrefBranch);
  var currentnick = prefManager.getCharPref("extensions.qsos-xuled.nick", "_");
  
  var newnick = window.prompt(strbundle.getString("nick"), currentnick);
  if (newnick != currentnick) {
    nick = newnick
    prefManager.setCharPref("extensions.qsos-xuled.nick", nick);
  }
}

////////////////////////////////////////////////////////////////////
// Menu "File" functions
////////////////////////////////////////////////////////////////////

//////////////////////////
//Submenu "File/New"
//////////////////////////
//Checks Document's state before opening a new one
function checknewFile() {
  if (myDoc) {
    if (docChanged == "true") {
      confirmDialog(strbundle.getString("closeAnyway"), closeFile);
    } else {
      closeFile();
    }
  }
  newFileDialog();
}

//Menu "New File"
//Shows the new.xul window in modal mode
function newFileDialog() {
  try {
    netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
  } catch (e) {
    alert("Permission to open file was denied.");
  }
  window.openDialog('chrome://qsos-xuled/content/new.xul','Properties','chrome,dialog,modal',myDoc,openRemoteFile);
}

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
  fp.init(window, strbundle.getString("selectFile"), nsIFilePicker.modeOpen);
  fp.appendFilter(strbundle.getString("QSOSFile"),"*.qsos");
  var res = fp.show();
  
  if (res == nsIFilePicker.returnOK) {
    myDoc = new Document(fp.file.path);
    myDoc.load();
    
    //Window's title
    document.getElementById("QSOS").setAttribute("title", strbundle.getString("QSOSEvaluation")+"  "+myDoc.getappname());
    
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

    //docChanged = "false";
    //myDoc = window.arguments[0];
    var authors = myDoc.getauthors();
    var mylist = document.getElementById("f-a-list");
    for(var i=0; i < authors.length; i++) {
      var listitem = document.createElement("listitem");
      listitem.setAttribute("label", authors[i].name);
      listitem.setAttribute("value", authors[i].email);
      mylist.appendChild(listitem);
    }
        
    freezeGeneric("");
    //Menu management
    document.getElementById("file-close").setAttribute("disabled", "false");
    document.getElementById("file-saveas").setAttribute("disabled", "false");

    //Draw top-level SVG chart
    drawChart();
  }
}

//Checks Document's state before opening a new one
function checkopenFile() {
  if (myDoc) {
    if (docChanged == "true") {
      confirmDialog(strbundle.getString("closeAnyway"), closeFile);
    } else {
      closeFile();
    }
  }
  openFile();
}

//////////////////////////
//Submenu "File/Load Remote File"
//////////////////////////
//Shows the load.xul window in modal mode
function loadRemoteDialog() {
  try {
    netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
  } catch (e) {
    alert("Permission to open file was denied.");
  }
  window.openDialog('chrome://qsos-xuled/content/load.xul','Properties','chrome,dialog,modal',myDoc,openRemoteFile);
}

function openRemoteFile(url) {
  if (url == "") return;

  myDoc = new Document("");
  myDoc.loadremote(url);

  //Window's title
  document.getElementById("QSOS").setAttribute("title", strbundle.getString("QSOSEvaluation")+"  "+myDoc.getappname());
  
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

  //Draw top-level SVG chart
  drawChart();
}

//Checks Document's state before opening a new one
function checkopenRemoteFile() {
  if (myDoc) {
    if (docChanged == "true") {
      confirmDialog(strbundle.getString("closeAnyway"), closeFile);
    } else {
      closeFile();
    }
  }
  loadRemoteDialog();
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
  if (criterion.children != "null") {
    treeitem.setAttribute("open", "false");
    treeitem.appendChild(buildsubtree(criterion.children));
  }
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
//Submenu "File/Save local file"
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
  fp.init(window, strbundle.getString("saveFileAs"), nsIFilePicker.modeSave);
  fp.appendFilter(strbundle.getString("QSOSFile"),"*.qsos");
  var res = fp.show();
  if (res == nsIFilePicker.returnOK) {
    myDoc.setfilename(fp.file.path);
    myDoc.write();
    docChanged = "false";
  }
}

//////////////////////////
//Submenu "File/Save remote file"
//////////////////////////
//Saves modifications to a new QSOS XML file
function saveRemote() {
  var prefManager = Components.classes["@mozilla.org/preferences-service;1"]
          .getService(Components.interfaces.nsIPrefBranch);
  var saveremote = prefManager.getCharPref("extensions.qsos-xuled.saveremote");
  
  myDoc.writeremote(saveremote);
}

//////////////////////////
//Submenu "File/Close"
//////////////////////////
//Closes the QSOS XML file and resets window
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
  
  document.getElementById("f-a-name").value = "";
  document.getElementById("f-a-email").value = "";
  
  document.getElementById("t-software").setAttribute("label", strbundle.getString("softwareLabel"));
  document.getElementById("t-c-title").setAttribute("label", strbundle.getString("criterionLabel"));
  
  document.getElementById("f-c-desc0").setAttribute("label", strbundle.getString("score0Label"));
  document.getElementById("f-c-desc1").setAttribute("label", strbundle.getString("score1Label"));
  document.getElementById("f-c-desc2").setAttribute("label", strbundle.getString("score2Label"));
  document.getElementById("f-c-score").selectedIndex = -1;
  document.getElementById("f-c-comments").value = "";
  
  init();
  myDoc = null;
  id = null;
  
  var tree = document.getElementById("mytree");
  var treechildren = document.getElementById("myTreechildren");
  tree.removeChild(treechildren);
  clearChart();
  clearLabels();
}

//Checks Document's state before closing it
function checkcloseFile() {
  if (docChanged == "true") {
    confirmDialog(strbundle.getString("saveBefore"), saveFile);
  }
  closeFile();
}

//////////////////////////
//Submenu "File/Exit"
//////////////////////////
//Exits application
function exit() {
  doLogout();
  self.close();
}

//Checks Document's state before exiting
function checkexit() {
  if (docChanged == "true") {
    confirmDialog(strbundle.getString("exitAnyway"), exit);
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
  document.getElementById("f-a-list").disabled = bool;
  document.getElementById("f-a-name").disabled = bool;
  document.getElementById("f-a-email").disabled = bool;
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
  if (tree.currentIndex != -1) {
    id = tree.view.getItemAtIndex(tree.currentIndex).firstChild.firstChild.getAttribute("id");
    //document.getElementById("t").selectedIndex = 1;
    //document.getElementById("t-c-title").setAttribute("label", myDoc.getkeytitle(id));
    
    document.getElementById("f-c-desc0").setAttribute("label", "0: "+myDoc.getkeydesc0(id));
    document.getElementById("f-c-desc1").setAttribute("label", "1: "+myDoc.getkeydesc1(id));
    document.getElementById("f-c-desc2").setAttribute("label", "2: "+myDoc.getkeydesc2(id));
    var score = myDoc.getkeyscore(id);
    
    if (score == "-1") {
      document.getElementById("f-c-deck").selectedIndex = "0";
      document.getElementById("f-c-desc").value = myDoc.getkeydesc(id);
      freezeScore("true");
    } else {
      document.getElementById("f-c-score").selectedIndex = score;
      document.getElementById("f-c-deck").selectedIndex = "1";
      freezeScore("");
    }
  
    document.getElementById("f-c-comments").value = myDoc.getkeycomment(id);
    freezeComments("");
  
    if (myDoc.hassubelements(id)) {
      drawChart(id);
    } else {
      if (document.getElementById("t-s").selectedIndex == 1) {
        var parentId = myDoc.getparent(id);
        if (parentId) drawChart(parentId);
      }
    }
  }
}

//Forces the selection of element with id in the criteria tree
function selectItem(id) {
  expandTree(true);
  tree = document.getElementById("mytree");
  for(i=0; i < tree.view.rowCount; i++) {
    currentId = tree.view.getItemAtIndex(i).firstChild.firstChild.getAttribute("id");
    if (currentId == id) {
      tree.view.selection.select(i);
      if (document.getElementById("t-s").selectedIndex != 1) tree.treeBoxObject.scrollToRow(i);
      break;
    }
  }
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

//Triggered when an author is select in the list
function changeAuthor(author) {
  document.getElementById("f-a-name").value = author.label;
  document.getElementById("f-a-email").value = author.value;
}

//Triggered when an author is added
function addAuthor() {
  var mylist = document.getElementById("f-a-list");
  var listitem = document.createElement("listitem");
  listitem.setAttribute("label", document.getElementById("f-a-name").value);
  listitem.setAttribute("value", document.getElementById("f-a-email").value);
  mylist.appendChild(listitem);
  myDoc.addauthor(document.getElementById("f-a-name").value, document.getElementById("f-a-email").value);
  docChanged = "true";
  document.getElementById("file-save").setAttribute("disabled", "false");
}

//Triggered when an author is deleted
function deleteAuthor() {
  var mylist = document.getElementById("f-a-list");
  mylist.removeChild(mylist.selectedItem);
  alert(document.getElementById("f-a-name").value);
  myDoc.delauthor(document.getElementById("f-a-name").value);
  document.getElementById("f-a-name").value = "";
  document.getElementById("f-a-email").value = "";
  docChanged = "true";
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

////////////////////////////////////////////////////////////////////
// SVG Chart functions
////////////////////////////////////////////////////////////////////

const SCALE = 100; //1 QSOS unit in pixels
const FONT_SIZE = SCALE/10;

//Clear the SVG chart
function clearChart() {
  var myChart = document.getElementById("chart");
  while (myChart.firstChild) {
    myChart.removeChild(myChart.firstChild);
  }
}

//Draw the SVG chart of a criterion
//criterion: if not specified, the top-level chart of sections is displayed
function drawChart(name) {
  clearChart();
  var myChart = document.getElementById("chart");
  //var width = myChart.parentNode.width.animVal.value / 2;
  //var height = myChart.parentNode.height.animVal.value / 2;
  var width = 400;
  var height = 250;
  myChart.setAttribute("transform", "translate("+width+","+height+")");
  
  //Collect charting data
  var myScores = (name)?myDoc.getSubChartData(name):myDoc.getChartData();
  
  //Chart's label
  clearLabels();
  var marker = null;
  
  if (name) marker = addLabel(name, null);
  var parentName = myDoc.getChartDataParent(name);
  
  while (parentName != null) {
    marker = addLabel(parentName, marker);
    parentName = myDoc.getChartDataParent(parentName);
  }
  addFirstLabel(marker);
  
  //draw chart's axis
  drawAxis(myScores.length);
  
  //draw path between points on each axis
  var myPath = document.createElementNS("http://www.w3.org/2000/svg", "path");
  var myD = "";
  var angle;
  for (i=0; i < myScores.length; i++) {
    myD += (i==0)?"M":"L";
    angle = (i+1)*2*Math.PI/(myScores.length);
    myD += " " + (myScores[i].score)*SCALE*Math.cos(angle) + " " + (myScores[i].score)*SCALE*Math.sin(angle) + " ";
    //2.1 = 2 + 0.1 of padding before actual text display
    drawText(2.1*SCALE*Math.cos(angle), 2.1*SCALE*Math.sin(angle), myScores[i]);
  }
  myD += "z";
  
  myPath.setAttribute("d", myD);
  myPath.setAttribute("fill", "none");
  myPath.setAttribute("stroke", "red");
  myPath.setAttribute("stroke-width", "2");
  
  myChart.appendChild(myPath);
}

//Add the root label of the chart navigation bar
//marker: label before which the new label is to be inserted, can be null
function addFirstLabel(marker) {
  var label = document.getElementById("chart-label");
  var newLabel = document.createElement("label");
  newLabel.setAttribute("value", myDoc.getappname() + " " + myDoc.getrelease());
  newLabel.setAttribute("onclick", "drawChart()");
  newLabel.style.cursor = "pointer";

  if (marker) {
    label.insertBefore(newLabel, marker);
  } else {
    label.appendChild(newLabel);
  }

  return newLabel;
}

//Add a label to the chart navigation bar
function addLabel(name, marker) {
  var label = document.getElementById("chart-label");
  var newLabel = document.createElement("label");
  newLabel.setAttribute("value", ">  " + myDoc.getkeytitle(name));
  newLabel.setAttribute("onclick", "selectItem(\"" + name + "\"); drawChart(\"" + name + "\")");
  newLabel.style.cursor = "pointer";

  if (marker) {
    label.insertBefore(newLabel, marker);
  } else {
    label.appendChild(newLabel);
  }

  return newLabel;
}

//Clear all labels
function clearLabels() {
  var label = document.getElementById("chart-label");
  while (label.firstChild) {
    label.removeChild(label.firstChild);
  }
}

//draw "n" equidistant axis
function drawAxis(n) {
  drawCircle(0.5*SCALE);
  drawCircle(SCALE);
  drawCircle(1.5*SCALE);
  drawCircle(2*SCALE);
  
  for (i=1; i < n+1; i++) {
    drawSingleAxis(2*i*Math.PI/n);
  }
}

//draw a single axis at "angle" (in radians) from angle 0	
function drawSingleAxis(angle) {
  x2 = 2*SCALE*Math.cos(angle);
  y2 = 2*SCALE*Math.sin(angle);
  drawLine(0, 0, x2, y2);
}

//draw a circle of "r" radius
function drawCircle(r) {
  var myChart = document.getElementById("chart");
  
  var myCircle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
  myCircle.setAttribute("cx", 0);
  myCircle.setAttribute("cy", 0);
  myCircle.setAttribute("r", r);
  myCircle.setAttribute("fill", "none");
  myCircle.setAttribute("stroke", "blue");
  myCircle.setAttribute("stroke-width", "1");
  
  myChart.appendChild(myCircle);
}

//draw a line between two points
function drawLine(x1, y1, x2, y2) {
  var myChart = document.getElementById("chart");
  
  var myLine = document.createElementNS("http://www.w3.org/2000/svg", "line");
  myLine.setAttribute("x1", x1);
  myLine.setAttribute("y1", y1);
  myLine.setAttribute("x2", x2);
  myLine.setAttribute("y2", y2);
  myLine.setAttribute("stroke", "green");
  myLine.setAttribute("stroke-width", "1");
  
  myChart.appendChild(myLine);
}

//draw an axis legend
//x, y: coordinates
//myScore: object chartData (cf. Document.js)
function drawText(x, y, myScore) {
  var myChart = document.getElementById("chart");
  
  var myText = document.createElementNS("http://www.w3.org/2000/svg", "text");
  myText.setAttribute("x", x);
  myText.setAttribute("y", y);
  myText.setAttribute("font-family", "Verdana");
  myText.setAttribute("font-size", FONT_SIZE);

  if (myScore.score) {
    myText.setAttribute("font-size", FONT_SIZE);
  
    if (myScore.score) {
      myText.setAttribute("fill", "green");
    } else {
      myText.setAttribute("fill", "red");
    }
    
    if (myScore.children) {
      myText.setAttribute("onclick", "selectItem(\"" + myScore.name + "\"); drawChart(\"" + myScore.name + "\")");	
    } else {
      myText.setAttribute("onclick", "selectItem(\"" + myScore.name + "\"); document.getElementById('t-s').selectedIndex = 1");
    }	
    myText.style.cursor = "pointer";
  
    myText.appendChild(document.createTextNode(myScore.title));
    myChart.appendChild(myText);
    
    //text position is ajusted to be outside the circle shape
    myTextLength = myText.getComputedTextLength();
    myX = (Math.abs(x)==x)?x:x-myTextLength;
    myY = (Math.abs(y)==y)?y+FONT_SIZE:y;
    myText.setAttribute("x", myX);
    myText.setAttribute("y", myY);myText.setAttribute("fill", "green");
  } else {
    myText.setAttribute("fill", "red");
  }
  
  if (myScore.children) {
    myText.setAttribute("onclick", "selectItem(\"" + myScore.name + "\"); drawChart(\"" + myScore.name + "\")");	
  } else {
    myText.setAttribute("onclick", "selectItem(\"" + myScore.name + "\"); document.getElementById('t-s').selectedIndex = 1");
  }	
  myText.style.cursor = "pointer";

  myText.appendChild(document.createTextNode(myScore.title));
  myChart.appendChild(myText);
  
  //text position is ajusted to be outside the circle shape
  myTextLength = myText.getComputedTextLength();
  myX = (Math.abs(x)==x)?x:x-myTextLength;
  myY = (Math.abs(y)==y)?y+FONT_SIZE:y;
  myText.setAttribute("x", myX);
  myText.setAttribute("y", myY);
}