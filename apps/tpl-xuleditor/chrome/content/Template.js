/*
**  Copyright (C) 2006 Atos Origin 
**
**  Author: Raphaël Semeteys <raphael.semeteys@atosorigin.com>
**
**  This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
**
**
** QSOS Template XUL Editor
** Template.js: template object abstracting the QSOS XML format
**
** TODO:
**	- Load remote QSOS XML file
*/

//Constructor
function Template() {
    var sheet;
    var file;
    var filename;
    
    this.transform = transform;

    //Public methods declaration
    this.create = create;
    this.load = load;
    this.write = write;
    this.getqsosspecificformat = getqsosspecificformat;
    this.setqsosspecificformat = setqsosspecificformat;
    this.getqsosappfamily = getqsosappfamily;
    this.setqsosappfamily = setqsosappfamily;
    this.getkeytitle = getkeytitle;
    this.setkeytitle = setkeytitle;
    this.getkeydesc = getkeydesc;
    this.setkeydesc = setkeydesc;
    this.getkeydesc0 = getkeydesc0;
    this.setkeydesc0 = setkeydesc0;
    this.getkeydesc1 = getkeydesc1;
    this.setkeydesc1 = setkeydesc1;
    this.getkeydesc2 = getkeydesc2;
    this.setkeydesc2 = setkeydesc2;

    this.dump = dump;
    this.getfilename = getfilename;
    this.setfilename = setfilename;
    this.isGenericSection = isGenericSection;
    this.hassubelements = hassubelements;
    this.getcomplextree = getcomplextree;

    this.fetchNode = fetchNode;
    this.deleteNode = deleteNode;
    this.createSection = createSection;
    this.createElementDesc = createElementDesc;
    this.createElementScore = createElementScore;
    this.insertNodeBefore = insertNodeBefore;
    this.insertSection = insertSection;
    this.insertSubelement = insertSubelement;
    this.getNodeType = getNodeType;
    this.setElementType = setElementType;
    this.setElementScore = setElementScore;
    this.setElementDesc = setElementDesc;

    ////////////////////////////////////////////////////////////////////
    // QSOS XML file functions
    ////////////////////////////////////////////////////////////////////

    //Load and parse the local QSOS XML file
    //initializes local variable: sheet
    //name: filepath to the QSOS XML file
    function create(name) {
	
	//Gets the generic template
	loadremote("chrome://qsos-tpl-xuled/content/generic.qsos");
	
	//Saves the new file
	filename = name;
	write();
    }
    
    //Load and parse the local QSOS XML file
    //initializes local variable: sheet
    //name: filepath to the QSOS XML file
    function load(name) {
	filename = name;
        try {
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        } catch (e) {
            alert("Permission to read file was denied.");
        }
        var file = Components.classes["@mozilla.org/file/local;1"]
            .createInstance(Components.interfaces.nsILocalFile);
        file.initWithPath( filename );
        if ( file.exists() == false ) {
            alert("File does not exist");
        }
        var is = Components.classes["@mozilla.org/network/file-input-stream;1"]
            .createInstance( Components.interfaces.nsIFileInputStream );
        is.init( file,0x01, 00004, null);
        var sis = Components.classes["@mozilla.org/scriptableinputstream;1"]
            .createInstance( Components.interfaces.nsIScriptableInputStream );
        sis.init( is );
        var output = sis.read( sis.available() );
        
        var domParser = new DOMParser();
        sheet = domParser.parseFromString(output, "text/xml");
    }
    
    //Serialize and write the local QSOS XML file
    function write() {
        try {
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        } catch (e) {
            alert("Permission to save file was denied.");
        }
        var file = Components.classes["@mozilla.org/file/local;1"]
            .createInstance(Components.interfaces.nsILocalFile);
        file.initWithPath( filename );
        if ( file.exists() == false ) {
            file.create( Components.interfaces.nsIFile.NORMAL_FILE_TYPE, 420 );
        }
        var outputStream = Components.classes["@mozilla.org/network/file-output-stream;1"]
            .createInstance( Components.interfaces.nsIFileOutputStream );

        outputStream.init( file, 0x04 | 0x08 | 0x20, 420, 0 );

        var serializer = new XMLSerializer();
	serializer.serializeToStream(sheet, outputStream, ""); 
        //var xml = serializer.serializeToString(sheet);
        //var result = outputStream.write( xml, xml.length );
        outputStream.close();
    }
    
    //Load and parse a remote QSOS XML file
    //ex: loadremote("http://localhost/qedit/xul/kolab.qsos")
    //initializes local variable: sheet
    function loadremote(url) {
        req = new XMLHttpRequest();
        req.open('GET', url, false); 
        req.overrideMimeType('text/xml');
        req.send(null);
        sheet = req.responseXML;
    }
    
    //Show the XML DOM structure in a dialogbox
    function dump() {
        var serializer = new XMLSerializer();
        var xml = serializer.serializeToString(sheet);
        alert(xml);
    }	

    ////////////////////////////////////////////////////////////////////
    // Tree functions
    ////////////////////////////////////////////////////////////////////

    //Returns true if an element has subelements, false if not
    //name: element's name attribute in the QSOS sheet
    function hassubelements(name) {
        var nb = sheet.evaluate("count(//*[@name='"+name+"']/element)", sheet, null, XPathResult.ANY_TYPE, null).numberValue;
        if (nb > 0) return true;
        else return false;
    }
    
    //Returns hierachical tree of objects representing the sheet's criteria
    //Array of "criterion" objects typed like this:
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

    //Recursive function for subelements
    //name: element's name attribute in the QSOS sheet
    //Returns an array of "criterion" objects typed like this:
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
	if (subcriteria.length > 0) return subcriteria;
	else return "null";
    }

    ////////////////////////////////////////////////////////////////////
    // Generic getters ans setters (private functions)
    ////////////////////////////////////////////////////////////////////

    //Get the value of a unique tag in the QSOS XML file
    //element: tagname
    //Returns tag's value or "" if tag doesn't exist
    function getkey(element) {
        var nodes = sheet.evaluate("//"+element, sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node) return node.textContent;
        else return "";
    }

    //Set the value of an unique tag in the QSOS XML file
    //element: tagname
    //value: tag's new value
    function setkey(element, value) {
        var nodes = sheet.evaluate("//"+element, sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
	if (node) node.textContent = value;
    }

    //Get the value of a specific element's subelement in the QSOS XML file
    //element: element's name in the QSOS XML file
    //subelement: subelement's tagname in the QSOS XML file
    //Returns subelement's value or "" if subelement doesn't exist (-1 if subelement is "score")
    function getgeneric(element, subelement) {
        var nodes = sheet.evaluate("//*[@name='"+element+"']/"+subelement, sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node) {
		return node.textContent;
	}
        else {
		if (subelement == "score") return -1;
		else return "";
	}
    }

    //Set the value of a specific element's subelement in the QSOS XML file
    //element: element's name in the QSOS XML file
    //subelement: subelement's tagname in the QSOS XML file
    //value: subelement's new value
    function setgeneric(element, subelement, value) {
        var nodes = sheet.evaluate("//*[@name='"+element+"']/"+subelement, sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node) node.textContent = value;
    }

    ////////////////////////////////////////////////////////////////////
    // Specific getters ans setters (public functions)
    ////////////////////////////////////////////////////////////////////

    function setfilename(name) {
    	filename = name;
    }
    
    function getfilename() {
    	return filename
    }
    
    //Retruns true if the node belongs to the "generic" section
    function isGenericSection(element) {
    	var node = sheet.evaluate("//*[@name='"+element+"']", sheet, null, XPathResult.ANY_TYPE,null).iterateNext();
	
	if (node.tagName == "section") {
		if (node.getAttribute("name") == "generic") return true;
		else return false;
	}
	
    	while (node.parentNode.tagName != "section") { 
		node = node.parentNode
 	}
	if (node.parentNode.getAttribute("name") == "generic") return true;
	else return false;
    }

    function getkeytitle(element) {
    	var nodes = sheet.evaluate("//*[@name='"+element+"']", sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node) return node.getAttribute("title");
        else return "";
    }
    
    function setkeytitle(element, value) {
	var nodes = sheet.evaluate("//*[@name='"+element+"']", sheet, null, XPathResult.ANY_TYPE,null);
	var node = nodes.iterateNext();
	if (node) node.setAttribute("title", value);
    }
    
    function getqsosspecificformat() {
        return getkey("qsosspecificformat");
    }
    
    function setqsosspecificformat(value) {
        return setkey("qsosspecificformat", value);
    }
    
    function getqsosappfamily() {
        return getkey("qsosappfamily");
    }
    
    function setqsosappfamily(value) {
        return setkey("qsosappfamily", value);
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

    ////////////////////////////////////////////////////////////////////
    // Fetch and delete functions
    ////////////////////////////////////////////////////////////////////

    //Returns an node given it's name or "false" if node doesn't exist
    function fetchNode(varname) {
	var nb = sheet.evaluate("count(//*[@name='"+varname+"'])", sheet, null, XPathResult.ANY_TYPE, null).numberValue;
	if (nb == 0) return "false";
	else return sheet.evaluate("//*[@name='"+varname+"']", sheet, null, XPathResult.ANY_TYPE,null).iterateNext();
    }

    //Deletes a node given it's name or "false" if node doesn't exist
    function deleteNode(varname) {
	var node = fetchNode(varname);
	if (node == "false") return "Error: no "+refname+" node found.";
	else node.parentNode.removeChild(node);
    }

    ////////////////////////////////////////////////////////////////////
    // Create functions
    ////////////////////////////////////////////////////////////////////

    //Creates and returns a new section:
    //<section name="name" title="title">
    //  <desc>vardesc</desc>
    //</section>
    function createSection(name, title, vardesc) {
	if (fetchNode(name) != "false") {
		return "Error: a "+name+" node already exists.";
	}
	else {
		var section = sheet.createElement("section");
		section.setAttribute("name", name);
		section.setAttribute("title", title);
	
		var desc = sheet.createElement("desc");
		desc.appendChild(sheet.createTextNode(vardesc));
		section.appendChild(desc);
	
		return section;
	}
    }

    //Creates and returns a new information element:
    //<element name="name" title="title">
    //  <desc>vardesc</desc>
    //  <comment></comment>
    //</element>
    function createElementDesc(name, title, vardesc) {
	if (fetchNode(name) != "false") {
		return "Error: a "+name+" node already exists.";
	}
	else {
		var element = sheet.createElement("element");
		element.setAttribute("name", name);
		element.setAttribute("title", title);
	
		var desc = sheet.createElement("desc");
		desc.appendChild(sheet.createTextNode(vardesc));
		element.appendChild(desc);
	
		var comment = sheet.createElement("comment");
		element.appendChild(comment);
	
		return element;
	}
    }

    //Creates and returns a new score element:
    //<element name="name" title="title">
    //  <desc0>vardesc0</desc0>
    //  <desc1>vardesc0</desc1>
    //  <desc2>vardesc0</desc2>
    //  <score></score>
    //  <comment></comment>
    //</element>
    function createElementScore(name, title, vardesc0, vardesc1, vardesc2) {
	if (fetchNode(name) != "false") {
		return "Error: a "+name+" node already exists.";
	}
	else {
		var element = sheet.createElement("element");
		element.setAttribute("name", name);
		element.setAttribute("title", title);
	
		var desc0 = sheet.createElement("desc0");
		desc0.appendChild(sheet.createTextNode(vardesc0));
		element.appendChild(desc0);
	
		var desc1 = sheet.createElement("desc1");
		desc1.appendChild(sheet.createTextNode(vardesc1));
		element.appendChild(desc1);
	
		var desc2 = sheet.createElement("desc2");
		desc2.appendChild(sheet.createTextNode(vardesc2));
		element.appendChild(desc2);
	
		var score = sheet.createElement("score");
		element.appendChild(score);
	
		var comment = sheet.createElement("comment");
		element.appendChild(comment);
	
		return element;
	}
    }

    ////////////////////////////////////////////////////////////////////
    // Insert functions
    ////////////////////////////////////////////////////////////////////

    //Inserts a element before another one
    //name: name of the element to be inserted
    //refname: name of the reference element before which insertion must be done
    function insertNodeBefore(name, refname) {
	var element = fetchNode(name);
	if (element == "false") {
		return "Error: no "+name+" node found.";
	}
	var refelement = fetchNode(refname);
	if (refelement == "false") {
		return "Error: no "+refname+" node found.";
	}
	else {
		var parentElement = refelement.parentNode;
		parentElement.insertBefore(element, refelement);
	}
	return "ok";
    }
    
    //Inserts a new section
    //section: section to be inserted
    function insertSection(element) {
    	sheet.documentElement.appendChild(element);
	return "ok";
    }

    //Inserts a subelement in another one
    //element: element to be inserted
    //refname: name of the parent node
    function insertSubelement(element, refname) {
	var refelement = fetchNode(refname);
	if (refelement == "false") return "Error: no "+refname+" node found.";
	else {
		refelement.appendChild(element);
		return "ok";
	}
    }

    ////////////////////////////////////////////////////////////////////
    // Type functions
    ////////////////////////////////////////////////////////////////////

    //Returns the type of a node
    //"section", "score" or "info" 
    function getNodeType(name) {
        var nodes = sheet.evaluate("//*[@name='"+name+"']/desc", sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node) {
		if (node.parentNode.nodeName == "section") return "section";
		else return "info";
	}
        else {
                return "score";
	}
    }

    //Sets/Changes the type of an element node
    //type: "score" or "info" 
    function setElementType(name, type) {
	switch (type) {
		case "score":
			setElementScore(name);
			break;
		case "info": 
			setElementDesc(name);
			break;
	}
    }

    //Changes the type of an element to Information (Desc)
    function setElementDesc(name) {
        var element = sheet.evaluate("//element[@name='"+name+"']", sheet, null, XPathResult.ANY_TYPE,null).iterateNext();
        if (element) {
		while (element.firstChild) {
			element.removeChild(element.firstChild);
		}
		//Creates info tag
		var desc = sheet.createElement("desc");
		var comment = sheet.createElement("comment");
		element.appendChild(desc);
		element.appendChild(comment);
        }
    }

    //Changes the type of an element to Score
    function setElementScore(name) {
        var element = sheet.evaluate("//element[@name='"+name+"']", sheet, null, XPathResult.ANY_TYPE,null).iterateNext();
        if (element) {
		while (element.firstChild) {
			element.removeChild(element.firstChild);
		}
		//Creates score tag
		var desc0 = sheet.createElement("desc0");
		var desc1 = sheet.createElement("desc1");
		var desc2 = sheet.createElement("desc2");
		var score = sheet.createElement("score");
		var comment = sheet.createElement("comment");
		element.appendChild(desc0);
		element.appendChild(desc1);
		element.appendChild(desc2);
		element.appendChild(score);
		element.appendChild(comment);
        }
    }

    ////////////////////////////////////////////////////////////////////
    // Unused function (for the time being...)
    ////////////////////////////////////////////////////////////////////

    //Applies a XSL transformation to the document and return a new document
    function transform(xslSheet) {
	var xsltProcessor = new XSLTProcessor();
	var myXMLHTTPRequest = new XMLHttpRequest();
	myXMLHTTPRequest.open("GET", xslSheet, false);
	myXMLHTTPRequest.overrideMimeType('text/xml');
	myXMLHTTPRequest.send(null);
	
	var xslRef = myXMLHTTPRequest.responseXML;
	xsltProcessor.importStylesheet(xslRef);
	return xsltProcessor.transformToDocument(sheet);
    }
}