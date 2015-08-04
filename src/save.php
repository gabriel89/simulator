<?php
$wti = $_POST['whatToInsert'];
$file = '../data/log.c';
$f = fopen ($file, 'a+');
fwrite($f, $wti . PHP_EOL);
fclose($f);
