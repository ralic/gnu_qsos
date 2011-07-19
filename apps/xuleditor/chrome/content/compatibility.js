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
//     alert("First, choose the right XSLT to export the template part to FreeMind.");
//     var filename = pickAFile(".xsl", "XSLT");
//     if (filename == "") {
//       return false;
//     }
//     var xslt = loadFile(filename);
//     if (xslt == null) {
//       return false;
//     }

    // FIXME find a way to open the right XSLT from the extension
    /*xslt = loadXSLT("chrome://qsos-xuled/content/freemind_to_qsos.xsl");
     *  if (xslt == null) {
     *  return false;
    }*/

    var xslt = parseXML(qsos_to_freemind);
    var error = xslt.getElementsByTagName("parsererror");
    if (error.length == 1) {
      alert("An error occurred while parsing the XSLT! This is a bug.");
      alert("loadFile: " + strbundle.getString("parsingError") + "\n\n" + error[0].textContent);
      return false;
    }

    // FIXME
//     alert("FIXME");
    var toTrans = myDoc.getSheet().getElementsByTagName("section")[0];

    var processor = new XSLTProcessor();
    processor.importStylesheet(xslt);
    element = processor.transformToDocument(toTrans);

    var serializer = new XMLSerializer();
    var xmlOutput = serializer.serializeToString(toTrans);
    alert("Output:\n" + xmlOutput);

    getPrivilege();
    alert("Choose the file to save the template.");
    var nsIFilePicker = Components.interfaces.nsIFilePicker;
    var fp = Components.classes["@mozilla.org/filepicker;1"].createInstance(nsIFilePicker);
    fp.init(window, strbundle.getString("saveFileAs"), nsIFilePicker.modeSave);
    fp.appendFilter(strbundle.getString("FreeMindTemplate"),"*.mm");
    var res = fp.show();
    if ((res != nsIFilePicker.returnOK) && (res != nsIFilePicker.returnReplace)) {
      return false;
    }
    var filename = fp.file.path;
    myDoc.writeXMLtoFile(element, filename);
  } catch(e) {
    alert("exportToFreeMind: " + e.message);
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

var qsos_to_freemind = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\
<xsl:stylesheet xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" version=\"1.0\">\
<xsl:output method=\"xml\" indent=\"yes\" encoding=\"UTF-8\"/>\
\
<xsl:template match=\"document\">\
<xsl:element name=\"map\">\
<xsl:attribute name=\"version\">0.7.1</xsl:attribute>\
<xsl:element name=\"node\">\
<xsl:attribute name=\"ID\"><xsl:value-of select=\"header/Cartouche/Component/ComponentName\"/></xsl:attribute>\
<richcontent TYPE=\"NODE\"><html>\
<head></head>\
<body><p style=\"text-align: center\">\
<xsl:value-of select=\"header/Cartouche/Component/ComponentName\"/><br/>\
<xsl:value-of select=\"header/Cartouche/Component/ComponentVersion\"/>\
</p></body>\
</html></richcontent>\
<font NAME=\"SansSerif\" BOLD=\"true\" SIZE=\"12\"/>\
<xsl:apply-templates select=\"section\"/>\
</xsl:element>\
</xsl:element>\
</xsl:template>\
\
<xsl:template match=\"section\">\
<node ID=\"{@name}\" TEXT=\"{@title}\">\
<xsl:if test=\"position() mod 2 = 0\">\
<xsl:attribute name=\"POSITION\">left</xsl:attribute>\
</xsl:if>\
<xsl:if test=\"position() mod 2 = 1\">\
<xsl:attribute name=\"POSITION\">right</xsl:attribute>\
</xsl:if>\
<font NAME=\"SansSerif\" BOLD=\"true\" SIZE=\"12\"/>\
<xsl:if test=\"desc != ''\">\
<xsl:element name=\"node\">\
<xsl:attribute name=\"TEXT\"><xsl:value-of select=\"desc\"/></xsl:attribute>\
<xsl:attribute name=\"STYLE\">bubble</xsl:attribute>\
<font NAME=\"SansSerif\" ITALIC=\"true\" SIZE=\"10\"/>\
</xsl:element>\
</xsl:if>\
<xsl:apply-templates select=\"element\"/>\
</node>\
</xsl:template>\
\
<xsl:template match=\"element\">\
<xsl:element name=\"node\">\
<xsl:attribute name=\"ID\"><xsl:value-of select=\"@name\"/></xsl:attribute>\
<xsl:attribute name=\"TEXT\"><xsl:value-of select=\"@title\"/></xsl:attribute>\
\
<xsl:if test=\"score = '0'\">\
<xsl:attribute name=\"FOLDED\">true</xsl:attribute>\
<icon BUILTIN=\"button_cancel\"/>\
</xsl:if>\
<xsl:if test=\"score = '1'\">\
<xsl:attribute name=\"FOLDED\">true</xsl:attribute>\
<icon BUILTIN=\"yes\"/>\
</xsl:if>\
<xsl:if test=\"score = '2'\">\
<xsl:attribute name=\"FOLDED\">true</xsl:attribute>\
<icon BUILTIN=\"button_ok\"/>\
</xsl:if>\
\
<xsl:choose>\
<xsl:when test=\"child::element\">\
<xsl:if test=\"desc != ''\">\
<xsl:element name=\"node\">\
<xsl:attribute name=\"TEXT\"><xsl:value-of select=\"desc\"/></xsl:attribute>\
<xsl:attribute name=\"STYLE\">bubble</xsl:attribute>\
<font NAME=\"SansSerif\" ITALIC=\"true\" SIZE=\"10\"/>\
</xsl:element>\
</xsl:if>\
</xsl:when>\
\
<xsl:otherwise>\
<xsl:element name=\"node\">\
<xsl:if test=\"score = '0'\">\
<xsl:attribute name=\"TEXT\"><xsl:value-of select=\"desc0\"/></xsl:attribute>\
</xsl:if>\
<xsl:if test=\"score = '1'\">\
<xsl:attribute name=\"TEXT\"><xsl:value-of select=\"desc1\"/></xsl:attribute>\
</xsl:if>\
<xsl:if test=\"score = '2'\">\
<xsl:attribute name=\"TEXT\"><xsl:value-of select=\"desc2\"/></xsl:attribute>\
</xsl:if>\
<xsl:attribute name=\"STYLE\">bubble</xsl:attribute>\
<font NAME=\"SansSerif\" ITALIC=\"true\" SIZE=\"10\"/>\
</xsl:element>\
\
<xsl:if test=\"comment != ''\">\
<xsl:element name=\"node\">\
<xsl:attribute name=\"TEXT\"><xsl:value-of select=\"comment\"/></xsl:attribute>\
<font NAME=\"SansSerif\" ITALIC=\"true\" SIZE=\"10\"/>\
</xsl:element>\
</xsl:if>\
</xsl:otherwise>\
</xsl:choose>\
\
<xsl:apply-templates select=\"element\"/>\
\
</xsl:element>\
</xsl:template>\
\
</xsl:stylesheet>"
