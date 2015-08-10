<?php
$myTextFileHandler = @fopen("../data/log.txt","r+");
	@ftruncate($myTextFileHandler, 0);
?>