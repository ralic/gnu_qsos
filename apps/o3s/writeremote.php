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
** O3S - Backend for remote clients
** writeremote.php: stores QSOS evaluations uploaded from remote clients
**
*/

$file = $_FILES['myfile'];
$destination = "incoming/".$file['name']. "." . uniqid().".qsos";

move_uploaded_file($file['tmp_name'], $destination);
chmod ($destination, 0770);
echo "File successfully uploaded";
?>