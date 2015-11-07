<?php
	function christmas_phase($con, &$nodes_array){
		print_r("\n\nIT'S CHRISTMAS DAY! EVERYBODY GETS STUFF\n");
		foreach ($nodes_array as &$nd){
			// get product that $nd sells
			$product = execute_sql_and_return ('<simulator.php>', $con, "SELECT value FROM products WHERE name = '" . $nd['has_product'] . "'");
			$product = mysqli_fetch_array($product);
			// update has_product_count for each node
			$production_buget = $nd['money'] / 4;
			$production_count = (int) ($production_buget / $product['value']);
			$added_product_count = (int) (mt_rand (0, $production_count));
			$nd['has_product_count'] += $added_product_count;
			print_r($nd['name'] . " has produced " . $added_product_count . " of " . $nd['has_product'] . "\n");
		}
		print_r("\n\n");
	}