<?php
/**
 *  Copyright (C) 2007-2011 Atos
 *
 *  Author: Raphael Semeteys <raphael.semeteys@atos.net>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 *  O3S
 *  upload.php: uploads QSOS evaluation on local filesystem
 *
**/


  include("config.php");
  include("lang.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
<?php
  echo "    <link REL=StyleSheet HREF='skins/$skin/o3s.css' TYPE='text/css'/>\n";
?>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
  </head>
  <body>
    <div id="bandeau">
      <center>
        <a href="index.php">Start page</a> |
        <a href="upload.php">Upload an evaluation</a> |
        <a href="search.php">Search for an evaluation</a>
      </center>
    </div>
    <center>
<?php
  echo "      <img src='skins/$skin/o3s.png'/>\n";
?>
      <br/>
      <br/>
<?php
  if ((isset($_FILES['myFile'])) && ($_FILES['myFile']['tmp_name'] <> "")) {
    $file = $_FILES['myFile'];

    function displayUploadError($errorString) {
      return "<div style='color: red'>Upload error: " . $errorString . "<br/></div>\n";
    }

    function cleanString($str) {
      // Find out why escapeshellarg doesn't work here...
      return escapeshellcmd($str);
    }

    $filename = basename($file['name']);

    // Here we should get informations from the evaluation in order to validate it's upload (like template type, component name and version
    if(isset($lang) == FALSE) { $lang="";}
    if(isset($type) == FALSE) { $type="test";}

    $lang = cleanString($lang);
    $type = cleanString($type);
    $filename = cleanString($filename);

    $evaluationDir = "qsos/repositories/incoming/" . $lang . "/evaluations/" . $type . "/";

    exec("mkdir -p " . $evaluationDir, $output, $return_var);
    if ($return_var != 0) {
      die(displayUploadError("Can't create dir: " . $evaluationDir));
    }

    if (chdir($evaluationDir) == FALSE) {
      die(displayUploadError("Can't change directory to: " . $evaluationDir));
    }

    exec("git reset --hard HEAD", $output, $return_var);
    if ($return_var != 0) {
      die(displayUploadError("Can't reset git dir"));
    }

    if (move_uploaded_file($file['tmp_name'], $filename) == FALSE) {
      die(displayUploadError("Can't move file to: $evaluationDir"));
    }

    if (chmod($filename, 0660) == FALSE) {
      die(displayUploadError("Can't chmod the file: " . $filename));
    }

    exec("git config user.name \"O3S\"", $output, $return_var);
    if ($return_var != 0) {
      die(displayUploadError("Can't setup git user.name"));
    }

    exec("git config user.email \"qsos-general@nongnu.org\"", $output, $return_var);
    if ($return_var != 0) {
      die(displayUploadError("Can't setup git user.email"));
    }

    exec("git pull origin", $output, $return_var);
    if ($return_var != 0) {
      die(displayUploadError("Can't pull origin"));
    }

    exec("git add " . $filename, $output, $return_var);
    if ($return_var != 0) {
      echo $output;
      die(displayUploadError("Can't add file " . $filename));
    }

    exec("git commit -m \"Adds evaluation coming from O3S (" . $filename . ") in incoming folder\"", $output, $return_var);
    if ($return_var != 0) {
      die(displayUploadError("Can't commit changes: this evaluation may already be there!"));
    }

//     exec("git push origin", $output, $return_var);
//     if ($return_var != 0) {
//       die(displayUploadError("Can't push changes to origin"));
//     }

    echo "<div style='color: red'>File " . $filename . " successfully uploaded<br/></div>\n";

//     $destination = $sheet.$delim.basename($file['name']);
//     if (move_uploaded_file($file['tmp_name'], $destination)) {
//       chmod ($destination, 0660);
//       echo "<div style='color: red'>File ".basename($file['name'])." successfully uploaded<br/></div>";
//     } else {
//       echo "<div style='color: red'>Upload error: ".$file['error']."<br/></div>";
//     }
  }

?>
      <div style='font-weight: bold'>
        Upload a QSOS evaluation
        <br/>
        <br/>
      </div>
      <p>
        <form id='myForm' enctype='multipart/form-data' method='POST' action='upload.php'>
          <input type='file' id='myFile' name='myFile'/>
          <input type='submit' value='Upload'/>
        </form>
      </p>
      <input type='button' value='Update repository' onclick="window.location='metadata.php'">
    </center>
  </body>
</html>
