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

function loadXSLT() {
  if (checkCloseFile() == false) {
    return;
  }
  getPrivilege();
  var nsIFilePicker = Components.interfaces.nsIFilePicker;
  var fp = Components.classes["@mozilla.org/filepicker;1"].createInstance(nsIFilePicker);
  fp.init(window, strbundle.getString("selectFile"), nsIFilePicker.modeOpen);
  fp.appendFilter(strbundle.getString("QSOSFile"), "*.qsos");
  var res = fp.show();

  if (res == nsIFilePicker.returnOK) {
    openLocalFile(fp.file.path);
  }


  getPrivilege();

  var file = Components.classes["@mozilla.org/file/local;1"]
  .createInstance(Components.interfaces.nsILocalFile);
  file.initWithPath(myDoc.filename);
  if (file.exists() == false) {
    alert("File does not exist");
    return false;
  }

  var is = Components.classes["@mozilla.org/network/file-input-stream;1"]
  .createInstance(Components.interfaces.nsIFileInputStream);
  is.init(file, 0x01, 00004, null);

  var sis = Components.classes["@mozilla.org/scriptableinputstream;1"]
  .createInstance(Components.interfaces.nsIScriptableInputStream);
  sis.init(is);

  var output = sis.read(sis.available());

  var converter = Components.classes["@mozilla.org/intl/scriptableunicodeconverter"]
  .createInstance(Components.interfaces.nsIScriptableUnicodeConverter);
  converter.charset = "UTF-8";
  output = converter.ConvertToUnicode(output);

  var domParser = new DOMParser();
  sheet = domParser.parseFromString(output, "text/xml");

  var error = sheet.getElementsByTagName("parsererror");
  if (error.length == 1) {
    error = error[0].textContent;
    alert(strbundle.getString("parsingError") + "\n\n" + error);
    return false;
  }

  return true;
}


// Open a dialog box to pick a file with the extension 'ext', and type 'type'
// Returns the complete file as a string
function openFileGeneric(ext, type) {
  getPrivilege();
  var nsIFilePicker = Components.interfaces.nsIFilePicker;
  var fp = Components.classes["@mozilla.org/filepicker;1"].createInstance(nsIFilePicker);
  fp.init(window, strbundle.getString("selectFile"), nsIFilePicker.modeOpen);
  fp.appendFilter(type, "*" + ext);
  var res = fp.show();

  if (res != nsIFilePicker.returnOK) {
    return "";
  }

  var filename = fp.file.path;
  var file = Components.classes["@mozilla.org/file/local;1"]
  .createInstance(Components.interfaces.nsILocalFile);
  file.initWithPath(filename);
  if (file.exists() == false) {
    alert("File does not exist");
    return "";
  }

  var is = Components.classes["@mozilla.org/network/file-input-stream;1"]
  .createInstance(Components.interfaces.nsIFileInputStream);
  is.init(file, 0x01, 00004, null);

  var sis = Components.classes["@mozilla.org/scriptableinputstream;1"]
  .createInstance(Components.interfaces.nsIScriptableInputStream);
  sis.init(is);

  var output = sis.read(sis.available());

  var converter = Components.classes["@mozilla.org/intl/scriptableunicodeconverter"]
  .createInstance(Components.interfaces.nsIScriptableUnicodeConverter);
  converter.charset = "UTF-8";
  output = converter.ConvertToUnicode(output);

  alert(output);

  return output;
}


function updateFromTemplate() {
  try{
//   alert("updateFromTemplate: début");
  if (myDoc == null) {
    alert("updateFromTemplate: You need to open an evaluation before try to update it!");
    return false;
  }

  // Open template file dialog to choose a template to take stuff from
  var template = openFileGeneric(".mm", strbundle.getString("FreeMindTemplate"));
  if (template == "") {
//     alert("updateFromTemplate: This template is empty or can't be opened");
    return false;
  }

  var domParser = new DOMParser();
  sheet = domParser.parseFromString(template, "text/xml");

  var error = sheet.getElementsByTagName("parsererror");
  if (error.length >= 1) {
    error = error[0].textContent;
    alert(strbundle.getString("parsingError") + "\n\n" + error);
    return false;
  }

  // Do the update
  alert("TODO");
//   alert(sheet);
//   alert(template);
//   tempate.dump();

//   alert("updateFromTemplate: fin");
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
  var file = Components.classes["@mozilla.org/file/local;1"]
  .createInstance(Components.interfaces.nsILocalFile);
  file.initWithPath(filename);
  if (file.exists() == false) {
    file.create(Components.interfaces.nsIFile.NORMAL_FILE_TYPE, 420);
  }

  var outputStream = Components.classes["@mozilla.org/network/file-output-stream;1"]
  .createInstance(Components.interfaces.nsIFileOutputStream);

  outputStream.init(file, 0x04 | 0x08 | 0x20, 420, 0);

  myDoc.get("openSourceCartouche").dump();
  var xml = serialize(myDoc.get("openSourceCartouche"), 0);

  var converter = Components.classes["@mozilla.org/intl/scriptableunicodeconverter"]
  .createInstance(Components.interfaces.nsIScriptableUnicodeConverter);
  converter.charset = "UTF-8";
  xml = converter.ConvertFromUnicode(xml);

  outputStream.write(xml, xml.length);

  outputStream.close();
  }catch(e){ alert(e.message); }

  return true;
}


function exportToFreeMind() {
  alert("TODO");
}


function serialize(node, depth) {
  var indent = "";
  var line = "";

  if (depth == 0) {
    line = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  }

  for (i=0; i < depth; i++) {
    indent += "   ";
  }

  // Opening <tag attribute="value" ...>
  line += indent + "<" + node.tagName;
//   if (node.hasAttributes()) {
//     var attributes = node.attributes;
//     for (var i = 0; i < attributes.length; i++) {
//       var attribute = attributes[i];
//       line += " " + attribute.name + "=\"" + specialChars(attribute.value) + "\"";
//     }
//   }
  line += ">";

  // Children tags (recursion)
  var test = false;
  var children = node.childNodes;
  for (i = 0; i < children.length; i++) {
    var child = children[i];
    if (child.tagName) {
      line += "\n" + serialize(child, depth+1);
      // closing </tag> should be indented and on a new line
      test = true;
    }
  }

  // Node value + closing </tag>
  if (test) {
    line += "\n" + indent + "</" + node.tagName + ">";
  } else {
    // childNode is the value of the XML node (<tag>value</tag>)
    if (children[0]) {
      // Convert XML special chars
      line += specialChars(children[0].nodeValue);
    }
    line += "</" + node.tagName + ">";
  }

  return line;
}

// Deals with XML special chars (<,>,'," and &)
function specialChars(string) {
  string = string.replace(/&/g, '&amp;');
  string = string.replace(/</g, '&lt;');
  string = string.replace(/>/g, '&gt;');
  string = string.replace(/"/g, '&quot;');
  string = string.replace(/'/g, '&apos;');

  return string;
}
