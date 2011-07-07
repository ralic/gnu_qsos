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
function changeEvalAuthor(elem1, elem2, elem3, author) {
//   for(var i = 0; i < author.childNodes.length; ++i)
//     alert(author.childNodes[i].nodeName + " " + author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName));

  var i = 0;
//   alert(author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName));
//   alert(elem1);
  document.getElementById(elem1).value = author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName);
  ++i;
//   alert(elem2);
//   alert(author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName));
  document.getElementById(elem2).value = author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName);
  ++i;
  document.getElementById(elem3).value = author.childNodes[i].getAttribute(author.childNodes[i].attributes[0].nodeName);
}


// Triggered when an evaluation author is added
function addEvalAuthor() {
  try{
    var list = document.getElementById("evaluationAuthors");
    var listitem = document.createElement("listitem");
    var name = document.getElementById("authorName").value;
    var email = document.getElementById("authorEmail").value;
    var comment = document.getElementById("authorComment").value;

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
function delEvalAuthor() {
  try{
  var list = document.getElementById("evaluationAuthors");

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

  document.getElementById("authorName").value = "";
  document.getElementById("authorEmail").value = "";
  document.getElementById("authorComment").value = "";

  docHasChanged();
  } catch(e) { alert(e.message); }
}
