<?php
	include_once ('globals.php');
	include_once ('common_functions.php');
	include_once ('investments.php');

	// function to treat economic phase
	function economicPhase ($con, $nodes) {
		decideUponInvestment();

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
	}
}