<?php
	$file = $_POST['file']
	$myTextFileHandler = @fopen($file,"r+");
	@ftruncate($myTextFileHandler, 0);
?>