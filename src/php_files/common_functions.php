<?php
	// random floating-point generator
	function frand ($modifier = 1, $min = 0, $max = 9, $decimals = 2) {
	 	$scale = pow (10, $decimals);

		return (mt_rand ($min * $scale, $max * $scale) / $scale) * $modifier;
	}

	// function to write to log important events on every cycle
	function addToLog ($content){
		$file = fopen ('../../data/log.txt', 'a+');

		fwrite ($file, $content);
		fclose ($file);
	}

