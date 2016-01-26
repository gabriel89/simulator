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
		for($i = 0; $i < $node_count; $i++){
			// get corresponding line
			$line_links = explode(";", $lines[$i]);
			// get rid of first element | REDUNDANT
			array_shift[$links];

			// prepare variables corresponding to table fields
			$ID 		= '';
			$links 		= '';
			$requests 	= '';
			$serves 	= '';
			$quantity 	= '';

			//set ID
			$ID = $i;

			//set links
			for($j = 0; $j < $node_count; $j++){
				if($line_links[$j] != 0){
					// node $i has link to node $j
					$links .= $j . ',';
				}
			}
			//remove tailing ','
			$links = explode(",", $links);
			$links=array_reverse($links);
			array_shift($links);
			$links=array_reverse($links);
			$links = implode(",", $links);

			//set requests
			for($j = 0; $j < sizeof($products); $j++){
				//for each product randomize check for need and create specific request
				if(floor(frand(3, 5, 7, 11)) % floor(frand(1, 3, 5, 7)) == 0){
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
			$requests = explode("^", $requests);
			$requests=array_reverse($requests);
			array_shift($requests);
			$requests=array_reverse($requests);
			$requests = implode("^", $requests);

			//set serves and quantity
			$serves = 'P' . floor(frand(sizeof($products)) % sizeof($products));
			$quantity = floor(frand(50));
			// update global quantity for served product
			$productIndex = explode('P', $serves);
			array_shift($productIndex);
			$products[$productIndex]['global_quantity'] += $quantity;

			//commit to DB
			execute_sql('<create_initial_arbor.php>', $con, "INSERT INTO nodes (ID, links, requests, serves, quantity) VALUES ('" . $i . ", " . $links . ", " . $requests . ", " . $serves . ", " . $quantity ."')");
		}

		// unset large array to free up memory
		unset ($rows);
		unset ($headings);
	}
