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

	$file 	= file_get_contents ('../../data/initializer.csv');
	$csv 	= read_CSV ($file, $con);

	// close connection
	$con->close();

	// ----------------------------------------------------------------------------------------------------------

	// function to read from csv file
	function read_CSV ($content, $con) {
		global $nodes;
		global $products;

		// get all the lines of the csv file
		$lines = explode("\n", $content);

		// get the number of nodes
		$node_count = sizeof(explode(";", $lines[0])) - 1;

		// get rid of first line of the file | DON'T NEED ANYMORE
		array_shift($lines);

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
					if (floor(frand(3, 5, 7, 11)) % floor(frand(1, 3, 5, 7)) == 0){
						//set product ID
						$request = 'P'. $j . '|';
						//set quantity
						$request .= frand(10) . '|';
						//set priority
						$request .= (floor(frand(25)) % 3) . '^';

						$requests .= $request;
					}
				}
				//remove tailing '^'
				$requests = trim($requests, '^');
			}

			//set serves and quantity
			$serves = 'P' . (empty($products) ? 0 : floor(frand(sizeof($products)) % sizeof($products)));
			$quantity = floor(frand(50));

			// get product index
			$productIndex = explode('P', $serves);
			
			// update global quantity for served product
			if (!empty($products))
				$products[$productIndex[1]]['global_quantity'] += $quantity;

			//commit to DB
			execute_sql('<create_initial_arbor.php>', $con, "INSERT INTO nodes (ID, links, requests, serves, quantity) VALUES ('" . $ID . "', '" . $links . "', '" . $requests . "', '" . $serves . "', '" . $quantity ."')");
		}

		// unset large array to free up memory
		unset ($rows);
		unset ($headings);
	}
