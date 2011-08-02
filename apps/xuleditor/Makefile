#  Copyright (C) 2006-2011 Atos
#
#  Authors: Raphael Semeteys <raphael.semeteys@atos.net>
#           Timothée Ravier <travier@portaildulibre.fr>
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
# $Id: Makefile,v 1.14 2010/12/23 17:54:25 rsemeteys Exp $

APPPACK=application.ini Changes chrome.manifest LICENSE README xuleditor xuleditor.bat

EXTPACK=Changes chrome.manifest install.rdf LICENSE README

APPSRC=chrome/content/confirm.xul chrome/content/Document.js chrome/content/editor.js chrome/content/editor.xul chrome/content/load.js chrome/content/load.xul chrome/content/new.js chrome/content/new.xul chrome/content/logo-qsos.png chrome/content/logo32.png chrome/content/file.js chrome/content/opensourcecartouche.js chrome/content/criteria.js chrome/content/chart.js chrome/content/metadata.js chrome/content/compatibility.js chrome/content/confirmUpload.xul

EXTSRC=$(APPSRC) chrome/content/qsos-overlay.js chrome/content/qsos-overlay.xul chrome/skin/qsos-overlay.css

LOCALE=chrome/locale/en-US/confirm.dtd chrome/locale/en-US/editor.dtd chrome/locale/en-US/load.dtd chrome/locale/en-US/new.dtd chrome/locale/en-US/editor.properties chrome/locale/en-US/qsos-overlay.dtd chrome/locale/fr-FR/confirm.dtd chrome/locale/fr-FR/editor.dtd chrome/locale/fr-FR/load.dtd chrome/locale/fr-FR/new.dtd chrome/locale/fr-FR/editor.properties chrome/locale/fr-FR/qsos-overlay.dtd chrome/locale/en-US/confirmUpload.dtd chrome/locale/fr-FR/confirmUpload.dtd

SKIN=chrome/skin/classic.css chrome/skin/document-open.png chrome/skin/documentation.png chrome/skin/document-new.png chrome/skin/document-open.png chrome/skin/document-open-remote.png chrome/skin/document-save.png chrome/skin/document-save-as.png chrome/skin/document-save.png chrome/skin/document-close.png chrome/skin/list-add.png chrome/skin/list-remove.png chrome/skin/application-exit.png chrome/skin/user-properties.png chrome/skin/tools-wizard.png chrome/skin/quickopen-file.png chrome/skin/go-next-view-page.png chrome/skin/freemind.png chrome/skin/osc_small.png chrome/skin/osc_small_disabled.png chrome/skin/qsos_small.png chrome/skin/document-open_disabled.png chrome/skin/document-new_disabled.png chrome/skin/document-open_disabled.png chrome/skin/document-open-remote_disabled.png chrome/skin/document-save_disabled.png chrome/skin/document-save-as_disabled.png chrome/skin/document-save_disabled.png chrome/skin/document-close_disabled.png chrome/skin/application-exit_disabled.png chrome/skin/user-properties.png chrome/skin/tools-wizard_disabled.png chrome/skin/quickopen-file_disabled.png chrome/skin/go-next-view-page_disabled.png chrome/skin/freemind_disabled.png chrome/skin/osc_22.png chrome/skin/upload.png chrome/skin/upload_disabled.png

PREFS=defaults/preferences/qsos-xuled.js

PREFS_XULRUNNER=defaults/preferences/qsos-xuled-xulrunner.js

VERSION=2.0

ext:
	rm -f xuleditor-firefox-${VERSION}.xpi
	zip -r xuleditor $(EXTPACK) $(EXTSRC) $(LOCALE) $(SKIN) $(PREFS)
	mv xuleditor.zip xuleditor-firefox-${VERSION}.xpi

app:
	rm -f xuleditor-application-${VERSION}.zip
	zip -r xuleditor-application-${VERSION}.zip $(APPPACK) $(APPSRC) $(LOCALE) $(SKIN) $(PREFS_XULRUNNER)
