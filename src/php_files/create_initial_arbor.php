<?php
	$servername = "localhost";
	$username 	= "sim";
	$password 	= "sim";
	$dbname 	= "sim";

	// Create connection
	$con = new mysqli($servername, $username, $password, $dbname);

	// Check connection
	if ($con->connect_error) {
	    die ("Connection failed: " . $conn->connect_error);
	}

	// truncate tables
	mysqli_query($con,'TRUNCATE TABLE products');
	mysqli_query($con,'TRUNCATE TABLE nodes');

	$file 	= file_get_contents('../../data/initializer.csv');
	$csv 	= read_CSV ($file, $con);


	$con->close();



	function read_CSV ($content, $con){
		$rows 			= explode ("\n", $content);
		$headings 		= explode (";", $rows[0]);
		$headingsArr 	= [];

		generateProducts($con);

		// pop empty element from the list
		array_shift($headings);

		// pop headings from the list
		array_shift($rows);

		// add nodes to table, with extra data for each node
		foreach ($headings as $value){
			// select a random product
			$has_prod_search 	= $con->query("SELECT name FROM products ORDER BY RAND() LIMIT 1");
			$needs_prod_search 	= $con->query("SELECT name FROM products ORDER BY RAND() LIMIT 1");
			$has_prod 			= $has_prod_search->fetch_assoc()['name'];
			$needs_prod 		= $needs_prod_search->fetch_assoc()['name'];

			execute_sql($con, "INSERT INTO nodes (name, needs_product, has_product, money) VALUES ('".$value."', '".$needs_prod."', '".$has_prod."', '".frand(10)."')");
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
						execute_sql($con, "UPDATE nodes SET link_to = CONCAT_WS(',', link_to, '".$rowNode."') WHERE name = '".$local_heading."'");
						
						// also add the inverse of it to have the linkTo attribute set
						execute_sql($con, "UPDATE nodes SET link_to = CONCAT_WS(',', link_to, '".$local_heading."') WHERE name = '".$rowNode."'");
					}
				}
			}
		}

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
			execute_sql($con, "INSERT INTO products (name, value) VALUES ('P".$i."', '".frand()."')");
		}
	}
	// end generate products

	// sql insert and error handling
	function execute_sql($con, $sql){
		if ($con->query($sql) !== TRUE) {
			echo "<create_initial_arbor.php> Error: " . $sql . "<br>" . $con->error;
		}
	}



