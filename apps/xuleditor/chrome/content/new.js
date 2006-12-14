var xmlDoc;
	 
function init() {
	try {
		netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	} catch (e) {
		alert("Permission to open file was denied.");
	}
        req = new XMLHttpRequest();
        req.open('GET', "http://www.qsos.org/phpviewer/loadremote.php?tpl=yes", false); 
	//req.overrideMimeType('text/xml');
        req.send(null);

	var domParser = new DOMParser();
	xmlDoc = domParser.parseFromString(req.responseText, "text/xml");
	
	var evalTree = document.getElementById("evalTree");

	var treechildren = getlist();
	evalTree.appendChild(treechildren);
}

function doOK() {
	var evalTree = document.getElementById("evalTree");
	var url = evalTree.view.getItemAtIndex(evalTree.currentIndex).firstChild.firstChild.getAttribute("id");

	if (url.substr(0, 7) != "http://") url = "";
	//Call window opener callback function
	window.arguments[1](url);
}

function getlist() {
	var treechildren = document.createElement("treechildren");
	treechildren.setAttribute("id", "myTreechildren");

	var items = xmlDoc.evaluate("/templates/item", xmlDoc, null, XPathResult.ANY_TYPE,null);
	var item = items.iterateNext();
	while (item) {
		var treeitem = document.createElement("treeitem");
		//treeitem.setAttribute("container", "true");
		//treeitem.setAttribute("open", "true");
		var treerow = document.createElement("treerow");
		var treecell = document.createElement("treecell");
		treecell.setAttribute("id", item.getAttribute("id"));
		treecell.setAttribute("label", item.getAttribute("label"));
		treerow.appendChild(treecell);
		treeitem.appendChild(treerow);
		treechildren.appendChild(treeitem);
		item = items.iterateNext();
	}
	return treechildren;
}
