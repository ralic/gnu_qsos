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


function newFileFromTemplate() {
  try {
//     alert("First, choose the template you want to use for your evaluation.");
    var filename = pickAFile(".mm", strbundle.getString("FreeMindTemplate"));
    if (filename == "") {
      return false;
    }
    var xml = loadFile(filename);
    if (xml == null) {
      return false;
    }

//     alert("Then, choose the right XSLT sheet to convert it to an evaluation.");
    var xslt = parseXML(freemind_to_qsos_2_0);
    var error = xslt.getElementsByTagName("parsererror");
    if (error.length == 1) {
      alert("An error occurred while parsing the XSLT! This is a bug. Please report it using this description:\nThe Freemind to QSOS 2.0 XSLT doesn't work.\nPlease include your evaluations in the report.");
      alert("loadFile: " + strbundle.getString("parsingError") + "\n\n" + error[0].textContent);
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
    var xmlOutput = serializer.serializeToString(myDoc.getSheet());
//     alert("Output:\n" + xmlOutput);

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
//   alert("newFileFromTemplate: fin");
}


function updateFromTemplate() {
  try{
    if (myDoc == null) {
      alert("updateFromTemplate: You need to open an evaluation before trying to update it!");
      return false;
    }

    // Open template file dialog to choose a template to take stuff from
//     alert("Choose the template you want to use in order to update your evaluation.");
    var filename = pickAFile(".mm", strbundle.getString("FreeMindTemplate"));
    if (filename == "") {
      return false;
    }
    var template = loadFile(filename);
    if (template == null) {
      return false;
    }

    var xslt = parseXML(freemind_to_qsos_2_0);
    var error = xslt.getElementsByTagName("parsererror");
    if (error.length == 1) {
      alert("An error occurred while parsing the XSLT! This is a bug. Please report it using this description:\nThe Freemind to QSOS 2.0 XSLT doesn't work.\nPlease include your evaluations in the report.");
      alert("loadFile: " + strbundle.getString("parsingError") + "\n\n" + error[0].textContent);
      return false;
    }

    var processor = new XSLTProcessor();
    processor.importStylesheet(xslt);
    var templateXML = processor.transformToDocument(template);
  } catch(e) {
    alert("updateFromTemplate: Problem in file loading or processing: " + e.message);
  }

  // Type and version testing
  try {
    var currentType = myDoc.get("qsosMetadata/template/type");

    var nodes = templateXML.evaluate("//qsosMetadata/template/type", templateXML, null, XPathResult.ANY_TYPE, null);
    var node = nodes.iterateNext();
    if (node) {
      var newType = node.textContent;
    } else {
      alert(strbundle.getString("noTemplateType"));
      return false;
    }

    if (currentType != newType) {
      alert(strbundle.getString("wrongTemplateType") + " " + currentType + " != " + newType);
      return false;
    } else {
      var currentVersion = myDoc.get("qsosMetadata/template/version");

      var nodes = templateXML.evaluate("//qsosMetadata/template/version", templateXML, null, XPathResult.ANY_TYPE, null);
      var node = nodes.iterateNext();
      if (node) {
        var newVersion = node.textContent;
      } else {
        alert(strbundle.getString("noVersion"));
        return false;
      }

      if (newVersion != currentVersion) {
        if(confirm(strbundle.getString("confirmUpdate") + " " + currentVersion + " -> " + newVersion + ".") == false) {
            return false;
        }
      } else {
        alert(strbundle.getString("noNewVersion"));
        return false;
      }
    }
  } catch(e) {
    alert("updateFromTemplate: Problem in info testing stuff: " + e.message);
  }

  // Do the update
  try {
    // Creates of copy in order to work on the sheet easily
    var tmpTemplateXML = parseXML(serializeXML(myDoc.getSheet()));
    var newSheet = mergeSections(myDoc.getSheet(), templateXML, tmpTemplateXML);
  } catch(e) {
    alert("updateFromTemplate: " + e.message);
  }

  myDoc.setSheet(newSheet);

  // Resets the criteria tab
  document.getElementById("criteriaDescription").value = "";
  document.getElementById("scoreDescription0").label = strbundle.getString("score0Label");
  document.getElementById("scoreDescription1").label = strbundle.getString("score1Label");
  document.getElementById("scoreDescription2").label = strbundle.getString("score2Label");
  document.getElementById("scoreRadiogroup").selectedIndex = -1;
  document.getElementById("criteriaComments").value = "";

  var tree = document.getElementById("criteriaTree");
  var treechildren = document.getElementById("myTreechildren");
  tree.removeChild(treechildren);
  clearChart();
  clearLabels();

  try {
    var tree = document.getElementById("criteriaTree");
    var treechildren = buildtree();
    tree.appendChild(treechildren);
  } catch (e) {
    alert("updateFromTemplate: can't populate the criteria tree: " + e.message);
    return false;
  }

  docHasChanged();

  return true;
}


function mergeSections(oldSheet, newSheet, tmpOldSheet) {
  try {
    // Removes the old sections form the oldSheet
    oldDocument = oldSheet.evaluate("//document", oldSheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null).iterateNext();
    var oldSections = oldSheet.evaluate("//document/section", oldSheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
    var section = oldSections.iterateNext();
    while (section != null) {
      oldDocument.removeChild(section);
      oldSections = oldSheet.evaluate("//document/section", oldSheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
      section = oldSections.iterateNext();
    }

    // Adds the new ones from the newSheet to the oldSheet, and try to update them if they were previously filled in oldSheet
    var oldDocument = oldSheet.evaluate("//document", oldSheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null).iterateNext();
    var newSections = newSheet.evaluate("//document/section", newSheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
    var section = newSections.iterateNext();
    while (section != null) {
      oldDocument.appendChild(updateNode(section, tmpOldSheet));
      newSections = newSheet.evaluate("//document/section", newSheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
      section = newSections.iterateNext();
    }
  } catch (e) {
    alert("mergeSections: " + e.message);
  }

  return oldSheet;
}


function updateNode(section, oldSheet) {
  try {
    var comments = section.getElementsByTagName("comment");
    var len = comments.length;
//     alert(len);
    for (var i = 0; i < len; ++i) {
//       alert(comments[i]);
      var id = comments[i].parentNode.getAttribute("name");
//       alert(id);
//       alert("//element[@name='" + id + "']");
      var node = oldSheet.evaluate("//element[@name='" + id + "']", oldSheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null).iterateNext();
//       alert(node);
      if (node) {
//         alert("trouvé !");
//         alert(node.getElementsByTagName("comment")[0]);
//         alert(node.getElementsByTagName("comment")[0].textContent);
        comments[i].textContent = node.getElementsByTagName("comment")[0].textContent;
        comments[i].nextSibling.textContent = node.getElementsByTagName("score")[0].textContent;
      }
    }
  } catch (e) {
    alert("updateNode: " + e.message);
  }

  return section;
}


// function getSections(sheet) {
//   var nodes = sheet.evaluate("//document/qsosMetadata", sheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
//   var node = nodes.iterateNext();
//   if (node) {
//     nodes.removeChild(node);
//   } else {
//     alert("This evaluation doesn't have a QSOS Metadata part!");
//   }
//   var nodes = sheet.evaluate("//document/openSourceCartouche", sheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
//   var node = nodes.iterateNext();
//   if (node) {
//     nodes.removeChild(node);
//   } else {
//     alert("This evaluation doesn't have an OpenSource Cartouche part!");
//   }
//   sheet.dump();
//
//   return sheet;
// }
//
//
// function removeSections(sheet) {
//   var nodes = sheet.evaluate("//document/section", sheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
//   var node = nodes.iterateNext();
//   while (node != null) {
//     nodes.removeChild(node);
//     node = nodes.iterateNext();
//   }
//
//   return sheet;
// }
//
//
// function setSections(updateSheet, newSheet) {
//   var nodes = sheet.evaluate("//evaluation/authors", sheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
//   var node = nodes.iterateNext();
//   var author = sheet.createElement("author");
//   var nameElem = sheet.createElement("name");
//   nameElem.appendChild(document.createTextNode(name));
//   var emailElem = sheet.createElement("email");
//   emailElem.appendChild(document.createTextNode(email));
//   var commentElem = sheet.createElement("comment");
//   commentElem.appendChild(document.createTextNode(comment));
//   author.appendChild(nameElem);
//   author.appendChild(emailElem);
//   author.appendChild(commentElem);
//   node.appendChild(author);
//
//   return sheet;
// }


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
      alert("An error occurred while parsing the XSLT! This is a bug. Please report it using this description:\nThe QSOS 2.0 to Freemind XSLT doesn't work.\nPlease include your evaluations in the report.");
      alert("loadFile: " + strbundle.getString("parsingError") + "\n\n" + error[0].textContent);
      return false;
    }

    var toTrans = myDoc.getSheet();

    var processor = new XSLTProcessor();
    processor.importStylesheet(xslt);
    element = processor.transformToDocument(toTrans);

//     var serializer = new XMLSerializer();
//     var xmlOutput = serializer.serializeToString(toTrans);
//     alert("Output:\n" + xmlOutput);

    getPrivilege();
//     alert("Choose the file to save the template.");
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
//     alert("Choose the evaluation you want to update.");
    var filename = pickAFile(".qsos", strbundle.getString("QSOSFile"));
    if (filename == "") {
      return false;
    }
    var xml = loadFile(filename);
    if (xml == null) {
      return false;
    }

    var xslt = parseXML(qsos_1_6_to_qsos_2_0);
    var error = xslt.getElementsByTagName("parsererror");
    if (error.length == 1) {
      alert("An error occurred while parsing the XSLT! This is a bug. Please report it using this description:\nThe QSOS 1.6 to QSOS 2.0 XSLT doesn't work.\nPlease include your evaluations in the report.");
      alert("loadFile: " + strbundle.getString("parsingError") + "\n\n" + error[0].textContent);
      return false;
    }

    // FIXME find a way to open the right XSLT from the extension
    /*xslt = loadXSLT("chrome://qsos-xuled/content/freemind_to_qsos.xsl");
     if (xslt == null) {
     return false;
     }*/

    myDoc = new Document();

    try {
    var processor = new XSLTProcessor();
    processor.importStylesheet(xslt);
    myDoc.setSheet(processor.transformToDocument(xml));
    } catch (e) {
      alert("updateFromOldQSOS: can't process sheet: " + e.message);
      closeFile();
      return false;
    }

//     try {
//     var serializer = new XMLSerializer();
//     var xmlOutput = serializer.serializeToString(myDoc.getSheet());
//     alert("Output:\n" + xmlOutput);
//     } catch (e) {
//       alert("updateFromOldQSOS: failed to serialize, check your evaluation : " + e.message);
//       closeFile();
//       return false;
//     }

    try {
      setupEditorForEval();
    } catch (e) {
      alert("updateFromOldQSOS: an error occured while setting up the editor: " + e.message);
      closeFile();
      return false;
    }
  } catch (e) {
    alert("updateFromOldQSOS: general error: " + e.message);
    closeFile();
    return false;
  }
}

// Commands used to produce "Javascript compliant" strings form raw xslt files:
// sed 's/"/\\"/g' <file.xslt> | sed 's/$/\\/g'

var qsos_1_6_to_qsos_2_0 = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\
<xsl:stylesheet xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" version=\"1.0\">\
<xsl:output method=\"xml\" indent=\"yes\" encoding=\"UTF-8\"/>\
\
<xsl:template match=\"document\">\
<xsl:element name=\"document\">\
<xsl:apply-templates select=\"header\"/>\
<xsl:apply-templates select=\"section\"/>\
</xsl:element>\
</xsl:template>\
\
<xsl:template match=\"header\">\
<xsl:element name=\"header\">\
<xsl:element name=\"metadata\">\
<xsl:apply-templates select=\"authors\"/>\
<xsl:apply-templates select=\"dates\"/>\
<xsl:apply-templates select=\"language\"/>\
<xsl:apply-templates select=\"qsosformat\"/>\
<xsl:apply-templates select=\"qsosspecificformat\"/>\
</xsl:element>\
<xsl:element name=\"cartouche\">\
<xsl:attribute name=\"version\">0.1</xsl:attribute>\
<Component>\
<xsl:apply-templates select=\"appname\"/>\
<xsl:apply-templates select=\"release\"/>\
<xsl:apply-templates select=\"url\"/>\
<status/>\
<releaseDate/>\
<xsl:apply-templates select=\"qsosappfamily\"/>\
<mainTech/>\
</Component>\
<license>\
<xsl:apply-templates select=\"licensedesc\"/>\
<version></version>\
<homepage></homepage>\
</license>\
<team/>\
<legal/>\
<misc/>\
</xsl:element>\
</xsl:element>\
</xsl:template>\
\
<xsl:template match=\"appname\">\
<xsl:element name=\"name\"><xsl:apply-templates select=\"@*|node()\"/></xsl:element>\
</xsl:template>\
\
<xsl:template match=\"release\">\
<xsl:element name=\"version\"><xsl:apply-templates select=\"@*|node()\"/></xsl:element>\
</xsl:template>\
\
<xsl:template match=\"url\">\
<xsl:element name=\"homepage\"><xsl:apply-templates select=\"@*|node()\"/></xsl:element>\
</xsl:template>\
\
<xsl:template match=\"qsosappfamily\">\
<xsl:element name=\"type\"><xsl:apply-templates select=\"@*|node()\"/></xsl:element>\
</xsl:template>\
\
<xsl:template match=\"licensedesc\">\
<xsl:element name=\"name\"><xsl:apply-templates select=\"@*|node()\"/></xsl:element>\
</xsl:template>\
\
<xsl:template match=\"authors\">\
<authors>\
<xsl:apply-templates select=\"author\"/>\
</authors>\
</xsl:template>\
\
<xsl:template match=\"author\">\
<author>\
<name><xsl:value-of select=\"name\"/></name>\
<email><xsl:value-of select=\"email\"/></email>\
</author>\
</xsl:template>\
\
<xsl:template match=\"dates\">\
<dates>\
<creation><xsl:value-of select=\"creation\"/></creation>\
<validation><xsl:value-of select=\"validation\"/></validation>\
<update/>\
</dates>\
</xsl:template>\
\
<xsl:template match=\"language\">\
<xsl:element name=\"language\"><xsl:apply-templates select=\"@*|node()\"/></xsl:element>\
</xsl:template>\
\
<xsl:template match=\"qsosformat\">\
<xsl:element name=\"qsosversion\">2.0</xsl:element>\
</xsl:template>\
\
<xsl:template match=\"qsosspecificformat\">\
<xsl:element name=\"templateversion\"><xsl:apply-templates select=\"@*|node()\"/></xsl:element>\
</xsl:template>\
\
<xsl:template match=\"@*|node()\">\
<xsl:copy><xsl:apply-templates select=\"@*|node()\"/></xsl:copy>\
</xsl:template>\
\
</xsl:stylesheet>";

var freemind_to_qsos_2_0 = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\
<xsl:stylesheet xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" version=\"1.0\">\
<xsl:output method=\"xml\" indent=\"yes\" encoding=\"UTF-8\"/>\
\
<xsl:template match=\"map\">\
<xsl:element name=\"document\">\
<xsl:apply-templates select=\"node\"/>\
</xsl:element>\
</xsl:template>\
\
<xsl:template match=\"node\">\
<xsl:choose>\
<xsl:when test=\"parent::map\">\
<qsosMetadata>\
<template>\
<xsl:apply-templates select=\"//node[@ID='authors']\"/>\
<reviewer>\
<name><xsl:value-of select=\"//node[@ID='reviewer_name_entry']/@TEXT\"/></name>\
<email><xsl:value-of select=\"//node[@ID='reviewer_email_entry']/@TEXT\"/></email>\
<reviewDate><xsl:value-of select=\"//node[@ID='review_date_entry']/@TEXT\"/></reviewDate>\
<comment><xsl:value-of select=\"//node[@ID='reviewer_comment_entry']/@TEXT\"/></comment>\
</reviewer>\
<dates>\
<creation><xsl:value-of select=\"//node[@ID='creation_entry']/@TEXT\"/></creation>\
<update><xsl:value-of select=\"//node[@ID='update_entry']/@TEXT\"/></update>\
<validation></validation>\
</dates>\
<version><xsl:value-of select=\"//node[@ID='version_entry']/@TEXT\"/></version>\
<type><xsl:value-of select=\"//node[@ID='type']/@TEXT\"/></type>\
</template>\
<evaluation>\
<authors>\
<author>\
<name></name>\
<email></email>\
<comment></comment>\
</author>\
</authors>\
<reviewer>\
<name></name>\
<email></email>\
<reviewDate></reviewDate>\
<comment></comment>\
</reviewer>\
<dates>\
<creation></creation>\
<update></update>\
<validation></validation>\
</dates>\
</evaluation>\
<language><xsl:value-of select=\"//node[@ID='language_entry']/@TEXT\"/></language>\
<qsosVersion>2.0</qsosVersion>\
</qsosMetadata>\
<openSourceCartouche>\
<metadata>\
<cartoucheVersion>0.2 Beta</cartoucheVersion>\
<author>\
<name></name>\
<email></email>\
<comment></comment>\
</author>\
<reviewer>\
<name></name>\
<email></email>\
<reviewDate></reviewDate>\
<comment></comment>\
</reviewer>\
<dates>\
<creation></creation>\
<update></update>\
<validation></validation>\
</dates>\
</metadata>\
<component>\
<name></name>\
<version></version>\
<status></status>\
<releaseDate></releaseDate>\
<homepage></homepage>\
<description></description>\
<archetype></archetype>\
<vendor></vendor>\
<tags></tags>\
<mainTech></mainTech>\
<checksum></checksum>\
</component>\
<license>\
<name></name>\
<version></version>\
<homepage></homepage>\
</license>\
<team>\
<number></number>\
<developers>\
<developer>\
<name></name>\
<email></email>\
<company></company>\
</developer>\
</developers>\
<contributors>\
<contributor>\
<name></name>\
<email></email>\
<company></company>\
</contributor>\
</contributors>\
</team>\
<legal>\
<copyright></copyright>\
<patents>\
<patent>\
<ipcNumber></ipcNumber>\
<name></name>\
<publicationDate></publicationDate>\
<description></description>\
</patent>\
</patents>\
<cypher>\
<name></name>\
<exportRestrictions></exportRestrictions>\
</cypher>\
</legal>\
<misc>\
<comment></comment>\
<fileNumber>1</fileNumber>\
<data>\
<volume></volume>\
<unit></unit>\
</data>\
<dependencies></dependencies>\
</misc>\
</openSourceCartouche>\
<xsl:apply-templates select=\"node\"/>\
</xsl:when>\
<xsl:when test=\"./@STYLE='bubble'\">\
<desc><xsl:value-of select=\"@TEXT\"/></desc>\
</xsl:when>\
<xsl:when test=\"@ID = 'metadata'\"></xsl:when>\
<xsl:when test=\"@ID = 'authors'\">\
<authors>\
<xsl:apply-templates select=\"node\"/>\
</authors>\
</xsl:when>\
<xsl:when test=\"@TEXT = 'author' and ancestor::node/@ID = 'authors'\">\
<author>\
<xsl:apply-templates select=\"node\"/>\
</author>\
</xsl:when>\
<xsl:when test=\"@TEXT = 'name' and ancestor::node/ancestor::node/@ID = 'authors'\">\
<name><xsl:value-of select=\"child::node/@TEXT\"/></name>\
</xsl:when>\
<xsl:when test=\"@TEXT = 'email' and ancestor::node/ancestor::node/@ID = 'authors'\">\
<email><xsl:value-of select=\"child::node/@TEXT\"/></email>\
</xsl:when>\
<xsl:when test=\"@TEXT = 'comment' and ancestor::node/ancestor::node/@ID = 'authors'\">\
<comment><xsl:value-of select=\"child::node/@TEXT\"/></comment>\
</xsl:when>\
<xsl:when test=\"child::icon\">\
<xsl:if test=\"icon/@BUILTIN = 'full-0'\"><desc0><xsl:value-of select=\"@TEXT\"/></desc0></xsl:if>\
<xsl:if test=\"icon/@BUILTIN = 'full-1'\"><desc1><xsl:value-of select=\"@TEXT\"/></desc1></xsl:if>\
<xsl:if test=\"icon/@BUILTIN = 'full-2'\"><desc2><xsl:value-of select=\"@TEXT\"/></desc2></xsl:if>\
</xsl:when>\
<xsl:when test=\"count(ancestor::node()) = 3\">\
<section name=\"{@ID}\" title=\"{@TEXT}\">\
<xsl:apply-templates select=\"attribute\"/>\
<xsl:apply-templates select=\"node\"/>\
</section>\
</xsl:when>\
<xsl:otherwise>\
<element name=\"{@ID}\" title=\"{@TEXT}\">\
<xsl:apply-templates select=\"attribute\"/>\
<xsl:apply-templates select=\"node\"/>\
<xsl:if test=\"child::node/icon\">\
<comment></comment>\
<score></score>\
</xsl:if>\
</element>\
</xsl:otherwise>\
</xsl:choose>\
</xsl:template>\
\
<xsl:template match=\"attribute\">\
<xsl:element name=\"{@NAME}\">\
<xsl:value-of select=\"@VALUE\"/>\
</xsl:element>\
</xsl:template>\
\
</xsl:stylesheet>";

var qsos_2_0_to_freemind = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\
<xsl:stylesheet xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" version=\"1.0\">\
<xsl:output method=\"xml\" indent=\"yes\" encoding=\"UTF-8\"/>\
\
<xsl:template match=\"document\">\
<xsl:element name=\"map\">\
<xsl:attribute name=\"version\">0.9</xsl:attribute>\
<xsl:element name=\"node\">\
<xsl:attribute name=\"ID\"><xsl:value-of select=\"openSourceCartouche/Component/ComponentName\"/></xsl:attribute>\
<richcontent TYPE=\"NODE\"><html>\
<head></head>\
<body><p style=\"text-align: center\">\
<xsl:value-of select=\"openSourceCartouche/Component/ComponentName\"/><br/>\
<xsl:value-of select=\"openSourceCartouche/Component/ComponentVersion\"/>\
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
</xsl:stylesheet>";
