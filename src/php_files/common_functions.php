<?php
	include_once ('globals.php');

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

	function fetch_nodes_toArray ($con){
		global $nodes;

		$nodes_result = execute_sql_and_return ($_SERVER['PHP_SELF'], $con, "SELECT * FROM nodes");
		$nodes 	= [];

		foreach ($nodes_result as $n_res){
			$nodes = array_merge ($nodes, [$n_res]);
		}

		return $nodes;
	}

	function fetch_products_toArray ($con){
		global $products;

		$products_result = execute_sql_and_return ($_SERVER['PHP_SELF'], $con, "SELECT * FROM products");
		$products 	= [];

		foreach ($products_result as $p_res){
			$products = array_merge ($products, [$p_res]);
		}

		return $products;
	}

	function checkNodesGlobalVariable ($con){
		global $nodes;

		if (empty($nodes)){
			$nodes = fetch_nodes_toArray ($con);
		}

		return $nodes;
	}

	function checkProductsGlobalVariable ($con){
		global $products;

		if (empty($products)){
			$products = fetch_products_toArray ($con);
		}

		return $products;
	}

	function unserialize_requests($requests_string){
		$result = [];
		$result = explode ('^', $requests_string);

		foreach($result as &$res){
			$res = explode('|', $res);
		}

		return $result;
	}

	function int_to_rank ($r){
		switch($r){
			case 0 : return "low";

			case 1 : return "normal";

			case 2 : return "high";
		}
	}

	function calc_base_cost($max_cost, $global_quantity, $quality_factor) {
		return ($global_quantity == 0) ?  0 : ( $max_cost / $global_quantity ) * $quality_factor;
	}

	function removeNormalizationFlag ($lines) {
		if (trim($lines[0]) == 'Normalized')
			array_shift($lines);

		return $lines;
	}

	function getProvidersOf($product_name){
		global $nodes;

		$rez = '';
		for ($i = 0; $i < sizeof($nodes); $i++){
			if ($nodes[$i]['serves'] === $product_name)
				$rez .= $i . ',';
		}

		$rez = trim($rez, ',');
		$rez = explode(',', $rez);

		return $rez;
	}

	function getProviders ($buyer_node){
		global $nodes;

		//returns a list of other nodes which sell the products buyer_node requests
		//the list is ordered by the priority of the request (most required -> least required)
		$requests = $nodes[$buyer_node]['requests'];
		$requests = explode('^', $requests);

		if ($requests[0] === '') return '';

		$max_priority = 0;

		foreach ($requests as &$request) {
			$request = explode('|', $request);

			if ($request[2] > $max_priority)
				$max_priority = $request[2];
		}

		$rez = [];

		while ($max_priority >= 0) {
			foreach ($requests as $request) {
				if ($request[2] === $max_priority)
					$rez = array_merge ($rez, getProvidersOf($request[0]));
			}

			$max_priority--;
		}

		return $rez;
	}