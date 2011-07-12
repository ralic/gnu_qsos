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
 *  criteria.js: functions associated with the criteria tab
 *
**/

var currentId;


// Forces the selection of element with id in the criteria tree
function selectItem(id) {
  expandTree(true);
  tree = document.getElementById("criteriaTree");
  for(i = 0; i < tree.view.rowCount; ++i) {
    tmpCurrentId = tree.view.getItemAtIndex(i).firstChild.firstChild.getAttribute("id");
    if (tmpCurrentId == id) {
      tree.view.selection.select(i);
      if (document.getElementById("tabBox").selectedIndex != 1) tree.treeBoxObject.scrollToRow(i);
      break;
    }
  }
}


// Expands or collapses the tree
// bool: "false" to collapse, "true" to expand
function expandTree(bool) {
  var treeitems = document.getElementsByTagName("treeitem");
  for (var i = 0; i < treeitems.length ; i++) {
    var children = treeitems[i].getElementsByTagName("treeitem");
    if (children.length > 0) treeitems[i].setAttribute("open", bool);
  }
}


// Triggered when a new criterion is selected in the tree
// Fills criteria's fields with new values
function treeselect(tree) {
//   alert("treeselect: begin");
  //Forces focus to trigger possible onchange event on another XUL element
  document.getElementById("criteriaTree").focus();
  if (tree.currentIndex != -1) {
    id = tree.view.getItemAtIndex(tree.currentIndex).firstChild.firstChild.getAttribute("id");
    //document.getElementById("t").selectedIndex = 1;
    //document.getElementById("t-c-title").setAttribute("label", myDoc.getkeytitle(id));

    document.getElementById("criteriaDescription").value = myDoc.getkeydesc(id);

    var score = myDoc.getkeyscore(id);
    if (score == "-1") {
      document.getElementById("scoreRadiogroup").hidden = "true";
      freezeScore("true");
    } else {
      document.getElementById("scoreRadiogroup").hidden = "";
      document.getElementById("scoreRadiogroup").selectedIndex = score;
      freezeScore("");
    }

    var desc = new Array(myDoc.getkeydesc0(id), myDoc.getkeydesc1(id), myDoc.getkeydesc2(id));
    for (var i = 0; i < desc.length; ++i) {
      if (desc[i] == "") {
        document.getElementById("scoreDescription" + i).setAttribute("label", strbundle.getString("score" + i + "Label"));
      } else {
        document.getElementById("scoreDescription" + i).setAttribute("label", i + ": " + desc[i]);
      }
    }

    document.getElementById("criteriaComments").value = myDoc.getkeycomment(id);
    freezeComments("");

    if (myDoc.hassubelements(id)) {
      drawChart(id);
      currentId = id;
    } else {
      var parentId = myDoc.getparent(id);
      if (parentId) {
          drawChart(parentId);
          // Store the Id for fixing groubox selection
          currentId = parentId;
      }
    }
  }
}


// Triggered when current criteria's comments are modified
function changeComments(xulelement) {
  myDoc.setkeycomment(id, xulelement.value);
  docHasChanged();
}


// Triggered when current criteria's score is modified
function changeScore(score) {
  myDoc.setkeyscore(id, score);
  docHasChanged();
}
