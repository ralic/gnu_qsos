<?php
/*
**  Copyright (C) 2007 Atos Origin 
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
** O3S - 
** upload.php: uploads QSOS evaluation on local filesystem
**
*/

include("config.php");
include("lang.php");

echo "<html>\n";
echo "<head>\n";
echo "<LINK REL=StyleSheet HREF='skins/$skin/o3s.css' TYPE='text/css'/>\n";
echo "</head>\n";

echo "<body>\n";
echo "<center>\n";
echo "<img src='skins/$skin/o3s.png'/>\n";
echo "<br/><br/>\n";

if (isset($_FILES['myFile']) && $_FILES['myFile']['tmp_name'] <> "") {
  $file = $_FILES['myFile'];
  $destination = $sheet.$delim.basename($file['name']);
  if (move_uploaded_file($file['tmp_name'], $destination)) {
    chmod ($destination, 0770);
    echo "<div style='color: red'>File ".basename($file['name'])." successfully uploaded<br/></div>";
  } else {
    echo "<div style='color: red'>Upload error: ".$file['error']."<br/></div>";
  }
}

echo "<div style='font-weight: bold'>Upload a QSOS evaluation<br/><br/></div>\n";

echo "<p><form id='myForm' enctype='multipart/form-data' method='POST' action='upload.php'>
  <input type='file' id='myFile' name='myFile'/>
  <input type='submit' value='Upload'/>
</form></p>";
echo "<input type='button' value='Update repository' onclick=\"window.location='metadata.php'\">";
echo "</div>\n";

echo "</center>\n";
echo "</body>\n";
echo "</html>\n";

?>