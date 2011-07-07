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
 *  Documents.js: document object abstracting the QSOS XML format
 *
**/

//Constructor
//name: filepath to the QSOS XML file
function Document() {
    var sheet;
    var req;
    var filename;
    var url;

    //Public methods declaration
    this.load = load;
    this.loadremote = loadremote;
    this.write = write;
    this.writeremote = writeremote;
    this.getkeytitle = getkeytitle;
    this.getAuthors = getAuthors;
    this.getTeam = getTeam;
    this.addEvalAuthor = addEvalAuthor;
    this.delEvalAuthor = delEvalAuthor;
    this.addTeamMember = addTeamMember;
    this.delTeamMember = delTeamMember;

    this.get = get;
    this.set = set;

    this.getkeydesc = getkeydesc;
    this.setkeydesc = setkeydesc;
    this.getkeydesc0 = getkeydesc0;
    this.setkeydesc0 = setkeydesc0;
    this.getkeydesc1 = getkeydesc1;
    this.setkeydesc1 = setkeydesc1;
    this.getkeydesc2 = getkeydesc2;
    this.setkeydesc2 = setkeydesc2;
    this.getkeycomment = getkeycomment;
    this.setkeycomment = setkeycomment;
    this.getkeyscore = getkeyscore;
    this.setkeyscore = setkeyscore;

    this.dump = dump;
    this.hassubelements = hassubelements;
    this.getparent = getparent;
    this.getfilename = getfilename;
    this.setfilename = setfilename;
    this.getcomplextree = getcomplextree;
    this.getChartData = getChartData;
    this.getSubChartData = getSubChartData;
    this.getChartDataParent = getChartDataParent;

    ////////////////////////////////////////////////////////////////////
    // QSOS XML file functions
    ////////////////////////////////////////////////////////////////////

    // Get privilege to open windows
    function getPrivilege() {
      try {
        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        return true;
      } catch (e) {
        alert("newFile: Permission to open file denied: " + e.message);
        return false;
      }
    }


    // Load and parse the local QSOS XML file
    // initializes local variable: sheet
    function load() {
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

    // Load and parse a remote QSOS XML file
    // ex: loadremote("http://localhost/qedit/xul/kolab.qsos")
    // initializes local variable: sheet
    function loadremote(url) {
      myDoc.url = url;
      getPrivilege();
      req = new XMLHttpRequest();
      req.open('GET', url, false);
      req.overrideMimeType('text/xml');
      req.send(null);
      var domParser = new DOMParser();
      sheet = domParser.parseFromString(req.responseText, "text/xml");
    }

    // Serialize and write the local QSOS XML file
    function write() {
      getPrivilege();

      var file = Components.classes["@mozilla.org/file/local;1"]
                  .createInstance(Components.interfaces.nsILocalFile);

      file.initWithPath(myDoc.filename);
      if (file.exists() == false) {
          file.create(Components.interfaces.nsIFile.NORMAL_FILE_TYPE, 420);
      }

      var outputStream = Components.classes["@mozilla.org/network/file-output-stream;1"]
                         .createInstance(Components.interfaces.nsIFileOutputStream);

      outputStream.init(file, 0x04 | 0x08 | 0x20, 420, 0);

      var xml = serialize(sheet.documentElement, 0);

      var converter = Components.classes["@mozilla.org/intl/scriptableunicodeconverter"]
                      .createInstance(Components.interfaces.nsIScriptableUnicodeConverter);
      converter.charset = "UTF-8";
      xml = converter.ConvertFromUnicode(xml);

      outputStream.write(xml, xml.length);

      outputStream.close();
    }

    //Serialize and upload the QSOS XML file to a remote server
    function writeremote(url) {
       getPrivilege();

      var xml = serialize(sheet.documentElement, 0);

      var stream = Components.classes["@mozilla.org/io/string-input-stream;1"].createInstance(Components.interfaces.nsIStringInputStream);
      stream.setData(xml, xml.length);

      var bstream =  Components.classes["@mozilla.org/network/buffered-input-stream;1"].getService();
      bstream.QueryInterface(Components.interfaces.nsIBufferedInputStream);
      bstream.init(stream, 1000);
      bstream.QueryInterface(Components.interfaces.nsIInputStream);
      var binary = Components.classes["@mozilla.org/binaryinputstream;1"].createInstance(Components.interfaces.nsIBinaryInputStream);
      binary.setInputStream(stream);

      req = false;
      req = new XMLHttpRequest();

      //Set the filename
      var tmpFilename = myDoc.get("component/mainTech") + "." + myDoc.get("component/name") + "." + myDoc.get("component/version");
      if (tmpFilename == "..") {
        if (myDoc.filename == null) {
          tmpFilename = "upload";
        } else {
          tmpFilename = myDoc.filename;
        }
      }

      //Prepare the MIME POST data
      var boundaryString = 'qsoswriteremote';
      var boundary = '--' + boundaryString;
      var requestbody = boundary + '\n'
                      + 'Content-Disposition: form-data; name="myfile"; filename="'
                      + tmpFilename + '"' + '\n'
                      + 'Content-Type: text/xml' + '\n'
                      + '\n'
                      + binary.readBytes(binary.available())
                      + '\n'
                      + boundary;

      //Do the AJAX request
      req.onreadystatechange = requestdone;
      req.open('POST', url, true);
      req.setRequestHeader("Content-type", "multipart/form-data; \
        boundary=\"" + boundaryString + "\"");
      req.setRequestHeader("Connection", "close");
      req.setRequestHeader("Content-length", requestbody.length);
      req.send(requestbody);
    }

    //Upload callback
    function requestdone() {
      if (req.readyState == 4) {
        if (req.status == 200) {
          result = req.responseText;
          alert(result);
        } else {
          alert('There was a problem with the upload.');
        }
      }
    }

    // Recursively serialize a XML node in a string
    // node: XML node to serialize
    // depth: depth of recursion (used fo indentation), 0 is used at the beginning
    // returns the string with identations and \n characters
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
      if (node.hasAttributes()) {
        var attributes = node.attributes;
        for (var i = 0; i < attributes.length; i++) {
          var attribute = attributes[i];
          line += " " + attribute.name + "=\"" + specialChars(attribute.value) + "\"";
        }
      }
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

    // Show the XML DOM structure in a dialogbox
    function dump() {
      var serializer = new XMLSerializer();
      var xml = serializer.serializeToString(sheet);
      alert(xml);
    }

    ////////////////////////////////////////////////////////////////////
    // Tree functions
    ////////////////////////////////////////////////////////////////////

    // Returns true if an element has subelements, false if not
    // name: element's name attribute in the QSOS sheet
    function hassubelements(name) {
        var nb = sheet.evaluate("count(//*[@name='"+name+"']/element)", sheet, null, XPathResult.ANY_TYPE, null).numberValue;
        if (nb > 0) {
                return true;
        }
        else {
                return false;
        }
    }

    function getparent(name) {
        var node = sheet.evaluate("//*[@name='"+name+"']", sheet, null, XPathResult.ANY_TYPE,null).iterateNext();
        var parent = node.parentNode;
        if (parent.nodeName == "section" || parent.nodeName == "element") {
          return parent.getAttribute("name");
        } else {
          return false;
        }
    }

    // Returns hierachical tree of objects representing the sheet's criteria
    // Array of "criterion" objects typed like this:
    //	criterion.name: section or element's name attribute in the QSOS sheet
    //	criterion.title: section or element's title attribute in the QSOS sheet
    //	criterion.children: array of "criterion" objects representing the element's subelements
    //		or "null" if element doesn't have any subelements
    function getcomplextree() {
      var criteria = new Array();
      var sections = sheet.evaluate("//section", sheet, null, XPathResult.ANY_TYPE,null);
      var section = sections.iterateNext();
      while (section) {
        var criterion = new Object();
        criterion.name = section.getAttribute("name");
        criterion.title =  section.getAttribute("title");
        criterion.children = getsubcriteria(criterion.name);
        criteria.push(criterion);
        section = sections.iterateNext();
      }
      return criteria;
    }

    // Recursive function for subelements
    // name: element's name attribute in the QSOS sheet
    // Returns an array of "criterion" objects typed like this:
    //	criterion.name: section or element's name attribute in the QSOS sheet
    //	criterion.title: section or element's title attribute in the QSOS sheet
    //	criterion.children: array of "criterion" objects representing the element's subelements
    //		or "null" if element doesn't have any subelements
    function getsubcriteria(name) {
      var subcriteria = new Array();
      var elements = sheet.evaluate("//*[@name='"+name+"']/element", sheet, null, XPathResult.ANY_TYPE,null);
      var element = elements.iterateNext();
      while (element) {
        var criterion = new Object();
        criterion.name = element.getAttribute("name");
        criterion.title =  element.getAttribute("title");
        criterion.children = getsubcriteria(criterion.name);
        subcriteria.push(criterion);
        element = elements.iterateNext();
      }
      if (subcriteria.length > 0)
        return subcriteria;
      else
        return "null";
    }

    ////////////////////////////////////////////////////////////////////
    // Generic getters ans setters (private functions)
    ////////////////////////////////////////////////////////////////////

    // Get the value of a unique tag in the QSOS XML file
    // element: tagname
    // Returns tag's value or "" if tag doesn't exist
    function getkey(element) {
        var nodes = sheet.evaluate("//"+element, sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node)
                return node.textContent;
        else
            return "";
    }

    // Set the value of an unique tag in the QSOS XML file
    // element: tagname
    // value: tag's new value
    function setkey(element, value) {
      alert(element + " " + value);
      var nodes = sheet.evaluate("//"+element, sheet, null, XPathResult.ANY_TYPE,null);
      var node = nodes.iterateNext();
      if (node) node.textContent = value;
    }

    // Get the value of a specific element's subelement in the QSOS XML file
    // element: element's name in the QSOS XML file
    // subelement: subelement's tagname in the QSOS XML file
    // Returns subelement's value or "" if subelement doesn't exist (-1 if subelement is "score")
    function getgeneric(element, subelement) {
        var nodes = sheet.evaluate("//*[@name='"+element+"']/"+subelement, sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node)
                return node.textContent;
        else
            if (subelement == "score")
                return -1;
            else
                return "";
    }

    // Set the value of a specific element's subelement in the QSOS XML file
    // element: element's name in the QSOS XML file
    // subelement: subelement's tagname in the QSOS XML file
    // value: subelement's new value
    function setgeneric(element, subelement, value) {
        var nodes = sheet.evaluate("//*[@name='"+element+"']/"+subelement, sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node) {
          node.textContent = value;
        } else {
          //if subelement doesn't exist, create it
          nodes = sheet.evaluate("//*[@name='"+element+"']", sheet, null, XPathResult.ANY_TYPE,null);
          node = nodes.iterateNext();
          var newsubelement = sheet.createElement(subelement);
          newsubelement.appendChild(document.createTextNode(value));
          node.appendChild(newsubelement);
        }
    }


    function get(element) {
      var nodes = sheet.evaluate("//"+element, sheet, null, XPathResult.ANY_TYPE, null);
      var node = nodes.iterateNext();
      if (node)
        return node.textContent;
      else
        alert("Element: " + element + " doesn't exist.");
        return "";
    }


    function set(element, value) {
      var nodes = sheet.evaluate("//"+element, sheet, null, XPathResult.ANY_TYPE, null);
      var node = nodes.iterateNext();
      if (node) {
        node.textContent = value;
      } else {
        alert("Element: " + element + " doesn't exist. You're probably using the wrong function.");
      }
    }


    ////////////////////////////////////////////////////////////////////
    // Specific getters ans setters (public functions)
    ////////////////////////////////////////////////////////////////////

    function setfilename(name) { myDoc.filename = name; }

    function getfilename() { return myDoc.filename; }

    function getkeytitle(element) {
      var nodes = sheet.evaluate("//*[@name='"+element+"']", sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node)
            return node.getAttribute("title");
        else
            return "";
    }


    ////////////////////////////////////////////////////////////////////
    // Authors, reviewer, contributors and developers management
    ////////////////////////////////////////////////////////////////////

    function getAuthors(type) {
      var authors = new Array();
      var nodes = sheet.evaluate("//" + type + "/authors/author", sheet, null, XPathResult.ANY_TYPE, null);
      var node = nodes.iterateNext();
      while (node != null) {
        var author = new Object();
        var names = node.getElementsByTagName("name");
        if (names.length > 0) {
          author.name = names[0].textContent;
        } else {
          author.name = "";
        }
        var emails = node.getElementsByTagName("email");
        if (emails.length > 0) {
          author.email = emails[0].textContent;
        } else {
          author.email = "";
        }
        var comments = node.getElementsByTagName("comment");
        if (comments.length > 0) {
          author.comment = comments[0].textContent;
        } else {
          author.comment = "";
        }
        authors.push(author);
        node = nodes.iterateNext();
      }
      return authors;
    }


    function getTeam(type) {
      var authors = new Array();
      var nodes = sheet.evaluate("//team/" + type + "s/" + type, sheet, null, XPathResult.ANY_TYPE, null);
      var node = nodes.iterateNext();
      while (node != null) {
        var author = new Object();
        var names = node.getElementsByTagName("name");
        if (names.length > 0) {
          author.name = names[0].textContent;
        } else {
          author.name = "";
        }
        var emails = node.getElementsByTagName("email");
        if (emails.length > 0) {
          author.email = emails[0].textContent;
        } else {
          author.email = "";
        }
        var companies = node.getElementsByTagName("compagny");
        if (companies.length > 0) {
          author.company = companies[0].textContent;
        } else {
          author.company = "";
        }
        authors.push(author);
        node = nodes.iterateNext();
      }
      return authors;
    }


    function addEvalAuthor(name, email, comment) {
      var nodes = sheet.evaluate("//evaluation/authors", sheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
      var node = nodes.iterateNext();
      var author = sheet.createElement("author");
      var nameElem = sheet.createElement("name");
      nameElem.appendChild(document.createTextNode(name));
      var emailElem = sheet.createElement("email");
      emailElem.appendChild(document.createTextNode(email));
      var commentElem = sheet.createElement("comment");
      commentElem.appendChild(document.createTextNode(comment));
      author.appendChild(nameElem);
      author.appendChild(emailElem);
      author.appendChild(commentElem);
      node.appendChild(author);
    }


    function delEvalAuthor (name, email) {
      var nodes = sheet.evaluate("//evaluation/authors/author", sheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
      var node = nodes.iterateNext();
      while (node != null) {
        var names = node.getElementsByTagName("name");
        var emails = node.getElementsByTagName("email");
        if ((names.length > 0) && (emails.length > 0)) {
          if ((names[0].textContent == name) && (emails[0].textContent == email)) {
            nodes = sheet.evaluate("//evaluation/authors", sheet, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
            nodes = nodes.iterateNext();
            nodes.removeChild(node);
            return;
          }
        }
        node = nodes.iterateNext()
      }
      alert("Warning: delEvalAuthor: the author you're trying to remove has not been found!");
    }


    function addTeamMember(type, name, email, company) {
      var nodes = sheet.evaluate("//team/" + type + "s", sheet, null, XPathResult.ANY_TYPE, null);
      var node = nodes.iterateNext();
      var author = sheet.createElement(type);
      var nameElem = sheet.createElement("name");
      nameElem.appendChild(document.createTextNode(name));
      var emailElem = sheet.createElement("email");
      emailElem.appendChild(document.createTextNode(email));
      var companyElem = sheet.createElement("company");
      companyElem.appendChild(document.createTextNode(company));
      author.appendChild(nameElem);
      author.appendChild(emailElem);
      author.appendChild(companyElem);
      node.appendChild(author);
    }


    function delTeamMember (type, name, email) {
      var nodes = sheet.evaluate("//team/" + type + "s/" + type, sheet, null, XPathResult.ANY_TYPE, null);
      var node = nodes.iterateNext();
      while (node != null) {
        var names = node.getElementsByTagName("name");
        var emails = node.getElementsByTagName("email");
        if ((names.length > 0) && (emails.length > 0)) {
          if ((names[0].textContent == name) && (emails[0].textContent == email)) {
            var nodes = sheet.evaluate("//team/" + type + "s", sheet, null, XPathResult.ANY_TYPE, null);
            nodes = nodes.iterateNext();
            nodes.removeChild(node);
            return;
          }
        }
        node = nodes.iterateNext()
      }
      alert("Warning: delTeamMember: the team member you're trying to remove has not been found!");
    }


    function getkeydesc(element) {
      return getgeneric(element, "desc")
    }

    function setkeydesc(element, value) {
      return setgeneric(element, "desc", value);
    }

    function getkeydesc0(element) {
      return getgeneric(element, "desc0")
    }

    function setkeydesc0(element, value) {
      return setgeneric(element, "desc0", value);
    }

    function getkeydesc1(element) {
      return getgeneric(element, "desc1")
    }

    function setkeydesc1(element, value) {
      return setgeneric(element, "desc1", value);
    }

    function getkeydesc2(element) {
      return getgeneric(element, "desc2")
    }

    function setkeydesc2(element, value) {
      return setgeneric(element, "desc2", value);
    }

    function getkeycomment(element) {
      return getgeneric(element, "comment")
    }

    function setkeycomment(element, value) {
      return setgeneric(element, "comment", value);
    }

    function getkeyscore(element) {
      return getgeneric(element, "score");
    }

    function setkeyscore(element, value) {
      return setgeneric(element, "score", value);
    }


    ////////////////////////////////////////////////////////////////////
    // Licenses management
    ////////////////////////////////////////////////////////////////////

//     function getlicenselist() {
//       return new Array("Affero GPL", "AFPL (Aladdin)", "APSL (Apple)", "Artistic License", "BSD", "CeCILL License (INRIA)", "Copyback License", "DFSG approved", "Eclipse Public License", "EFL (Eiffel)", "Free but Restricted", "Free for Eductional Use", "Free for Home Use", "Free for non-commercial use", "Freely Distribuable", "Freeware", "GNU FDL", "GNU GPL", "GNU approved License", "GNU LGPL", "LPPL (Latex)", "NOKOS (Nokia)", "NPL (Netscape)", "Open Content License", "OSI Approved", "Proprietary", "Proprietary with source", "Proprietary with trial", "Public Domain", "Shareware", "SUN Binary Code License", "The Apache License", "The Apache License 2.0", "Voxel Public License", "WTFPL", "Zope Public License");
//     }

//     function getlicenseid() { return getkey("licenseid"); }

//     function setlicenseid(value) { return setkey("licenseid", value); }

//     function getlicensedesc() { return getkey("licensedesc"); }

//     function setlicensedesc(value) { return setkey("licensedesc", value); }

    ////////////////////////////////////////////////////////////////////
    // Chart functions
    ////////////////////////////////////////////////////////////////////

    //Returns the name of a criterion's parent
    function getChartDataParent(name) {
      var node = sheet.evaluate("//*[@name='"+name+"']", sheet, null, XPathResult.ANY_TYPE,null).iterateNext();
      if (node) {
        return node.parentNode.getAttribute("name");
      }
      else {
        return null;
      }
    }

    //Returns the scored criteria of QSOS document
    //Returned object: array of chartData
    //		chartData.name: name of the subcriterion
    //		chartData.title: title of the subcriterion
    //		chartData.children: null or array of chartData representing the subcriterion's subcritria
    //		chartData.score: score of the subcriterion (mean value of subcriretia)
    function getChartData() {
      var chartData = new Array();
      var sections = sheet.evaluate("//section", sheet, null, XPathResult.ANY_TYPE,null);
      var section = sections.iterateNext();
      while (section) {
        var criterion = new Object();
        criterion.name = section.getAttribute("name");
        criterion.title =  section.getAttribute("title");
        criterion.children = getSubChartData(criterion.name);
        criterion.score = renderScore(criterion.children);
        chartData.push(criterion);
        section = sections.iterateNext();
      }
      return chartData;
    }

    //Recursive function returning the scored subcriteria of a criteria
    //Returned object: array of chartData
    //		chartData.name: name of the subcriterion
    //		chartData.title: title of the subcriterion
    //		chartData.children: null or array of chartData representing the subcriterion's subcritria
    //		chartData.score: score of the subcriterion
    function getSubChartData(name) {
      var chartData = new Array();
      var elements = sheet.evaluate("//*[@name='"+name+"']/element", sheet, null, XPathResult.ANY_TYPE,null);
      var element = elements.iterateNext();
      while (element) {
        var criterion = new Object();
        criterion.name = element.getAttribute("name");
        criterion.title =  element.getAttribute("title");

        if (hassubelements(criterion.name)) {
          criterion.children = getSubChartData(criterion.name);
          criterion.score = renderScore(criterion.children);
          chartData.push(criterion);
        }
        else {
          criterion.children = null;
          criterion.score = getkeyscore(criterion.name);
          if (criterion.score == "") criterion.score = null;
          if (criterion.score != -1) {
            chartData.push(criterion);
          }
        }
        element = elements.iterateNext();
      }
      return chartData;
    }

    //Renders the value of a criterion based on its subecriteria's values
    //chartData: object representing the criterion
    //		chartData.name: name of the subcriterion
    //		chartData.title: title of the subcriterion
    //		chartData.children: null or array of chartData representing the subcriterion's subcritria
    //		chartData.score: score of the subcriterion
    function renderScore(chartData) {
      var score = 0;
      var sum = 0;
      var totalWeight = 0
      var isRenderable = true;

      for (var i = 0; i < chartData.length; ++i) {
        totalWeight++;
        if (chartData[i].score == null) isRenderable = false;
        sum += Math.round(chartData[i].score * 100)/100;
      }

      if (isRenderable) {
        score = Math.round((sum/totalWeight)*100)/100;
      }
      else {
        score = null;
      }
      return score;
    }
}