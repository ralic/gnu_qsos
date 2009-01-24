#  Copyright (C) 2006, 2007 Atos Origin 
#
#  This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
#  the Free Software Foundation; either version 2 of the License, or
#  (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
#
#
# QSOS XUL Editor Makefile
# $Id: Makefile,v 1.12 2009/01/24 18:30:21 rsemeteys Exp $

APPPACK=application.ini Changes chrome.manifest LICENSE README xuleditor xuleditor.bat

EXTPACK=Changes chrome.manifest install.rdf LICENSE README

APPSRC=chrome/content/about.xul chrome/content/authors.js chrome/content/authors.xul chrome/content/confirm.xul chrome/content/Document.js chrome/content/editor.js chrome/content/editor.xul chrome/content/load.js chrome/content/load.xul chrome/content/new.js chrome/content/new.xul chrome/content/logo-qsos.png chrome/content/logo32.png

EXTSRC=$(APPSRC) chrome/content/qsos-overlay.js chrome/content/qsos-overlay.xul

LOCALE=chrome/locale/en-US/about.dtd chrome/locale/en-US/authors.dtd chrome/locale/en-US/confirm.dtd chrome/locale/en-US/editor.dtd chrome/locale/en-US/load.dtd chrome/locale/en-US/new.dtd chrome/locale/en-US/editor.properties chrome/locale/en-US/qsos-overlay.dtd chrome/locale/fr-FR/about.dtd chrome/locale/fr-FR/authors.dtd chrome/locale/fr-FR/confirm.dtd chrome/locale/fr-FR/editor.dtd chrome/locale/fr-FR/load.dtd chrome/locale/fr-FR/new.dtd chrome/locale/fr-FR/editor.properties chrome/locale/fr-FR/qsos-overlay.dtd

SKIN=chrome/skin/classic.css chrome/skin/header.png chrome/skin/criteria.png chrome/skin/graph.png chrome/skin/chat.png

JSJAC=chrome/content/jsjac/API chrome/content/jsjac/AUTHORS chrome/content/jsjac/ChangeLog chrome/content/jsjac/COPYING chrome/content/jsjac/crypt.js chrome/content/jsjac/JSJaCConnection.js chrome/content/jsjac/JSJaCHttpBindingConnection.js chrome/content/jsjac/JSJaCHttpPollingConnection.js chrome/content/jsjac/JSJaC.js chrome/content/jsjac/JSJaCPacket.js chrome/content/jsjac/json.js chrome/content/jsjac/qm_cookie.js chrome/content/jsjac/README chrome/content/jsjac/semantic.cache chrome/content/jsjac/xmlextras.js

PREFS=defaults/preferences/qsos-xuled.js

PREFS_XULRUNNER=defaults/preferences/qsos-xuled-xulrunner.js

VERSION=0.8

ext:
	rm -f xuleditor-firefox-${VERSION}.xpi
	zip -r xuleditor $(EXTPACK) $(EXTSRC) $(LOCALE) $(SKIN) $(PREFS) $(JSJAC)
	mv xuleditor.zip xuleditor-firefox-${VERSION}.xpi

app:
	rm -f xuleditor-application-${VERSION}.zip
	zip -r xuleditor-application-${VERSION}.zip $(APPPACK) $(APPSRC) $(LOCALE) $(SKIN) $(PREFS_XULRUNNER) $(JSJAC)