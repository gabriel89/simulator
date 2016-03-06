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

//---------------------------------I-N-V-E-S-T-M-E-N-T---O-P-T-I-O-N-S----------------------------------

	function investInProduction($idx){
		global $nodes;
		global $products;
	}
	//---------------   LINK INVESTMENT FUNCTION   ---------------\\
	function investInLinks($idx){
		global $nodes;
		global $products;

		// picks a list of nodes that produce a requested product with which to link
		// a link cost a big sum of money
		// link cost is influenced by production quality of the desired node
		// ex: base link cost == baseLinkCost; node has production quality of 0.78 => linkCost = baseLinkCost * 1.78;
		// has following subfunctions: 	getSingleLinkInvestmentCost($node A, $node B), getLinkInvestmentCost($idx),
		//								link($node A, $node B), getLinkTargets($idx)
	}

	//--------------   LINK INVESTMENT SUBFUNCTIONS   ---------------\\
	// returns an array containing the nodes that idx would link to
	function getLinkTargets($idx){

	}
	// returns the cost of linking node A to node B
	function getSingleLinkInvestmentCost($nodeA, $nodeB){

	}
	// returns the cost of linking node at idx with all selected link targets
	function getLinkInvestmentCost($idx){

	}
	// links node A to node B : adds B to A's links and vise-versa, removes the link cost from A's money
	function link($nodeA, $nodeB){

	}

	function investInExpansion($idx){
		global $nodes;
		global $products;
	}