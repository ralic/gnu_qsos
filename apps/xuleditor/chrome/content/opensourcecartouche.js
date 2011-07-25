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
 *  opensourcecartouche.js: functions associated with the opensourcecartouche tab
 *
**/


function displayXML(xmlObject) {
  var serializer = new XMLSerializer();
  var xml = serializer.serializeToString(xmlObject);
  alert(xml);
}


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


// Triggered when a team member is added
function addTeamMember(type) {
  try{
    var list = document.getElementById(type + "Team");
    var listitem = document.createElement("listitem");
    var name = document.getElementById(type + "Name").value;
    var email = document.getElementById(type + "Email").value;
    var company = document.getElementById(type + "Company").value;

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
      var listcellCompany = document.createElement("listcell");

      listcellName.setAttribute("label", name);
      listcellEmail.setAttribute("label", email);
      listcellCompany.setAttribute("label", company);
      listitem.appendChild(listcellName);
      listitem.appendChild(listcellEmail);
      listitem.appendChild(listcellCompany);
      list.appendChild(listitem);

      try {
      myDoc.addTeamMember(type, name, email, company);
      } catch (e) { alert(e.message); }

      docHasChanged(true);
      document.getElementById("del" + type + "Button").disabled = "";
    }
  } catch(e) { alert(e.message); }
}


// Triggered when a team member is deleted
function delTeamMember(type) {
  try{
  var list = document.getElementById(type + "Team");

  if (list.selectedItem == null) {
    alert(strbundle.getString("selectPerson"));
    return;
  }

  if (list.getRowCount() <= 1) {
    document.getElementById("del" + type + "Button").disabled = true;
    if (list.getRowCount() == 0) {
      alert(strbundle.getString("noOneToRemove"));
      return;
    }
  }

  try {
  myDoc.delTeamMember(type, list.selectedItem.firstChild.getAttribute("label"), list.selectedItem.childNodes[1].getAttribute("label"));
  } catch (e) { alert(e.message); }

  list.removeChild(list.selectedItem);

  document.getElementById(type + "Name").value = "";
  document.getElementById(type + "Email").value = "";
  document.getElementById(type + "Company").value = "";

  docHasChanged();
  } catch(e) { alert(e.message); }
}
