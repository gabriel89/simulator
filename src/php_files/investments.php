<?php
	include_once ('globals.php');
	include_once ('common_functions.php');

//---------------------------------I-N-V-E-S-T-M-E-N-T---O-P-T-I-O-N-S----------------------------------

	function investInProduction($idx){
		global $nodes;
		global $products;
	}
	//---------------   LINK INVESTMENT FUNCTIONS   ---------------\\
	// picks a list of nodes that produce a requested product with which to link
		// a link cost a big sum of money
		// link cost is influenced by production quality of the desired node
		// ex: base link cost == baseLinkCost; node has production quality of 0.78 => linkCost = baseLinkCost * 1.78;
		// has following subfunctions: 	getSingleLinkInvestmentCost($node A, $node B), getLinkInvestmentCost($idx),
		//								link($node A, $node B), getLinkTargets($idx)

	function getLinkInvestmentCost($idx, $linkTargets){
		// returns the cost of linking node at idx with all selected link targets
		$investmentCost = 0;
		foreach ($linkTargets as $linkTarget){
			$investmentCost += getSingleLinkInvestmentCost (100, $linkTarget);
		}

		return $investmentCost;
	}

	function investInLinks($idx, $linkTargets, $investmentCost){
		global $nodes;

		// completes the linking of node at idx with all link targets
		foreach ($linkTargets as $linkTarget){
			linkNodes ($idx, $linkTarget);
		}

		$nodes[$idx]['money'] -= $investmentCost;
	}

	//--------------   LINK INVESTMENT SUBFUNCTIONS   ---------------\\
	// returns an array containing the nodes that idx would link to
	function getLinkTargets($idx){
		$requestSuppliers = getProviders ($idx);

		$linkTargets = [];

		for($i = 0; $i < sizeof($requestSuppliers); $i++){
			if ((int)(mt_rand (0, $production_count)) % 7 == 0){
				$linkTargets = array($linkTargets, [$requestSuppliers[$i]]);
			}
		}

		return $linkTargets;
	}

	// links node A to node B : adds B to A's links and vise-versa, removes the link cost from A's money
	function linkNodes($nodeA, $nodeB){
		global $nodes;

		$nodes[$nodeA]['links'] .= (',' . $nodeB);
		$nodes[$nodeB]['links'] .= (',' . $nodeA);

		$nodes[$nodeA]['money'] -= getSingleLinkCost (100, $nodeB);
	}

	//------------- EXPANSION INVESTMENT FUNCTIONS -----------------------
	function getExpansionInvestmentTarget($idx){
		global $nodes;
		global $products;

		$index = getNextInsertIndex();
		addToLog("next index is " + $index);

		$serves = frand (1, 0, sizeof($products), 0);

		$requests = '';

		if (!empty($products)) {
			for ($j = 0; $j < sizeof($products); $j++) {
				//for each product randomize if product is requested
				//a node may not request the product it produces
				if ((floor(frand(3, 5, 7, 3)) % 2 == 0) && ($idx != $j)) {
					//set product ID
					$request = 'P'. $j . '|';
					//set quantity
					$request .= ceil(frand(10)) . '|';
					//set priority
					$request .= (floor(frand(25)) % 3) . '^';

					$requests .= $request;
				}
			}
			//remove tailing '^'
			$requests = trim($requests, '^');
		}

		$links = '' + $idx;

		$productionQuality = frand(1, 0.1, 1, 2);

		$rez['id'] = $index;
		$rez['serves'] = $serves;
		$rez['requests'] = $requests;
		$rez['links'] = $links;
		$rez['parent'] = $idx;
		$rez['money'] = frand(1, 100, 300, 2);
		$rez['production_quality'] = 0.2;
		$rez['quantity'] = floor(frand(1, 10, 100, 0));

		return $rez;
	}

	function getExpansionInvestmentCost($investmentTarget, $idx){
		global $nodes;

		$moneyCost = $investmentTarget['money'] + 100;

		return $moneyCost;
	}

	function investInExpansion($idx, $investmentTarget, $investmentCost){
		global $nodes;
		$insertId = $investmentTarget['id'];

		$nodes[$idx]['links'] .= ',' . $insertId;
		$nodes[$idx]['money'] -= $investmentCost;

		$nodes[$insertId] = $investmentTarget;
	}

	function getNextInsertIndex(){
		global $nodes;
		return sizeof($nodes);
	}