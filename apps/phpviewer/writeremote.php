<?php
$file = $_FILES['myfile'];
$destination = "incoming/".$file['name']. "." . uniqid().".qsos";

move_uploaded_file($file['tmp_name'], $destination);
chmod ($destination, 0770);
echo "File successfully uploaded";
?>