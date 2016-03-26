<?php
	include_once ('globals.php');
	include_once ('common_functions');
	include_once ('investments.php');

	// function to treat economic phase
	function economicPhase ($con, $nodes) {
		decideUponInvestment();
=======
	function christmas_phase ($con, &$nodes_array) {
		print_r ("\n\nIT'S CHRISTMAS DAY! EVERYBODY GETS STUFF\n");

		foreach ($nodes_array as &$nd) {
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

	function investInProduction ($idx) {
		global $nodes;
		global $products;
	}

	function decideUponInvestment ($idx) {
		//investment is decided in regard to the minimum cost of the investment option
		//ex: if production costs more than links => chose links
		//ex2: if 
		// LINK INVESTMENT PARAMS
		$linkTargets = getLinkTargets ($idx);
		$linkInvestmentCost = getLinkInvestmentCost ($idx);

		// EXPANSION INVESTMENT PARAMS

		$expansionTarget = getExpansionInvestmentTarget ($idx);
		$expansionCost = getExpansionInvestmentCost ($expansionTarget);
		// investment decision is made based upon the minimum investment cost
		// 0 == LINKS
		// 1 == PRODUCTION
		// 2 == EXPANSION
		// 3 == RESIGNATION
		$investmentDecision = 0; 

		if ($investmentDecision === 0){
			investInLinks ($idx, $linkTargets, $linkInvestmentCost);
		}

		if($investmentDecision === 2){
			investInExpansion ($idx, $expansionTarget, $expansionCost);
		}
		
=======
	function getLinkInvestmentCost ($idx, $linkTargets) {
		// returns the cost of linking node at idx with all selected link targets
		$investmentCost = 0;
		
		foreach ($linkTargets as $linkTarget){
			$investmentCost += getSingleLinkInvestmentCost ($idx, $linkTarget);
		}

		return $investmentCost;
	}

	function investInLinks ($idx, $linkTargets) {
		// completes the linking of node at idx with all link targets
		foreach ($linkTargets as $linkTarget){
			link ($idx, $linkTarget);
		}
	}

	//--------------   LINK INVESTMENT SUBFUNCTIONS   ---------------\\
	// returns an array containing the nodes that idx would link to
	function getLinkTargets ($idx) {
		$requestSuppliers = getProviders ($idx);

		$linkTargets = [];

		for ($i = 0; $i < sizeof($requestSuppliers); $i++) {
			if ((int)(mt_rand (0, $production_count)) % 7 == 0) {
				$linkTargets = array($linkTargets, [$requestSuppliers[$i]]);
			}
		}

		return $linkTargets;
	}

	// returns the cost of linking node A to node B
	function getSingleLinkInvestmentCost ($nodeA, $nodeB) {
		// final link cost is dependant on the supplier's production quality
		// could also depend on length of previous transaction path (the longer it was, the more it will cost)
		global $nodes;
		
		$linkCost = 100;

		return ($linkCost * (1 + $nodes[$nodeB]['productionQuality']));
	}

	// links node A to node B : adds B to A's links and vise-versa, removes the link cost from A's money
	function linkNodes ($nodeA, $nodeB){
		global $nodes;

		$nodes[$nodeA]['links'] .= (',' . $nodeB);
		$nodes[$nodeB]['links'] .= (',' . $nodeA);

		$nodes[$nodeA]['money'] -= getSingleLinkInvestmentCost ($nodeA, $nodeB);
	}

	function investInExpansion ($idx) {
		global $nodes;
		global $products;
	}