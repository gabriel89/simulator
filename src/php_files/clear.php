<?php
	ini_set('display_errors', 1);

	$file = $_POST['file'];
	$handler = @fopen($file,"w+");
	@ftruncate($handler, 0);