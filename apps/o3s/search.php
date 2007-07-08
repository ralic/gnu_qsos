<?php
//Based on this script: http://programmabilities.com/php/?id=2

session_start();
session_unset();
session_destroy();
$_SESSION = array();

$searchstr = $_REQUEST['s'];

include("config.php");
include("fs.functions.php");
include("locales/$lang.php");

echo "<html>\n";
echo "<head>\n";
echo "<LINK REL=StyleSheet HREF='skins/$skin/o3s.css' TYPE='text/css'/>\n";
echo "</head>\n";

echo "<body>\n";
echo "<center>\n";
echo "<img src='skins/$skin/o3s.png'/>\n";
echo "<br/><br/>\n";

echo "<p><form action='$PHP_SELF' method='post'>\n";
echo "	<input type='text' name='s' value='$searchstr' size='20' maxlength='30'/>\n";
echo "	<input type='submit' value='".$msg['s1_button_search']."'/><br/><br/>\n";
echo "	<input type='button' value='".$msg['s1_button_back']."' onclick=\"location.href='index.php'\"/>\n";
echo "</form></p>\n";

echo "</center>\n";

if (! empty($searchstr)) {
	// empty() is used to check if we've any search string.
	// If we do, call grep and display the results.
	echo '<hr/>';
	// Call grep with case-insensitive search mode on all files
	$cmdstr = "grep -i -l $searchstr $sheet/*/*/*.qsos";
	$fp = popen($cmdstr, 'r'); // open the output of command as a pipe
	$myresult = array(); // to hold my search results
	while ($buffer = fgetss($fp, 4096)) {
			// grep returns in the format
			// filename: line
			// So, we use split() to split the data
			list($fname, $fline) = split(':', $buffer, 2);
			$fname = trim($fname);
			// we take only the first hit per file matching the locale filter
			if (isLocalizedName($fname, $locale) && ! defined($myresult[$fname])) {
				$myresult[$fname] = $fline;
			}
	}

	// we have results in a hash. lets walk through it and print it
	if (count($myresult)) {
		echo '<ul><br/>';
		while (list($fname, $fline) = each ($myresult)) {
			$name = basename($fname, ".qsos");
			echo "<li><a href='show.php?svg=yes&s=$searchstr&f[]=$fname'>$name</a></li>\n";
		}
		echo '</ul><br/>';
	} else { 
		// no hits
		echo $msg['s1_search_msg1']."<strong>$searchstr</strong>".$msg['s1_search_msg2']."<br/>\n";
	}
	pclose($fp);
}

echo "</body>\n";
echo "</html>\n";
?>