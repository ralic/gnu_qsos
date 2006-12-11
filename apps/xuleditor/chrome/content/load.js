var xmlDoc;
	 
function init() {
	try {
		netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	} catch (e) {
		alert("Permission to open file was denied.");
	}
        req = new XMLHttpRequest();
        req.open('GET', "http://localhost:88/test/OOo/loadremote.php", false); 
	//req.overrideMimeType('text/xml');
        req.send(null);

	var domParser = new DOMParser();
	xmlDoc = domParser.parseFromString(req.responseText, "text/xml");

	var criteria = getcomplextree();
	
	var evalTree = document.getElementById("evalTree");

	var treechildren = buildtree(criteria);
	evalTree.appendChild(treechildren);
}

function doOK() {
	var evalTree = document.getElementById("evalTree");
	var url = evalTree.view.getItemAtIndex(evalTree.currentIndex).firstChild.firstChild.getAttribute("id");

	if (url.substr(0, 7) != "http://") url = "";
	//Call window opener callback function
	window.arguments[1](url);
}

function getcomplextree() {
	var criteria = new Array();
	var items = xmlDoc.evaluate("/children/item", xmlDoc, null, XPathResult.ANY_TYPE,null);
	var item = items.iterateNext();
	while (item) {
		var criterion = new Object();
		criterion.id = item.getAttribute("id");
		criterion.label =  item.getAttribute("label");
		criterion.children = getsubcriteria(criterion.id);
		criteria.push(criterion);
		item = items.iterateNext();
	}
	return criteria;
}

function getsubcriteria(id) {
	var subcriteria = new Array();
	var items = xmlDoc.evaluate("//*[@id='"+id+"']/children/item", xmlDoc, null, XPathResult.ANY_TYPE,null);
	var item = items.iterateNext();
	while (item) {
		var criterion = new Object();
		criterion.id = item.getAttribute("id");
		criterion.label =  item.getAttribute("label");
		criterion.children = getsubcriteria(criterion.id);
		subcriteria.push(criterion);
		item = items.iterateNext();
	}
	if (subcriteria.length > 0)
		return subcriteria;
	else
		return "null";
}

//XUL Tree recursive creation function
function buildtree(criteria) {
	var treechildren = document.createElement("treechildren");
	treechildren.setAttribute("id", "myTreechildren");
	for (var i=0; i < criteria.length; i++) {
		treeitem = newtreeitem(criteria[i]);
		treechildren.appendChild(treeitem);
	}
	return treechildren;
}

//XUL Tree recursive creation function
function newtreeitem(criterion) {
	var treeitem = document.createElement("treeitem");
	treeitem.setAttribute("container", "true");
	treeitem.setAttribute("open", "true");
	var treerow = document.createElement("treerow");
	var treecell = document.createElement("treecell");
	treecell.setAttribute("id", criterion.id);
	treecell.setAttribute("label", criterion.label);
	treerow.appendChild(treecell);
	treeitem.appendChild(treerow);
	if (criterion.children != "null")
	treeitem.appendChild(buildsubtree(criterion.children));
	return treeitem;
}

//XUL Tree recursive creation function
function buildsubtree(criteria) {
	var treechildren = document.createElement("treechildren");
	for (var i=0; i < criteria.length; i++) {
		treeitem = newtreeitem(criteria[i]);
		treechildren.appendChild(treeitem);
	}
	return treechildren;
}