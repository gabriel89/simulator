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
	    die ("Connection failed: " . $conn->connect_error);
	}

	// retrieve procedural arbor to be able to visualize it
	$arbor = createVisual ($con);

	// write initial log
	writeInitialLog ($arbor);

	echo $arbor;

	// close connection
	$con->close();

	// ----------------------------------------------------------------------------------------------------------

	// function to create the code for the visual representation of the arbor
	function createVisual ($con) {
		global $nodes;
		global $products;
		$pairs = '';
		$visual = '';

		$nodes = fetch_nodes_toArray ($con); // global variable is empty
		$products = fetch_products_toArray ($con); // global variable is empty

		foreach ($nodes as $row) {
			$visual .= 'n' . $row['id'] . '{';
			
			// check if node is producer
			// if ($row['is_producer']){
			// 	$visual .= 'color:red, shape:dot';
			// }

			// set serves and requests
			if ($row['serves'])
				$visual .= 'serves: ' . str_replace ('^', ', ', $row['serves']) . ',';
			if ($row['requests'])
				$visual .= ' requests: ' . str_replace ('^', ', ', $row['requests']) . ',';

			// set wealth
			$visual .= ' money: ' . $row['money'];

			// add new-lines
			$visual .= "}\n\n";

			// set links
			if (isset ($row['links'])) {
				foreach (explode(',', $row['links']) as $value) {
					// hacky solution, but avoids duplicates by searching for a simple |n0-n1| pairs in a string
					if (strpos($pairs, '|n' . $value . '-n' . $row['id'] . '|') === false){
						$pairs .= '|n' . $row['id'] . '-n' . $value . '|';
						$visual .= 'n' . $row['id'] . "--n$value\n\n";
					}
				}
			}

	    }

		return $visual;
	}

	// create array of products from object
	function createArrayFromObject ($products){
		$prod = [];

		while ($row = mysqli_fetch_assoc($products)){
			$prod[$row['name']] = $row['value'];
	    }

	    return $prod;
	}

	// Function to write initial data to the log
	function writeInitialLog ($content){
		$content = ";--------------------------------\n;        INITIAL SETTINGS\n;--------------------------------\n\n$content";

		$file = fopen ('../../data/log.txt', 'w+');
		
		fwrite ($file, $content);
		fclose ($file);
	}
	// End write initial
