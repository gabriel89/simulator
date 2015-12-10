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

	function fetch_nodes_toArray($con){
		$nodes = execute_sql_and_return ($_SERVER['PHP_SELF'], $con, "SELECT * FROM nodes");
		$nodes_array 	= [];

		foreach ($nodes as $nd){
			$nodes_array = array_merge ($nodes_array, [$nd]);
		}

		return $nodes_array;
	}

	function fetch_products_toArray($con){
		$products = execute_sql_and_return ($_SERVER['PHP_SELF'], $con, "SELECT * FROM products");
		$products_array 	= [];

		foreach ($products as $nd){
			$products_array = array_merge ($products_array, [$nd]);
		}

		return $products_array;
	}

	function int_to_rank(int r){
		switch(r){
			case 0 : return "low";

			case 1 : return "normal";

			case 2 : return "high";
		}
	}