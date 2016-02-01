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

	// truncate table
	mysqli_query ($con,'TRUNCATE TABLE products');

	$file 	= file_get_contents ('../../data/initializer.csv');

	generateProducts ($file, $con);

	// close connection
	$con->close();

	// ----------------------------------------------------------------------------------------------------------

	// retrieve number of nodes
	function retrieveNodesCount ($content, $con) {
		$rows 		= explode ("\n", $content);
		$headings 	= explode (";", $rows[0]);
		
		// pop empty element from the list
		array_shift ($headings);

		return count ($headings);
	}

	// function to generate products and their values
	function generateProducts ($file, $con) {
		$products 	= checkProductsGlobalVariable ($con);
		$multiplier = 1.4;
		$n_node 	= retrieveNodesCount ($file, $con);

		for ($i = 0; $i < $n_node * $multiplier; $i++) {
			execute_sql('<create_initial_products.php>', $con, "INSERT INTO products (name, base_cost, max_cost, global_quantity) VALUES ('P" . $i . "', '" . 0 . "', '" . frand() . "', '" . 0 . "')");
		}

		$products = fetch_nodes_toArray ($con);
	}
	// end generate products