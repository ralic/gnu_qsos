<?php

$file = $_FILES['myfile'];

$dir = "/tmp/".uniqid();
mkdir($dir, 0755);

move_uploaded_file($file['tmp_name'], $dir."/upload.qsos");
chmod ($dir."/upload.qsos", 0770);
echo "QSOS ".$file['type'];
?>