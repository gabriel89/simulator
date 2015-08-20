<?php
ini_set('display_errors', 1);

	$file = $_POST['file'];
	$myTextFileHandler = @fopen($file,"w+");
	@ftruncate($myTextFileHandler, 0);
	?>