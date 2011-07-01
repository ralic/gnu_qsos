/**
 *  Copyright (C) 2006-2011 Atos Origin
 *
 *  Author: Raphael Semeteys <raphael.semeteys@atosorigin.com>
 *          Timothée Ravier <travier@portaildulibre.fr>
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
 *  general.js: functions associated with the general tab
 *
**/


function docHasChanged(bool) {
  if (bool == false){
    docChanged = false;
    document.getElementById("saveFile").disabled = "true";
  } else {
    docChanged = true;
    document.getElementById("saveFile").disabled = "";
  }
}

// Triggered when an author/reviewer/contributor is select in a list
function changePerson(elem1, elem2, elem3, author) {
  var i = 0;
  document.getElementById(elem1).value = author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName);
  ++i;
  document.getElementById(elem2).value = author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName);
  ++i;
  document.getElementById(elem3).value = author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName);
}

// Triggered when an author is added
function addAuthor() {
  var mylist = document.getElementById("f-a-list");
  var listitem = document.createElement("listitem");
  var name = document.getElementById("f-a-name").value;
  var email = document.getElementById("f-a-email").value;
  if (name == "" || email == "") {
    alert("A valid name and e-mail adress are required");
  } else {
    for (var i = 0; i < mylist.getRowCount(); ++i) {
      if (mylist.getItemAtIndex(i).label == name) {
        alert("There already is someone named " + name);
        return;
      }
    }
    listitem.setAttribute("label", name);
    listitem.setAttribute("value", email);
    mylist.appendChild(listitem);
    myDoc.addauthor(name, email);
    docHasChanged();
    document.getElementById("delAuthorButton").disabled = "";
  }
}

// Triggered when an author is deleted
function deleteAuthor() {
  var mylist = document.getElementById("f-a-list");
  if (mylist.selectedItem == null) {
    alert("Select an author to be deleted");
    return;
  }
  if (mylist.getRowCount() <= 1) {
    document.getElementById("delAuthorButton").disabled = true;
    if (mylist.getRowCount() == 0) {
      alert("There isn't any author any more");
      return;
    }
  }
  mylist.removeChild(mylist.selectedItem);
  myDoc.delauthor(document.getElementById("f-a-name").value);
  document.getElementById("f-a-name").value = "";
  document.getElementById("f-a-email").value = "";
  docHasChanged();
}
