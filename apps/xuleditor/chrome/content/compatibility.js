/**
 *  Copyright (C) 2006-2011 Atos
 *
 *  Authors: Timothée Ravier <travier@portaildulibre.fr>
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
 *  compability.js: functions to import/export/update evaluations thanks to xslt sheet
 *
 **/

// chrome://qsos-xuled/skin/edit-redo.png
// chrome://qsos-xuled/xslt/freemind_to_qsos.xsl
// chrome://qsos-xuled/xslt/qsos_to_freemind.xsl
// chrome://qsos-xuled/xslt/qsos_to_new_qsos.xsl


function newFileFromTemplate() {
  try {
    alert("First, choose the template you want to use for your evaluation.");
    var filename = pickAFile(".mm", strbundle.getString("FreeMindTemplate"));
    if (filename == "") {
      return false;
    }
    var xml = loadFile(filename);
    if (xml == null) {
      return false;
    }

    alert("Then, choose the right XSLT sheet to convert it to an evaluation.");
    filename = pickAFile(".xsl", "XSLT");
    if (filename == "") {
      return false;
    }
    var xslt = loadFile(filename);
    if (xslt == null) {
      return false;
    }

    // FIXME find a way to open the right XSLT from the extension
    /*xslt = loadXSLT("chrome://qsos-xuled/content/freemind_to_qsos.xsl");
    if (xslt == null) {
      return false;
    }*/

    myDoc = new Document();

    var processor = new XSLTProcessor();
    processor.importStylesheet(xslt);
    myDoc.setSheet(processor.transformToDocument(xml));

    var serializer = new XMLSerializer();
    var xmlOutput = serializer.serializeToString(myDoc.sheet);
    alert("Output:\n" + xmlOutput);

    try {
      setupEditorForEval();
    } catch (e) {
      alert("newFileFromTemplate: an error occured while setting up the editor: " + e.message);
      closeFile();
      return false;
    }
  } catch (e) {
    alert("newFileFromTemplate: " + e.message);
  }
  alert("newFileFromTemplate: fin");
}


function updateFromTemplate() {
  try{
  if (myDoc == null) {
    alert("updateFromTemplate: You need to open an evaluation before trying to update it!");
    return false;
  }

  // Open template file dialog to choose a template to take stuff from
  alert("First, choose the template you want to use in order to update your evaluation.");
  var filename = pickAFile(".mm", strbundle.getString("FreeMindTemplate"));
  if (filename == "") {
    return false;
  }
  var template = loadFile(filename);
  if (template == null) {
    return false;
  }

  alert("Then, choose the right XSLT to convert this template to a QSOS evaluation.");
  filename = pickAFile(".xsl", "XSLT");
  if (filename == "") {
    return false;
  }
  var xslt = loadFile(filename);
  if (xslt == null) {
    return false;
  }

  var processor = new XSLTProcessor();
  processor.importStylesheet(xslt);
  var templateXML = processor.transformToDocument(template);

  // Do the update
  alert("TODO");
//   alert(sheet);
//   alert(template);
//   template.dump();
  }catch(e){ alert(e.message); }

  return true;
}


function exportOSC() {
  try{
    getPrivilege();
    var nsIFilePicker = Components.interfaces.nsIFilePicker;
    var fp = Components.classes["@mozilla.org/filepicker;1"].createInstance(nsIFilePicker);
    fp.init(window, strbundle.getString("saveFileAs"), nsIFilePicker.modeSave);
    fp.appendFilter(strbundle.getString("OSC"),"*.osc");
    var res = fp.show();
    if ((res != nsIFilePicker.returnOK) && (res != nsIFilePicker.returnReplace)) {
      return false;
    }
    var filename = fp.file.path;
    myDoc.writeOSC(filename);
  } catch(e) {
    alert("exportOSC: " + e.message);
    return false;
  }

  alert(strbundle.getString("saveSuccessOSC") + " " + filename);

  return true;
}


function exportToFreeMind() {
  try {
    alert("First, choose the right XSLT to export the template part to FreeMind.");
    var filename = pickAFile(".xsl", "XSLT");
    if (filename == "") {
      return false;
    }
    var xslt = loadFile(filename);
    if (xslt == null) {
      return false;
    }

    // FIXME find a way to open the right XSLT from the extension
    /*xslt = loadXSLT("chrome://qsos-xuled/content/freemind_to_qsos.xsl");
     *  if (xslt == null) {
     *  return false;
    }*/

    // FIXME
    alert("FIXME");
    var toTrans = myDoc.getSheet().getElementsByTagName("section")[0];

    var processor = new XSLTProcessor();
    processor.importStylesheet(xslt);
    element = processor.transformToDocument(toTrans);

    var serializer = new XMLSerializer();
    var xmlOutput = serializer.serializeToString(toTrans);
    alert("Output:\n" + xmlOutput);

    getPrivilege();
    alert("Then choose the file to save the template.");
    var nsIFilePicker = Components.interfaces.nsIFilePicker;
    var fp = Components.classes["@mozilla.org/filepicker;1"].createInstance(nsIFilePicker);
    fp.init(window, strbundle.getString("saveFileAs"), nsIFilePicker.modeSave);
    fp.appendFilter(strbundle.getString("FreeMindTemplate"),"*.mm");
    var res = fp.show();
    if ((res != nsIFilePicker.returnOK) && (res != nsIFilePicker.returnReplace)) {
      return false;
    }
    var filename = fp.file.path;
    // FIXME Write the file
  } catch(e) {
    alert("exportOSC: " + e.message);
    return false;
  }

  alert(strbundle.getString("saveSuccessFreeMind") + " " + filename);

  return true;
}


function updateFromOldQSOS() {
  try {
    alert("First, choose the evaluation you want to update.");
    var filename = pickAFile(".qsos", strbundle.getString("QSOSFile"));
    if (filename == "") {
      return false;
    }
    var xml = loadFile(filename);
    if (xml == null) {
      return false;
    }

    alert("Then, choose the rigth XSLT to convert your old evaluation to the new version.");
    filename = pickAFile(".xsl", "XSLT");
    if (filename == "") {
      return false;
    }
    var xslt = loadFile(filename);
    if (xslt == null) {
      return false;
    }

    // FIXME find a way to open the right XSLT from the extension
    /*xslt = loadXSLT("chrome://qsos-xuled/content/freemind_to_qsos.xsl");
     i f (xslt == null) {             *
     return false;
     }*/

    myDoc = new Document();

    var processor = new XSLTProcessor();
    processor.importStylesheet(xslt);
    myDoc.setSheet(processor.transformToDocument(xml));

    var serializer = new XMLSerializer();
    var xmlOutput = serializer.serializeToString(myDoc.sheet);
    alert("Output:\n" + xmlOutput);

    try {
      setupEditorForEval();
    } catch (e) {
      alert("updateFromOldQSOS: an error occured while setting up the editor: " + e.message);
      closeFile();
      return false;
    }
  } catch (e) {
    alert("updateFromOldQSOS: " + e.message);
  }
}
