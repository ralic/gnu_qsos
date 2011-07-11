/**
 *  Copyright (C) 2006-2011 Atos
 *
 *  Authors: Raphael Semeteys <raphael.semeteys@atos.net>
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
 *  authors.js: functions associated with the authors tab
 *
**/


// Triggered when an author/reviewer/contributor is select in a list
function changeAuthor(type, author) {
  var nameElem = document.getElementById(type + "AuthorName");
  var emailElem = document.getElementById(type + "AuthorEmail");
  var commentElem = document.getElementById(type + "AuthorComment");

  var i = 0;
  nameElem.value = author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName);
  ++i;
  emailElem.value = author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName);
  ++i;
  commentElem.value = author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName);
}


// Triggered when an evaluation author is added
function addAuthor(type) {
  try{
    var list = document.getElementById( type + "Authors");
    var listitem = document.createElement("listitem");
    var name = document.getElementById(type + "AuthorName").value;
    var email = document.getElementById(type + "AuthorEmail").value;
    var comment = document.getElementById(type + "AuthorComment").value;

    if (name == "" || email == "") {
      alert(strbundle.getString("validAuthor"));
    } else {
      for (var i = 1; i <= list.getRowCount(); ++i) {
        if (list.childNodes[i].firstChild.getAttribute("label") == name) {
          alert(strbundle.getString("alreadyAuthor") + " " + name);
          return;
        }
      }

      var listcellName = document.createElement("listcell");
      var listcellEmail = document.createElement("listcell");
      var listcellComment = document.createElement("listcell");

      listcellName.setAttribute("label", name);
      listcellEmail.setAttribute("label", email);
      listcellComment.setAttribute("label", comment);
      listitem.appendChild(listcellName);
      listitem.appendChild(listcellEmail);
      listitem.appendChild(listcellComment);
      list.appendChild(listitem);

//       alert(name + " " + email + " " + comment);
      try {
      myDoc.addEvalAuthor(name, email, comment);
      } catch (e) { alert(e.message); }

      docHasChanged(true);
      document.getElementById("delAuthorButton").disabled = "";
    }
  } catch(e) { alert(e.message); }
}


// Triggered when an evaluation author is deleted
function delAuthor(type) {
  try{
  var list = document.getElementById(type + "Authors");

  if (list.selectedItem == null) {
    alert("Select an author to be deleted"); // TODO localize
    return;
  }

  if (list.getRowCount() <= 1) {
    document.getElementById("delAuthorButton").disabled = true;
    if (list.getRowCount() == 0) {
      alert("There isn't any author any more"); // TODO localize
      return;
    }
  }

//   alert(list.selectedItem.firstChild.getAttribute("label") + " " +  list.selectedItem.childNodes[1].getAttribute("label"));
  try {
  myDoc.delEvalAuthor(list.selectedItem.firstChild.getAttribute("label"), list.selectedItem.childNodes[1].getAttribute("label"));
  } catch (e) { alert(e.message); }

  list.removeChild(list.selectedItem);

  document.getElementById(type + "AuthorName").value = "";
  document.getElementById(type + "AuthorEmail").value = "";
  document.getElementById(type + "AuthorComment").value = "";

  docHasChanged();
  } catch(e) { alert(e.message); }
}
