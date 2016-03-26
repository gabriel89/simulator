<?php
	include_once ('globals.php');
	include_once ('common_functions');
	include_once ('investments.php');

	// function to treat economic phase
	function economicPhase ($con, $nodes) {
		decideUponInvestment();
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