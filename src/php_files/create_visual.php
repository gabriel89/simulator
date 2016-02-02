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
		$nodes 		= checkNodesGlobalVariable ($con);
		$products 	= checkProductsGlobalVariable ($con);
		$pairs 		= '';
		$visual 	= '';

		foreach ($nodes as $row) {
			$visual .= 'n' . $row['id'] . '{';
			
			//set node visual properties
			$visual .= 'shape:dot, color:' . genColor($row['links']) . ', ';

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
						$visual .= 'n' . $row['id'] . "--n$value {color: #777777, weight: 1.5}\n\n";
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

	function genColor($links){
		$link_size = explode(',', $links);
		$link_size = sizeof($link_size);
		// link for gradient maker
		// http://www.perbang.dk/rgbgradient/
		$palette = ['#FF0000','#E2001C','#C60038','#AA0055','#8D0071','#71008D','#5500AA','#3800C6','#1C00E2','#0000FF'];

		return $palette[ceil(9 * ($link_size / 6))];
	}




