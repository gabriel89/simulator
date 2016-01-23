<?php
	// include files
	include_once ('sql_execute.php');
	include_once ('common_functions.php');

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
	mysqli_query ($con,'TRUNCATE TABLE nodes');

	$file 	= file_get_contents ('../../data/initializer.csv');
	$csv 	= read_CSV ($file, $con);

	// close connection
	$con->close();

	// ----------------------------------------------------------------------------------------------------------

	// function to read from csv file
	function read_CSV ($content, $con) {
		$rows 			= explode ("\n", $content);
		$headings 		= explode (";", $rows[0]);
		
		// pop empty element from the list
		array_shift ($headings);

		// pop headings from the list
		array_shift($rows);

		// retrieve table of products
		$products = fetch_products_toArray($con);

		// add nodes to table, with extra data for each node
		foreach ($headings as $value){
			// select a random product
			$has_prod_search 	= $con->query ("SELECT name FROM products ORDER BY RAND() LIMIT 1");
			$has_prod 			= $has_prod_search->fetch_assoc()['name'];
			
			// select random number of products to be needed
			$products_check = [];
			for ($i = 0; $i < sizeof ($products); $i++){
				$products_check[$i] = 0;

				// make sure the product the node produces will not be added to the needed products
				if($products[$i]['name'] === $has_prod_search){
					$products_check[$i] = 1;
				}
			}
			// pick a random number of products to be chosen
			$num_prods = frand (1, 0, sizeof ($products) - 1, 0);

			// array to hold information regarding needed products
			$needed_prods = [];

			// pick random products by index from products array
			for($i = 0; $i < $num_prods; $i++){
				// pick a random index for retrieving product
				$index = frand (1, 0, sizeof ($products) - 1, 0);

				// check if product is valid for pick
				if($products_check[$index] === 0){
					// add selected product to needed products array
					$needed_prods[$i]['p_name'] 	= $products[$index]['name'];
					$needed_prods[$i]['value'] 		= $products[$index]['value'];
					// generate rank randomly for product
					$needed_prods[$i]['p_rank'] 	= int_to_rank (frand (1, 0, 2, 0));
					$needed_prods[$i]['p_count']    = rand(1,99);
				}
				// if product not valid, roll back and try again 
				else --$i;
			}
			// serialize the table
			$needed_prods = serialize ($needed_prods);

			execute_sql('<create_initial_arbor.php>', $con, "INSERT INTO nodes (name, needs_product, has_product, has_product_count, money) VALUES ('".trim ($value)."', '".trim ($needed_prods)."', '".trim ($has_prod)."', '".frand (10)."', '".frand (10)."')");
		}

		// also add the links
		foreach ($rows as $key => $value){
			$row 		= explode (";", $rows[$key]);
			$rowNode 	= str_replace ("\r" , "" , str_replace ("\n" , "" , $row[0])); 

			// pop row[0], which represents the node, from the list
			array_shift($row);

			foreach ($row as $i_key => $i_value) {
				if (intval (str_replace ("\r" , "" , str_replace ("\n" , "" , $row[$i_key]))) == 1) {
					$local_heading = str_replace ("\r" , "" , str_replace ("\n" , "" , $headings[$i_key]));

					if (trim ($rowNode) != ''){
						$node = preventDuplicate ($con, trim ($rowNode), trim ($local_heading));
						execute_sql ('<create_initial_arbor.php>', $con, "UPDATE nodes SET link_to = '".$node."' WHERE name = '".$local_heading."'");
						
						// also add the inverse of it to have the linkTo attribute set
						$node = preventDuplicate ($con, trim ($local_heading), trim ($rowNode));
						execute_sql ('<create_initial_arbor.php>', $con, "UPDATE nodes SET link_to = '".$node."' WHERE name = '".$rowNode."'");
					}
				}
			}
		}

		// unset large array to free up memory
		unset ($rows);
		unset ($headings);
	}

	// function to prevent duplicated nodes to being added into the link_to fields
	function preventDuplicate ($con, $to_add, $row_node) {
		$node_search 	= $con->query("SELECT link_to FROM nodes WHERE name = '".$row_node."' LIMIT 1");
		$link_to 		= $node_search->fetch_assoc()['link_to'];
		$link_to 		= explode(',', $link_to);
		$add 			= true;

		foreach ($link_to as $key => $value) {
			if ($value == $to_add) {
				$add = false;
			}
		}

		if ($add) {
			array_push ($link_to, $to_add);
		}

		// unset large array to free up memory
		unset ($node_search);

		return trim (implode (',', $link_to), ',');
	}