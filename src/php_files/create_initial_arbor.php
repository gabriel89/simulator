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

	// truncate tables
	mysqli_query ($con,'TRUNCATE TABLE products');
	mysqli_query ($con,'TRUNCATE TABLE nodes');

	$file 	= file_get_contents ('../../data/initializer.csv');
	$csv 	= read_CSV ($file, $con);

	// close connection
	$con->close();

	// ----------------------------------------------------------------------------------------------------------

	// function to read from csv file
	function read_CSV ($content, $con){
		$rows 			= explode ("\n", $content);
		$headings 		= explode (";", $rows[0]);
		$headingsArr 	= [];

		generateProducts ($con);
		// pop empty element from the list
		array_shift ($headings);

		// pop headings from the list
		array_shift($rows);

		// add nodes to table, with extra data for each node
		foreach ($headings as $value){
			// select a random product
			$has_prod_search 	= $con->query("SELECT name FROM products ORDER BY RAND() LIMIT 1");
			$needs_prod_search 	= $con->query("SELECT name FROM products ORDER BY RAND() LIMIT 1");
			$has_prod 			= $has_prod_search->fetch_assoc()['name'];
			$needs_prod 		= $needs_prod_search->fetch_assoc()['name'];

			execute_sql('<create_initial_arbor.php>', $con, "INSERT INTO nodes (name, needs_product, has_product, money) VALUES ('".trim ($value)."', '".trim ($needs_prod)."', '".trim ($has_prod)."', '".frand(10)."')");
		}

		// also add the links
		foreach ($rows as $key => $value){
			$row 		= explode (";", $rows[$key]);
			$rowNode 	= str_replace ("\r" , "" , str_replace ("\n" , "" , $row[0])); 

			// pop row[0], which represents the node, from the list
			array_shift($row);

			foreach ($row as $i_key => $i_value){
				if (intval (str_replace ("\r" , "" , str_replace ("\n" , "" , $row[$i_key]))) == 1){
					$local_heading = str_replace ("\r" , "" , str_replace ("\n" , "" , $headings[$i_key]));

					if (trim ($rowNode) != ''){
						$node = preventDuplicate ($con, trim ($rowNode), trim ($local_heading));
						execute_sql('<create_initial_arbor.php>', $con, "UPDATE nodes SET link_to = '".$node."' WHERE name = '".$local_heading."'");
						
						// also add the inverse of it to have the linkTo attribute set
						$node = preventDuplicate ($con, trim ($local_heading), trim ($rowNode));
						execute_sql('<create_initial_arbor.php>', $con, "UPDATE nodes SET link_to = '".$node."' WHERE name = '".$rowNode."'");
					}
				}
			}
		}

	}

	// function to prevent duplicated nodes to being added into the link_to fields
	function preventDuplicate ($con, $to_add, $row_node) {
		$node_search 	= $con->query("SELECT link_to FROM nodes WHERE name = '".$row_node."' LIMIT 1");
		$link_to 		= $node_search->fetch_assoc()['link_to'];
		$link_to 		= explode(',', $link_to);

		$add = true;
		foreach ($link_to as $key => $value) {
			if ($value == $to_add) {
				$add = false;
			}
		}

		if ($add) {
			array_push ($link_to, $to_add);
		}

		return trim (implode (',', $link_to), ',');
	}

	// random floating-point generator
	function frand ($modifier = 1, $min = 0, $max = 9, $decimals = 2) {
	 	$scale = pow(10, $decimals);

		return (mt_rand($min * $scale, $max * $scale) / $scale) * $modifier;
	}

	// function to generate products and their values
	function generateProducts ($con) {
		$prod_count = 7;

		for ($i = 0; $i < $prod_count; $i++) {
			execute_sql('<create_initial_arbor.php>', $con, "INSERT INTO products (name, value) VALUES ('P".$i."', '".frand()."')");
		}
	}
	// end generate products