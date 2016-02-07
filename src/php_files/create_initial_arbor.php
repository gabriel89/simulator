<?php
	// include files
	include_once ('sql_execute.php');
	include_once ('common_functions.php');
	include_once ('globals.php');

	// connect to DB
	$servername = "localhost";
	$username 	= "sim";
	$password 	= "sim";
	$dbname 	= "sim";

	// Create connection
	$con = new mysqli ($servername, $username, $password, $dbname);

	// Check connection
	if ($con->connect_error) {
	    die ("Connection failed: " . $con->connect_error);
	}

	// truncate table
	mysqli_query ($con,'TRUNCATE TABLE nodes');

	// start reading CSV contents
	read_CSV (file_get_contents ('../../data/initializer.csv'), $con);

	// close connection
	$con->close();

	// ----------------------------------------------------------------------------------------------------------

	// function to read from csv file
	function read_CSV ($content, $con) {
		global $nodes;
		global $products;

		$nodes 		= checkNodesGlobalVariable ($con);
		$products 	= checkProductsGlobalVariable ($con);

		// get all the lines of the csv file
		$lines = explode("\n", $content);

		// get rid of first line of the file within the varable
		$lines = removeNormalizationFlag ($lines);

		// get the number of nodes
		$node_count = sizeof(explode(";", $lines[0])) - 1;

		// iterate through all the lines of the file
		// line $i corresponds to $nodes[$i]'s $links
		for ($i = 0; $i < $node_count; $i++) {
			// get corresponding line
			$line_links = explode(";", $lines[$i]);
			// get rid of first element | REDUNDANT
			array_shift($line_links);

			// prepare variables corresponding to table fields
			$ID 		= '';
			$links 		= '';
			$requests 	= '';
			$serves 	= '';
			$quantity 	= '';
			$money 		= '';

			//set ID
			$ID = $i;

			//set links
			// TODO!: when a node has 0 links, $links will be empty and will cause an error
			for ($j = 0; $j < $node_count; $j++) {
				if ($line_links[$j] != 0) {
					// node $i has link to node $j
					$links .= $j . ',';
				}
			}

			//remove tailing ','
			$links = trim($links, ',');
			
			//set requests if $products is not an empty array
			if (!empty($products)) {
				for ($j = 0; $j < sizeof($products); $j++) {
					//for each product randomize check for need and create specific request
					if (floor(frand(3, 5, 7, 3)) % floor(frand(1, 3, 5, 2)) == 0){
						//set product ID
						$request = 'P'. $j . '|';
						//set quantity
						$request .= ceil(frand(10)) . '|';
						//set priority
						$request .= (floor(frand(25)) % 3) . '^';

						$requests .= $request;
					}
				}
				//remove tailing '^'
				$requests = trim($requests, '^');
			}

			//set serves and quantity
			$idx = (empty($products) ? 0 : floor(frand(sizeof($products)) % sizeof($products)));
			$serves 	= 'P' . $idx;
			$quantity 	= floor(mt_rand(20, 70));
			
			// update global quantity for served product
			$products[$idx]['global_quantity'] += $quantity;

			// set money
			$money = mt_rand(100, 200);
			
			//commit to DB
			execute_sql('<create_initial_arbor.php>', $con, "INSERT INTO nodes (ID, links, requests, serves, quantity, money) VALUES ('" . $ID . "', '" . $links . "', '" . $requests . "', '" . $serves . "', '" . $quantity ."', '" . $money ."')");
		}

		/// INSERET PRODUCTS CALCULATIONS HERE
		foreach ($products as $p){
			$base_cost = calc_base_cost($p['max_cost'], $p['global_quantity'], 1);
			execute_sql('<create_initial_products.php>', $con, "UPDATE products SET base_cost='" . $base_cost . "', global_quantity='" . $p['global_quantity'] . "' WHERE name='" . $p['name'] . "'");
		}

		// unset large array to free up memory
		unset ($rows);
		unset ($headings);
	}
