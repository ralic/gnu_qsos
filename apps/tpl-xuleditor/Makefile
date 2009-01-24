#  Copyright (C) 2006 Atos Origin 
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
# QSOS XUL Template Editor Makefile
# $Id: Makefile,v 1.2 2009/01/24 18:18:34 rsemeteys Exp $

APPPACK=application.ini Changes chrome.manifest LICENSE README tpl-xuleditor tpl-xuleditor.bat
EXTPACK=Changes chrome.manifest install.rdf LICENSE README

APPSRC=chrome/content/about.xul chrome/content/confirm.xul  chrome/content/editor.js chrome/content/editor.xul chrome/content/global.xml chrome/content/logo-qsos.png chrome/content/logo32.png chrome/content/newdesc.xul chrome/content/newscore.xul chrome/content/newsection.xul chrome/content/Template.js chrome/content/commons.js
EXTSRC=$(APPSRC) chrome/content/qsos-tpl-overlay.xul

LOCALE=chrome/locale/en-US/about.dtd chrome/locale/en-US/confirm.dtd chrome/locale/en-US/editor.dtd chrome/locale/en-US/editor.properties chrome/locale/en-US/newdesc.dtd chrome/locale/en-US/new.properties chrome/locale/en-US/newscore.dtd chrome/locale/en-US/newsection.dtd chrome/locale/en-US/qsos-tpl-overlay.dtd chrome/locale/fr-FR/about.dtd chrome/locale/fr-FR/confirm.dtd chrome/locale/fr-FR/editor.dtd chrome/locale/fr-FR/editor.properties chrome/locale/fr-FR/newdesc.dtd chrome/locale/fr-FR/new.properties chrome/locale/fr-FR/newscore.dtd chrome/locale/fr-FR/newsection.dtd chrome/locale/fr-FR/qsos-tpl-overlay.dtd

SKIN=chrome/skin/classic.css

PREFS=defaults/preferences/qsos-tpl-xuled.js

VERSION=0.3

ext:
	rm -f tpl-xuleditor-firefox-${VERSION}.xpi
	zip -r tpl-xuleditor $(EXTPACK) $(EXTSRC) $(LOCALE) $(SKIN)
	mv tpl-xuleditor.zip tpl-xuleditor-firefox-${VERSION}.xpi

app:
	rm -f tpl-xuleditor-application-${VERSION}.zip
	zip -r tpl-xuleditor-application-${VERSION}.zip $(APPPACK) $(APPSRC) $(LOCALE) $(SKIN) $(PREFS)