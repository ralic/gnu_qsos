
//Listener to trap URI modification and .qsos file browsint to redirect to XUL Editor

function registerMyListener() {
	window.getBrowser().addProgressListener(myListener, Components.interfaces.nsIWebProgressListener.STATE_START);
}

function unregisterMyListener() {
	window.getBrowser().removeProgressListener(myListener);
}

window.addEventListener("load", registerMyListener, false);
window.addEventListener("unload", unregisterMyListener, false);

var myListener = {
	QueryInterface:function(a){},
	onStateChange:function(a,b,c,d){},
	onLocationChange:function(aProgress,aRequest,aURI) {
		var url = aURI.spec;
		if (url.substr(-5) == ".qsos") window.openDialog('chrome://qsos-xuled/content/editor.xul','test', '_blank', 'chrome,dialog=no', url);
	},
	onProgressChange:function(a,b,c,d,e,f){},
	onStatusChange:function(a,b,c,d){},
	onSecurityChange:function(a,b,c){}
}