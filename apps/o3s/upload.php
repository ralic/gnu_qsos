<?php
/**
 *  Copyright (C) 2007-2011 Atos
 *
 *  Authors: Raphael Semeteys <raphael.semeteys@atos.net>
 *           Timothée Ravier  <travier@portaildulibre.fr>
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

/**
 * !!WARNING!!
 *
 * This script assumes that you are working in the o3s directory, in which you have cloned the qsos git repository, in a folder named qsos.
 *
 * You MUST choose a group / make sure permissions are OK before using O3S or it will break (especially the git part). If you can set a group to both apache and php stuff, use :
 *    setfacl -R -m default:group:$group_name:rwx $directory
 *    setfacl -R -m group:$group_name:rwx $directory
 *    setfacl -R -m default:u:$group_name:rwx $directory
 *    setfacl -R -m u:$group_name:rwx $directory
 *
 * You should not allow users to access the qsos directory remotely!
 * Once you have given a git account to O3S with a private key and uncommented the 'git push origin' lines, evaluations will be automatically added and pushed to the repository!
 *
 * This script uses A LOT of exec(), and could be exploited as we're not all the necessary checks. We should also use more php 'native' functions instead of exec calls.
**/


  include("config.php");
  include("lang.php");
  include("libs/QSOSDocument_2.0.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
<?php
  echo "    <link REL=StyleSheet HREF='skins/$skin/o3s.css' TYPE='text/css'/>\n";
?>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
    <script>
      function changeLang(lang) {
        window.location = 'upload.php?lang=' + lang;
      }
    </script>
  </head>
  <body>
    <div id="bandeau">
      <div id="language">
<?php
  foreach($supported_lang as $l) {
    $checked = $l;
    if (strcmp($l, $lang) == 0) {
      echo "        <input type='radio' onclick=\"changeLang('$l')\" checked=\"true\"/> $l\n";
    } else {
      echo "        <input type='radio' onclick=\"changeLang('$l')\"/> $l\n";
    }
  }
?>
      </div>
      <center>
<?php
  echo "        <a href=\"index.php?lang=" . $lang . "\">Start page</a> |\n";
  echo "        <a href=\"upload.php?lang=" . $lang . "\">Upload an evaluation</a> |\n";
  echo "        <a href=\"search.php?lang=" . $lang . "\">Search for an evaluation</a>\n";
?>
      </center>
    </div>
    <center>
<?php
  echo "      <img src='skins/$skin/o3s.png'/>\n";
?>
      <br/>
      <br/>
      <div style='font-weight: bold'>
        Upload a QSOS evaluation
        <br/>
        <br/>
      </div>
      <p>
        <form id='myForm' enctype='multipart/form-data' method='POST' action='upload.php'>
          <input type='file' id='myFile' name='myFile'/>
<?php
  echo "          <input type='text' id='lang' name='lang' value='" . $lang . "' hidden='true'/>\n";
?>
          <input type='submit' value='Upload'/>
        </form>
      </p>
<?php
  echo "      <input type='button' value='Update repository' onclick=\"window.location='metadata.php?lang=" . $lang . "'\">";
?>
      <br/>
<?php
  if ((isset($_FILES['myFile'])) && ($_FILES['myFile']['tmp_name'] <> "")) {
    $file = $_FILES['myFile'];

    function displayUploadError($errorString) {
      return "<div style='color: red' id='answer'>Upload error: " . $errorString . "<br/>Check if permissions are correct</div>\n</center>\n</body>\n</html>\n";
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

    // Creates the correct type directory in the correct language to hold the evaluation
    exec("mkdir -p " . $evaluationDir, $output, $return_var);
    if ($return_var != 0) {
      die(displayUploadError("Can't create dir: " . $evaluationDir));
    }

    if (chdir($evaluationDir) == FALSE) {
      die(displayUploadError("Can't change directory to: " . $evaluationDir));
    }

    // Resets git state (just in case, should do anything here)
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

    // Check the evaluation with the provided XSD
    // Code comming from : http://forums.devshed.com/xml-programming-19/validating-xml-against-xsd-with-php-430794.html
    // FIXME We should check it!!
    function libxml_display_error($error)
    {
        $return = "<br/>\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "<b>Warning $error->code</b>: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "<b>Error $error->code</b>: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "<b>Fatal Error $error->code</b>: ";
                break;
        }
        $return .= trim($error->message);
        if ($error->file) {
            $return .= " in <b>$error->file</b>";
        }
        $return .= " on line <b>$error->line</b>\n";

        return $return;
    }

    function libxml_display_errors()
    {
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            print libxml_display_error($error);
        }
        libxml_clear_errors();
    }

    // Enable user error handling
    libxml_use_internal_errors(true);

    $xml = new DOMDocument();
    $xml->load($filename);

    // FIXME Uncomment those lines to enable XSD validation
//     if ($xml->schemaValidate('../../../../../tools/xsd/QSOS_2.0.xsd') == FALSE) {
//         print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
//         libxml_display_errors();
//         unlink($filename);
//         exec("git reset --hard HEAD", $output, $return_var);
//         die(displayUploadError("This file isn't a valid QSOS evaluation. Check it with the XSD from git, found in git_root/tools/xsd/QSOS_2.0.xsd"));
//     }

    exec("git config user.name \"O3S\"", $output, $return_var);
    if ($return_var != 0) {
      die(displayUploadError("Can't setup git user.name"));
    }

    exec("git config user.email \"qsos-general@nongnu.org\"", $output, $return_var);
    if ($return_var != 0) {
      die(displayUploadError("Can't setup git user.email"));
    }

    // FIXME Uncomment those lines when you have given O3S an ssh key to pull/push to the savannah git repository
//     exec("git pull origin", $output, $return_var);
//     if ($return_var != 0) {
//       die(displayUploadError("Can't pull origin"));
//     }

    exec("git add " . $filename, $output, $return_var);
    if ($return_var != 0) {
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

    echo "<div style='color: red' id='answer'>File " . $filename . " successfully uploaded<br/></div>\n";

    // TODO Old code, to be removed
//     $destination = $sheet.$delim.basename($file['name']);
//     if (move_uploaded_file($file['tmp_name'], $destination)) {
//       chmod ($destination, 0660);
//       echo "<div style='color: red'>File ".basename($file['name'])." successfully uploaded<br/></div>";
//     } else {
//       echo "<div style='color: red'>Upload error: ".$file['error']."<br/></div>";
//     }
  }

?>
    </center>
  </body>
</html>
