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
** QSOS XUL Template Editor
** ecommons.js: commons functions
**
*/

//Generates an unique ID based on criterion's title
function getUID(myDoc) {
	var title = document.getElementById("d-c-title").value;
	var UID = title.replace(/ /g,"");
	UID = UID.replace(/"/g,"");
	UID = UID.replace(/'/g,"");
	UID = UID.toLowerCase();
	
	if (UID.length > 50) {
		UID = UID.substr(0, 49);
	}
	
	var newUID = "";
	for(var i=0; i < UID.length; i++) {
		strMid = UID.charAt(i);
		switch (UID.charCodeAt(i)) {
			case 192:
			case 193:
			case 194:
			case 195:
			case 196:
			case 197:
				strMid = "A";
				break
			case 198:
				strMid = "AE";
				break
			case 199:
				strMid = "C";
				break
			case 200:
			case 201:
			case 202:
			case 203:
				strMid = "E";
				break
			case 204:
			case 205:
			case 206:
			case 207:
				strMid = "I";
				break
			case 208:
				strMid = "D";
				break
			case 209:
				strMid = "N";
				break
			case 210:
			case 211:
			case 212:
			case 213:
			case 214:
			case 216:
				strMid = "O";
				break
			case 215:
				strMid = "x";
				break
			case 217:
			case 218:
			case 219:
			case 220:
				strMid = "U";
				break
			case 221:
				strMid = "Y";
				break
			case 222,254:
				strMid = "p";
				break
			case 223:
				strMid = "B";
				break
			case 224:
			case 225:
			case 226:
			case 227:
			case 228:
			case 229:
				strMid = "a";
				break
			case 230:
				strMid = "ae";
				break
			case 231:
				strMid = "c";
				break
			case 232:
			case 233:
			case 234:
			case 235:
				strMid = "e";
				break
			case 236:
			case 237:
			case 238:
			case 239:
				strMid = "i";
				break
			case 240:
			case 242:
			case 243:
			case 244:
			case 245:
			case 246:
			case 248:
				strMid = "o";
				break
			case 241:
				strMid = "n";
				break
			case 249:
			case 250:
			case 251:
			case 252:
				strMid = "u";
				break
			case 253:
			case 255:
				strMid = "y";
				break
			default:
				break
		}
		newUID = newUID + strMid;
	}
	UID = newUID;
	
	var i = 0;
	while ((myDoc.fetchNode(UID) != "false") || (UID == "generic")) {
		i = i + 1;
		UID = UID + String(i);
	}
	
	return UID;
}