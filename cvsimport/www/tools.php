<?php
include("func.php");
$lang = getlang();
head($lang);

if ($lang=="en") {
?>
        <h1>Tools</h1>
        <ul>
        </ul>
<?php
} else if ($lang=="fr") {
?>
        <h1>Outils</h1>
        <ul>
                <li>Editeur de grilles fonctionnelles (*.qtpl)<br/>
                <ul>
                        <li>QSOS XUL Template Editor (version alpha 0.1) [<a href="tpl-xuleditor.xpi">extention Firefox</a>]</li>
                </ul>
		</li>

                <li>Editeurs de fiches QSOS (*.qsos)<br/>
                <ul>
                        <li>QSOS XUL Editor (version bêta 0.2) [<a href="xuleditor.xpi">extention Firefox</a> | <a href="http://download.savannah.nongnu.org/releases/qsos/xuleditor-0.02.tar.gz">application xulrunner</a>]</li>
                </ul>
                <ul>
                        <li>QSOS Qt Editor (version bêta 0.2) [<a href="http://download.savannah.nongnu.org/releases/qsos/qsos-qteditor-0.02.tar.gz">application KDE</a>]</li>
                </ul>
                <ul>
                        <li>QSOS Java Editor (version alpha 0.1 bientôt disponible) [application Java SWT]</li>
                </ul>
		</li>
        </ul>
<?php
}
foot();
?>