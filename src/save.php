<?php
$wti = $_POST['whatToInsert'];
$file = '../data/log.txt';
$f = fopen ($file, 'a+');
fwrite($f, $wti . PHP_EOL);
fclose($f);
