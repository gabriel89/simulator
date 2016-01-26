<?php
	// include files
	include_once ('sql_execute.php');
	include_once ('globals.php');

	// connect to DB
	$servername = "localhost";
	$username 	= "sim";
	$password 	= "sim";
	$dbname 	= "sim";
	// retrieve procedural arbor to be able to visualize it
	$arbor = createVisual ();

	// write initial log
	writeInitialLog ($arbor);

	echo $arbor;

	// ----------------------------------------------------------------------------------------------------------

	// function to create the code for the visual representation of the arbor
	function createVisual () {
		global $nodes;
		global $products;
		$visual = '';

		foreach ($nodes as $row) {
			$visual .= 'n' . $row['ID'] . '{';
			
			// check if node is producer
			if ($row['is_producer']){
				$visual .= 'color:red, shape:dot';
			}
			// set serves
			$serves = 'serves: ' . $row['serves'] . ',';

			$visual .= $serves;

			$visual .= 'requests: ' . $row['requests'] . ',';

			// set wealth
			$visual .= ', money: ' . $row['money'];

			// add new-lines
			$visual .= "}\n\n";

			// set links
			if (isset ($row['links'])) {
				foreach (explode(',', $row['links']) as $value) {
					$visual .= 'n' . 	$row['ID'] . "--$value\n\n";
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
