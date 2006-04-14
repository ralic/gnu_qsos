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
** QSOS XUL Editor
** Documents.js: document object abstracting the QSOS XML format
**
**
** TODO:
**	- Authors management
**	- Load remote QSOS XML file
*/

//Constructor
//name: filepath to the QSOS XML file
function Document(name) {
    var sheet;
    var file;
    filename = name;
    
    //Public methods declaration
    this.load = load;
    this.write = write;
    this.getkeytitle = getkeytitle;
    this.getauthors = getauthors;
    this.addauthor = addauthor;
    this.getappname = getappname;
    this.setappname = setappname;
    this.getlanguage = getlanguage;
    this.setlanguage = setlanguage;
    this.getrelease = getrelease;
    this.setrelease = setrelease;
    this.getlicenselist = getlicenselist;
    this.getlicenseid = getlicenseid;
    this.setlicenseid = setlicenseid;
    this.getlicensedesc = getlicensedesc;
    this.setlicensedesc = setlicensedesc;
    this.geturl = geturl;
    this.seturl = seturl;
    this.getdesc = getdesc;
    this.setdesc = setdesc;
    this.getdemourl = getdemourl;
    this.setdemourl = setdemourl;
    this.getqsosformat = getqsosformat;
    this.setqsosformat = setqsosformat;
    this.getqsosspecificformat = getqsosspecificformat;
    this.setqsosspecificformat = setqsosspecificformat;
    this.getqsosappfamily = getqsosappfamily;
    this.setqsosappfamily = setqsosappfamily;
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
    this.getfilename = getfilename;
    this.setfilename = setfilename;
    this.getcomplextree = getcomplextree;

    ////////////////////////////////////////////////////////////////////
    // QSOS XML file functions
    ////////////////////////////////////////////////////////////////////

    //Load and parse the local QSOS XML file
    //initializes local variable: sheet
    function load() {
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
        var xml = serializer.serializeToString(sheet);
        var result = outputStream.write( xml, xml.length );
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
        if (nb > 0) {
            return true;
        } 
        else {
            return false;
        }
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
	if (subcriteria.length > 0)
		return subcriteria;
	else
		return "null";
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
        if (node) 
                return node.textContent;
        else
            return "";
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
        var nodes = sheet.evaluate("//element[@name='"+element+"']/"+subelement, sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node)
                return node.textContent;
        else
            if (subelement == "score") 
                return -1;
            else
                return "";
    }

    //Set the value of a specific element's subelement in the QSOS XML file
    //element: element's name in the QSOS XML file
    //subelement: subelement's tagname in the QSOS XML file
    //value: subelement's new value
    function setgeneric(element, subelement, value) {
        var nodes = sheet.evaluate("//element[@name='"+element+"']/"+subelement, sheet, null, XPathResult.ANY_TYPE,null);
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
    
    function getkeytitle(element) {
    	var nodes = sheet.evaluate("//element[@name='"+element+"']", sheet, null, XPathResult.ANY_TYPE,null);
        var node = nodes.iterateNext();
        if (node)
            return node.getAttribute("title");
        else
            return "";
    }
    
    function getappname() {
        return getkey("appname");
    }
    
    function setappname(value) {
        return setkey("appname", value);
    }
    
    function getlanguage() {
        return getkey("language");
    }
    
    function setlanguage(value) {
        return setkey("language", value);
    }
    
    function getrelease() {
        return getkey("release");
    }
    
    function setrelease(value) {
        return setkey("release", value);
    }
    
    function geturl() {
        return getkey("url");
    }
    
    function seturl(value) {
        return setkey("url", value);
    }
    
    function getdesc() {
        return getkey("desc");
    }
    
    function setdesc(value) {
        return setkey("desc", value);
    }
    
    function getdemourl() {
        return getkey("demourl");
    }
    
    function setdemourl(value) {
        return setkey("demourl", value);
    }
    
    function getqsosformat() {
        return getkey("qsosformat");
    }
    
    function setqsosformat(value) {
        return setkey("qsosformat", value);
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
    // Authors management
    ////////////////////////////////////////////////////////////////////

    function getauthors() {
	//FIXME: return name AND email...
	var authors = new Array();
	var nodes = sheet.evaluate("//authors", sheet, null, XPathResult.ANY_TYPE,null);
	var node = nodes.iterateNext();
	
	var children = sheet.evaluate("//name", node, null, XPathResult.ANY_TYPE,null);
	var child = children.iterateNext();
	
	while (child) {
		name = child.textContent;
		authors.push(name);
		child = children.iterateNext();
	}
	return authors;
    }
    
    function addauthor(varname, varemail) {
	var nodes = sheet.evaluate("//authors", sheet, null, XPathResult.ANY_TYPE,null);
	var node = nodes.iterateNext();
	var author = sheet.createElement("author");
	var name = sheet.createElement("name");
	name.appendChild(document.createTextNode(varname));
	var email = sheet.createElement("email");
	email.appendChild(document.createTextNode(varemail));
	author.appendChild(name);
	author.appendChild(email);
	node.appendChild(author);
    }
    
    ////////////////////////////////////////////////////////////////////
    // Licenses management
    ////////////////////////////////////////////////////////////////////

    function getlicenselist() {
    	return new Array("Affero GPL", "AFPL (Aladdin)", "APSL (Apple)", "Copyback License", "DFSG approved", "Eclipse Public License", "EFL (Eiffel)", "Free for Eductional Use", "Free for Hum Use", "Free for non-commercial use", "Free but Restricted", "Freely Distribuable", "Freeware", "NPL (Netscape)", "NOKOS (Nokia)", "OSI Approved", "Proprietary", "Proprietary with trial", "Proprietary with source", "Public Domain", "Shareware", "SUN Binary Code License", "The Apache License", "The Apache License 2.0", "CeCILL License (INRIA)", "Artistic License", "LPPL (Latex)", "Open Content License", "Voxel Public License", "WTFPL", "Zope Public License", "GNU GPL", "GNU LGPL", "BSD", "GNU approved License", "GNU FDL");
    }
    
    function getlicenseid() {
    	return getkey("licenseid");
    }
    
    function setlicenseid(value) {
    	return setkey("licenseid", value);
    }
    
    function getlicensedesc() {
    	return getkey("licensedesc");
    }
    
    function setlicensedesc(value) {
    	return setkey("licensedesc", value);
    }
}