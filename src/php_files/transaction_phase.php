<?php
	include_once ('globals.php');

	//function that takes the nodes id and removes it
	function removeNode($id) {
		global $nodes;
		global $null_refs;

		//first when we want to remove a node we have to remove all the nodes that have it's id in it's links
		for ($i = 0 ; $i < sizeof($nodes) ; $i++) {
			$links = explode(',', $nodes[$i]['links']);
			for ($j = 0 ; $j < sizeof($links); $j++) {
				if($links[$j] === $id) {
					//we remove the nodes id from the links of the other nodes
					unset($links[$j]);
				}
			}
			//we reconstruct the links with the node removed 
			$nodes[$i]['links'] = implode(',', $links);
			if($nodes[$i]['id'] == $id) {
				//we won't remove the index from the nodes array, instead we will delete all the information contained
				//and will push the index of the node that was removed in the null_refs array , for future uses when we
				//will want to add another node
				resign($i);
				array_push($null_refs,$i);
			}
		}
	}

	//empty the fields of the node we want to remove
	function resign($index) {
		global $nodes;

		$nodes[$index]['links'] = "";
		$nodes[$index]['requests'] = "";
		$nodes[$index]['serves'] = "";
		$nodes[$index]['quantity'] = "";
		$nodes[$index]['money'] = "";	
		$nodes[$index]['product_quality'] = "";
		$nodes[$index]['pay_up'] = "";
	}

	//function which increases a product quality 
	function investInProductQuality ($id) {
		global $nodes;
		global $products;
		$price_for_quality_improvement_amplifier = 10;
		$buget_for_investment = 0.3;

		echo"<br/>";
		//	if the product quality the node serves is at its finest the node can't invest in the increase of the product quality
		if($nodes[$id]['product_quality'] === 0.9) {
			return;
		}
		else{
			for($j = 0 ; $j < sizeof($products) ; $j++) {
				//find the product that matches the product the node serves
				if($products[$j]['name'] === $nodes[$id]['serves']) {
					//to increase the quality with a unit there will be a cost 
					$price_for_quality_increase = $products[$j]['max_cost'] * $price_for_quality_improvement_amplifier + $nodes[$id]['product_quality'] * $price_for_quality_improvement_amplifier;
					//the node will not invest all it's money in the quality improvement so it will have a budget
					$money_for_investment = $nodes[$id]['money'] - ($nodes[$id]['money'] * $buget_for_investment);
					
					$product_quality = $nodes[$id]['product_quality'];
					//while the product quality increase budget is not reached keep increasing the quality
 					while(($money_for_investment - $price_for_quality_increase) > 0 && $product_quality !== 0.9) {
						$product_quality += 0.01;
						$money_for_investment -= $price_for_quality_increase;
					}
					$nodes[$id]['product_quality'] = $product_quality;
					break;
				}
			}
			
		}
	}

	//function that change the nodes product
	function changeProduct($id) {
		global $nodes;
		global $products;
		$requests = '';
		if (!empty($products)) {
			for ($j = 0; $j < sizeof($products); $j++) {
				//for each product randomize if product is requested
				//a node may not request the product it produces
				if ((floor(frand(3, 5, 7, 3)) % 2 == 0) && ($id != $j)) {
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
		// REQUEST FAILSAFE
		if ($requests === '') {
			//set product ID
			$requests = 'P' . generateProductFailSafe($nodes[$id]['serves']) . '|';
			//set quantity
			$requests .= ceil(frand(10)) . '|';
			//set priority
			$requests .= (floor(frand(25)) % 3);
		}
		
		//here we set what requests, serves, product_quality,links and quantity the node will have
		$nodes[$id]['requests'] = $requests;
		$nodes[$id]['serves'] = 'P'.rand(0, sizeof($products) - 1);
		$nodes[$id]['product_quality']= frand(1,0.1,1,2);
		$nodes[$id]['quantity'] = floor(mt_rand(20, 70));
		$nodes[$id]['links'] = generateLinks($id);
	}

	function generateLinks($id) {
		global $nodes;
		$requests = unserialize_requests($nodes[$id]['requests']);
		$links = [];
		$linkCost = 50;
		//we search for other nodes that serve what the current node needs and create a link between them if the current node has the money for it
		for($i = 0 ; $i < sizeof($nodes) ; $i++) {
			foreach ($requests as $request) {
				if($nodes[$i]['serves'] === $request[0] && ($nodes[$id]['money'] - $linkCost) > 0) {
					array_push($links, $nodes[$i]['id']);
					$nodes[$id]['money'] -= $linkCost;
					break;
				}
			}
		}

		return implode(',', $links);
	}

	function generateProductFailSafe($serves) {
		global $products;
		for ($i = 0; $i < sizeof($products); $i++) {
			if (($serves != $i) && (floor(frand(3, 5, 7, 3)) % 2 == 0)) {
				return $i;
			}
		}
		// if no product was chosen, try again
		return generateProductFailSafe($serves);
	}
?>