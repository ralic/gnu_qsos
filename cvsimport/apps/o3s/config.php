<?php
/*
**  Copyright (C) 2007-2009 Atos Origin 
**
**  Author: Raphael Semeteys <raphael.semeteys@atosorigin.com>
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
** O3S
** config.php: configuration file
**
*/
//Local and web paths to QSOS sheets and templates
$sheet = "sheets";
$sheet_web = "http://localhost:88/o3s/sheets";
$template = "template";
$template_web = "http://localhost:88/o3s/template";
$delim = "/";

//Path to jpgraph library (for PNG graphs)
$jpgraph_path = "libs/jpgraph-2.1.3/src/";

//Temp directory, with trailing slash
$temp = "/tmp/";

//Activate/Deactivate OpenDocument exports caching
$cache = "off";

//Skin to use (CSS are stored in skins/ subdirectory)
$skin = "default";

//Locale to use (locale files are stored in locales/ subdirectory)
$default_lang = "fr"; //Default locale
$supported_lang = array('fr', 'en'); //Supported locale

$db_host = "localhost";
$db_user = "qsos";
$db_pwd = "qsos";
$db_db = "qsos";
?>
