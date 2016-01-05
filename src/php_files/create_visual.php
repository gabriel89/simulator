<?php
	// include files
	include_once ('sql_execute.php');

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

	// retrieve a list of products and nodes
	$nodes 		= execute_sql_and_return ('<create_visual.php>', $con, "SELECT * FROM nodes");
	$products 	= execute_sql_and_return ('<create_visual.php>', $con, "SELECT name, value FROM products");

	// retrieve procedural arbor to be able to visualize it
	$arbor = createVisual ($nodes, createArrayFromObject ($products));

	// write initial log
	writeInitialLog ($arbor);

	// close connection
	$con->close ();

	echo $arbor;

	// ----------------------------------------------------------------------------------------------------------

	// function to create the code for the visual representation of the arbor
	function createVisual ($nodes, $products) {
		$visual = '';

		while ($row = mysqli_fetch_assoc ($nodes)) {
			$visual .= $row['name'] . '{';
			
			// check if node is producer
			if ($row['is_producer']){
				$visual .= 'color:red, shape:dot';
			}

			// set hasProducts list
			$has_product = explode (',', $row['has_product']);
			if (count ($has_product) > 0) {
				$concatenated = [];
				$visual .= 'hasProduct: [';

				foreach ($has_product as $key => $value) {
					$concatenated[$key] = "$value (" . $products[$value] . ")";
				}

				$visual .= implode (', ', $concatenated) . ']';
			}

			// set needsProducts list
			$needs_product = explode (',', $row['needs_product']);
			$needs_product = unserialize($needs_product[0]);
				
			if(isset($needs_product[0])) {
				$needs_product = $needs_product[0];
				if (count ($needs_product) > 0) {
					$concatenated = [];
					$visual .= ', needsProduct: [';
					foreach ($needs_product as $key => $value) {
						$concatenated[$key] = "$value (" . $products[$needs_product["p_name"]] . ")";
					}

					$visual .= implode (', ', $concatenated) . ']';
				}
			}

			// set wealth
			$visual .= ', money: ' . $row['money'];

			// add new-lines
			$visual .= "}\n\n";

			// set links
			if (isset ($row['link_to'])) {
				foreach (explode(',', $row['link_to']) as $value) {
					$visual .= $row['name'] . "--$value\n\n";
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
