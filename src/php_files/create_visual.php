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
		$betweenness = getBetweenness();

		foreach ($nodes as $row) {
			$visual .= 'n' . $row['id'] . '{';
			
			//set node visual properties
			$visual .= 'shape:dot, color:' . genColor($row['links'], $betweenness[0], $betweenness[1]) . ', ';

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

	function genColor($links, $min, $max){
		// link for gradient maker
		// http://www.perbang.dk/rgbgradient/
		//$palette = ['#FF0000','#E50019','#CC0033','#B2004C','#990066','#7F007F','#660099','#4C00B2','#3300CC','#1900E5','#0000FF'];
		$palette = ['#0000FF','#1919E5','#3333CC','#4C4CB2','#666699','#7F7F7F','#999966','#B2B24C','#CCCC33','#E5E519','#FFFF00'];

		$link_size = explode(',', $links);
		$link_size = sizeof($link_size);
		
		// calculate percentage of link_size in interval [min, max]
		$p = floor((($link_size - $min) / ($max - $min)) * 10);

		return $palette[$p];
	}

	function getBetweenness(){
		global $nodes;

		//init r with the maximum number of links possible
		$r = [100000, 1];

		foreach ($nodes as $node){
			//get number of links for node
			$s = sizeof(explode(',', $node['links']));

			if ($s < $r[0]){
				$r[0] = $s;
			}
			if ($s > $r[1]){
				$r[1] = $s;
			}
		}

		return $r;
	}

