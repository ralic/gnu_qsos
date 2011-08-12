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
 * You MUST choose a group / make sure permissions are OK before using O3S or it will break (especially the git part). If you want to give a user/group access to the repository, use :
 *    setfacl -R -m default:group:$group_name:rwx $directory
 *    setfacl -R -m group:$group_name:rwx $directory
 *    setfacl -R -m default:u:$group_name:rwx $directory
 *    setfacl -R -m u:$group_name:rwx $directory
 * and remember to give ownership of all files to apache/cherokee/...
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
          If you want to be credited as an author for this commit in Git, please fill this form :
          <br/>
          Name: <input type='text' id='commitAuthorName' name='commitAuthorName'/>
          <br/>
          Email: <input type='text' id='commitAuthorEmail' name='commitAuthorEmail'/>
          <br/>
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
      return "<br/><div style='color: red' id='answer'>Upload error: " . $errorString . "<br/>Check if permissions are correct</div>\n</center>\n</body>\n</html>\n";
    }

    function cleanString($str) {
      // Find out why escapeshellarg doesn't work here...
      return escapeshellcmd($str);
    }

    $filename = basename($file['name']);
    $filename = cleanString($filename);

    // Temporary place to put evaluations, for XSD checks...
    $evaluationDir = "qsos/repositories/incoming/";

    // Check if this directory exists
    if(is_dir($evaluationDir) == FALSE) {
      if(mkdir($evaluationDir, 0770, TRUE) == FALSE) {
            die(displayUploadError("Can't create dir: " . $evaluationDir));
      }
    }

    if (chdir($evaluationDir) == FALSE) {
      die(displayUploadError("Can't change directory to: " . $evaluationDir));
    }

    // Resets git state (just in case, should not do anything particular here)
    exec("git reset --hard HEAD", $output, $return_var);
    if ($return_var != 0) {
      die(displayUploadError("Can't reset git dir"));
    }

    // FIXME Uncomment those lines when you have given O3S an ssh key to pull/push to the savannah git repository
//     exec("git pull origin", $output, $return_var);
//     if ($return_var != 0) {
//       die(displayUploadError("Can't pull origin"));
//     }

    // Move the uploaded file to the temporary directory
    if (move_uploaded_file($file['tmp_name'], $filename) == FALSE) {
      die(displayUploadError("Can't move file to: $evaluationDir"));
    }

    // Check the evaluation with the provided XSD
    // Code comming from : http://forums.devshed.com/xml-programming-19/validating-xml-against-xsd-with-php-430794.html
    // FIXME We should check it!!
    function libxml_display_error($error) {
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

    function libxml_display_errors() {
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

    // As the evaluation can now be considered valid, we try to retrieve some informations from it
    // TODO: get the language, type, component name from the evaluation
    if(isset($lang) == FALSE) { $lang="en";}
    if(isset($type) == FALSE) { $type="test";}

    $lang = cleanString($lang);
    $type = cleanString($type);

    $evaluationDir = $lang . "/evaluations/" . $type . "/";

    $evaluationUpdated = FALSE;
    if (file_exists($evaluationDir . $filename) == TRUE) {
      $evaluationUpdated = TRUE;
    }

    if (rename($filename, $evaluationDir . $filename) == FALSE) {
      unlink($filename);
      die(displayUploadError("Can't move file to: $evaluationDir"));
    }

    if (chdir($evaluationDir) == FALSE) {
      unlink($evaluationDir . $filename);
      die(displayUploadError("Can't change directory to: " . $evaluationDir));
    }

    if (chmod($filename, 0660) == FALSE) {
      unlink($filename);
      die(displayUploadError("Can't chmod the file: " . $filename));
    }

    // Makes sure O3S git setup is ok
    exec("git config user.name \"O3S\"", $output, $return_var);
    if ($return_var != 0) {
      unlink($filename);
      die(displayUploadError("Can't setup git user.name"));
    }

    exec("git config user.email \"qsos-general@nongnu.org\"", $output, $return_var);
    if ($return_var != 0) {
      unlink($filename);
      die(displayUploadError("Can't setup git user.email"));
    }

    exec("git add " . $filename, $output, $return_var);
    if ($return_var != 0) {
      unlink($filename);
      die(displayUploadError("Can't add file " . $filename));
    }

    if ($evaluationUpdated) {
      $commitMessage = "\"Updates evaluation coming from O3S (" . $filename . ") in incoming/" . $evaluationDir . "\"";
    } else {
      $commitMessage = "\"Adds evaluation coming from O3S (" . $filename . ") in incoming/" . $evaluationDir . "\"";
    }

    // Adds the checks in order to add the author to the commit (check this code !)
    $authorSet = FALSE;
    if (isset($_POST['commitAuthorName'])) {
      $authorSet = TRUE;
      $authorOption = cleanString($_POST['commitAuthorName']);
      if (isset($_POST['commitAuthorEmail'])) {
        $authorOption += " <" . cleanString($_POST['commitAuthorEmail']) . ">";
      }
    }

    if ($authorSet) {
      exec("git commit -m " . $commitMessage . " --author=" . $authorOption, $output, $return_var);
    } else {
      exec("git commit -m " . $commitMessage, $output, $return_var);
    }
    if ($return_var != 0) {
      unlink($filename);
      die(displayUploadError("Can't commit changes: this evaluation may already be there!"));
    }

    // FIXME Uncomment me once you're ready to push evaluations to the main repository
//     exec("git push origin", $output, $return_var);
//     if ($return_var != 0) {
//       die(displayUploadError("Can't push changes to origin"));
//     }

    echo "<div style='color: red' id='answer'>File " . $filename . " successfully uploaded<br/></div>\n";
  }

?>
    </center>
  </body>
</html>
