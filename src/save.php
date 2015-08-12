<?php
$wti = $_POST['whatToInsert'];
$file = $_POST['file'];
$action = $_POST['action'];
$f = fopen ($file, $action);
fwrite($f, $wti);
fclose($f);
