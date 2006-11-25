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
# QSOS XUL Editor Makefile
# $Id: Makefile,v 1.5 2006/11/25 11:35:53 rsemeteys Exp $

APPPACK=application.ini Changes chrome.manifest LICENSE README xuleditor xuleditor.bat
EXTPACK=Changes chrome.manifest install.rdf LICENSE README

APPSRC=chrome/content/about.xul chrome/content/authors.js chrome/content/authors.xul chrome/content/confirm.xul chrome/content/Document.js chrome/content/editor.js chrome/content/editor.xul chrome/content/logo-qsos.png chrome/content/logo32.png
EXTSRC=$(APPSRC) chrome/content/qsos-overlay.xul

LOCALE=chrome/locale/en-US/about.dtd chrome/locale/en-US/authors.dtd chrome/locale/en-US/confirm.dtd chrome/locale/en-US/editor.dtd chrome/locale/en-US/editor.properties chrome/locale/en-US/qsos-overlay.dtd chrome/locale/fr-FR/about.dtd chrome/locale/fr-FR/authors.dtd chrome/locale/fr-FR/confirm.dtd chrome/locale/fr-FR/editor.dtd chrome/locale/fr-FR/editor.properties chrome/locale/fr-FR/qsos-overlay.dtd

SKIN=chrome/skin/classic.css

PREFS=defaults/preferences/qsos-xuled.js

VERSION=0.4

ext:
	rm -f xuleditor-firefox-${VERSION}.xpi
	zip -r xuleditor $(EXTPACK) $(EXTSRC) $(LOCALE) $(SKIN)
	mv xuleditor.zip xuleditor-firefox-${VERSION}.xpi

app:
	rm -f xuleditor-application-${VERSION}.zip
	zip -r xuleditor-application-${VERSION}.zip $(APPPACK) $(APPSRC) $(LOCALE) $(SKIN) $(PREFS)