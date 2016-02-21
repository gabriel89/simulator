<?php
	//global declaration of heap and product array
	include_once ('common_functions.php');
	include_once ('sql_execute.php');
	include_once ('globals.php');
	$Q = [];

	function getConsumerPath ($con) {
		global $nodes;

		$nodes = checkNodesGlobalVariable ($con);
		$consumer_path 	= [];

		addToLog ("\n\n\n;--------------------------------\n;        ESTABLISHING TRANSACTION PATH\n;--------------------------------");
		
		for($b = 0; $b < sizeof($nodes); $b++) {
			$list 			= [];
			$providers 		= getProviders($b);
			
			for ($p = 0; $p < sizeof($providers); $p++) {
				if ($providers[$p] != null)
					$list = array_merge ($list, array (BFS ($b, $providers[$p])));
				else
					echo "<br />No providers found for $b <br /><br />";
			}
			
			$consumer_path = array_merge ($consumer_path, $list);

			addToLog ("\n\n" . implode ("\n", str_replace (',', ' -> ' , $list)));
		}

		return $consumer_path;
	}

	// function to finalize transaction:
	// for each element of $consumer_path we need to sell the product from the producer (first node) to the consumer (last node)
	// each intermediate node will add a personal profit to the total value of the previous node, thus product final price = initial price + profit node1 + profit node2 + ...
	function finalizeTransaction ($con, $consumer_path) {
		global $nodes;
		addToLog ("\n\n\n;--------------------------------\n;        FINALIZING TRANSACTIONS\n;--------------------------------\n\n");

		$last_sold_out_msg 	= '';

		// iterate through each set of paths | $consumer_path contains the set of every path to the potential buyers of each node
		foreach ($consumer_path as $current_path) {

			// retrieve the names of the nodes involved in this transaction | the first node is the seller | the last node is the buyer
			$inter_nodes = explode ('->', $current_path);
			// retrieve seller and buyer nodes from path
			$seller_node = $nodes[array_reverse($inter_nodes)[0]];
			//	check if the seller_node still has product to sell
			if (check_seller_has_product ($seller_node)) {
				$buyer_node = $nodes[$inter_nodes[0]];

				$products = $buyer_node['requests'];
				$products = unserialize_requests($products);

				foreach ($products as $product) {
					if($product[0] == $seller_node['serves']) {
						//	check if buyer still needs to purchase products
						if (check_buyer_needs_product ($product)) {
							// finalize transaction
							complete_purchase ($inter_nodes, $buyer_node, $seller_node, $con);
							$nodes[$seller_node["id"]] = $seller_node;
							$nodes[$buyer_node["id"]] = $buyer_node;
						}
					}
				}
			}
		}
		//	update database with new values for money and product quantities
		update_post_tranzaction ($con, $nodes);
	}

	//	refactorization of code to make code easier to manipulate
	//	functions to add : 	check_seller_has_product (); check_buyer_affords_product (); get_final_purchase_amount (); get_final_purchase_cost_ppc ();
	//						intermediate_profit_get (); check_buyer_needs_product ()
	//===============================================================R=E=F=A=C=T=O=R=I=Z=A=T=I=ON===================================================\\
	function check_seller_has_product ($seller_node){
		//	returns TRUE if seller has a positive amount of product to sell (least amount is 0 -> returns FALSE)
		return ($seller_node['quantity'] > 0);
	}

	function check_buyer_needs_product ($product){
		//	returns TRUE if buyer still needs a quantity of product to buy (least amount is 0 -> returns FALSE)
		return ($product [1] > 0);
	}

	function get_initial_cost_ppc ($product, $con){
		global $products;
		
		$products = checkProductsGlobalVariable ($con);
		
		foreach ($products as $temp) {
			if($temp["name"] == $product[0]) {
			//	returns the value (float) of ONE instance of the product the buyer needs
				return $temp["base_cost"];
			}
		}
	}

	function get_final_cost_ppc ($inter_nodes, $product, $seller_node, $initial_cost_ppc){
		//	returns the value (float) of ONE instance of product after it has passed through all intermediary nodes
		$price = $initial_cost_ppc;

		foreach ($inter_nodes as $intermediary) {
			if ($intermediary !== $seller_node['id'] && $intermediary !== $product[0])
				$price = calculateNewPrice ($price);
		}

		return $price;
	}

	function get_final_purchase_amount ($buyer_node, $product, $seller_node, $final_cost_ppc){
		//	returns the amount (int) of product the buyer can afford to buy from seller at the final per-piece cost
		$affordable_amount = (int) ($buyer_node ['money'] / $final_cost_ppc);

		if ($affordable_amount > $product [1])
			$affordable_amount = $product [1];

		if ($affordable_amount <= $seller_node ['quantity'])
			return $affordable_amount;

		return $seller_node ['quantity'];
	}

	function get_final_cost_whole ($final_purchase_amount, $final_cost_ppc){
		//	returns the value (float) of the ENTIRE settled amount to buy
		return $final_purchase_amount * $final_cost_ppc;
	}

	function complete_purchase (&$inter_nodes, &$buyer_node, &$seller_node, $con){
		global $nodes;

		//	complete tranzaction ; after tranzaction is completed, each intermediary node involved in the transaction receives its share of the total profit
		$products = $buyer_node['requests'];
		$products = unserialize_requests($products);

		foreach ($products as &$product) {
			$initial_cost_ppc 		= get_initial_cost_ppc ($product, $con);
			$final_cost_ppc 		= get_final_cost_ppc ($inter_nodes, $product, $seller_node, $initial_cost_ppc);
			$final_purchase_amount 	= get_final_purchase_amount ($buyer_node, $product, $seller_node, $final_cost_ppc);
			$final_cost_whole 		= get_final_cost_whole ($final_purchase_amount, $final_cost_ppc);
			$result 				= ''; 

			$product [1]			-= $final_purchase_amount;
			$buyer_node ['money'] 	-= $final_cost_whole;

			for ($i=0; $i < sizeof($products); $i++) { 
				$result .= implode('|', $products[$i])."^";
			}

			$buyer_node ['requests'] = substr($result, 0, -1);

			foreach (array_reverse($inter_nodes) as $intermediary){
				// each intermediary node involved in the transaction will receive 10% of the final cost
				if ($intermediary !== $seller_node['id'] && $intermediary !== $product[0]) {
					$inter_node 			= $nodes[$intermediary];
					$interm_profit 			= ($final_cost_whole / 11);
					$inter_node['money']	+= $interm_profit;
					$final_cost_whole 		-= $interm_profit;
				}
			}

			$seller_node ['quantity']   -= $final_purchase_amount;
			$seller_node ['money']		+= $final_cost_whole;
		}
	}
	//===========================================================================E=N=D==============================================================\\


	//===================================================================================================
	//            B F S   S E A R C H   A L G O R I T H M
	//===================================================================================================

	//	update database with new values of product counts and moneys
	function update_post_tranzaction ($con, $nodes){
		foreach($nodes as $nd){
			execute_sql_and_return('<simulator.php>', $con, "UPDATE nodes SET quantity = ".$nd['quantity'].", money = ".$nd['money']." WHERE id = '" . $nd['id']. "'");
		}
	}

	// function to calculate the new price of a product, factoring in the old price + individual profit
	function calculateNewPrice ($old_price) {
		// constant profit expressed in percentage (%) of $old_price
		$personal_profit = 10;
		return $old_price + (($personal_profit / 100) * $old_price);
	}

	// function to update money for each node
	function payDay ($con, $nodes) {
		addToLog (";--------------------------------\n;        PAYDAY\n;--------------------------------");

		foreach ($nodes as $node) {
			$node['money'] += frand (20);
		}

	}


	// implementation of BREADTH-FIRST-SEARCH algorithm for unweighted graphs
	// &$Q -> global variabl
	//	refactorization of code to make code easier to manipulate
	//	functions to add : 	check_seller_has_product (); checke reprezenting the heap containing parent nodes, indexed by child nodes_buyer_affords_product (); get_final_purchase_amount (); get_final_purchase_cost_ppc ();
	//						intermediate_profit_get ();
	// $Q structure : $Q[$child_node] -> $parent_node

	function BFS ($source, $end) {
		global $Q;
		global $nodes;

		// heap reset ; the value for each node, except $source, will have its parent set to 0 (for testing)		
		for ($i = 0; $i < sizeof($nodes); $i++){
			$Q[$i] = -1;
		}
		
		//	the $source node will always have its parent set to itself ; this is the condition for ending the path search
		$Q[$source] = $source;

		// step 1 : populate the heap according to BFS algorithm
		BFS_populate_heap($source, $end);

		// step 2 : verify the heap to reconstruct the path
		$result = BFS_get_path($source, $end);

		return $result;
	}

	//	function to populate the heap according to BFS
	//	$Q 				-> global heap
	//	$nodes 			-> hash table containing all the nodes of the graph
	//	$node_start 	-> list of nodes for which in the current iteration we search for neighbours
	//	$end 			-> the target node for which we are look for the path ; used for recursion loop exit
	function BFS_populate_heap($node_start) {
		global $Q;
		global $nodes;
		
		//condition for stopping population of heap is a full heap

		$node_start = explode(',', $node_start);
		//initialize starting nodes for next iteration
		$node_next = '';

		//iterate through starting nodes
		//for each get closest neighbours and set their heap values
		foreach ($node_start as $current){
			//get links of current node
			$links = $nodes[$current]['links'];
			$links = explode(',', $links);

			//iterate through current node's links
			//if link hasn't been checked before, its value is set in the heap
			foreach ($links as $link){
				if ($Q[$link] < 0) {
					$Q[$link] = $current;
					$node_next .= $link . ',';
				}
			}
		}

		//get rid of trailing comma
		$node_next = trim($node_next, ',');

		//call function for next set of starting nodes
		if (BFS_check_heap() === FALSE){
			BFS_populate_heap($node_next);
		}
	}

	//	function to return the shortest path from $start to $end
	function BFS_get_path ($start, $end) {
		global $Q;

		//	if $end's parent is itself, return $end ; this means we've reached the source node as it is the only one with this property
		if ($Q[$end] === $start)
			return $end;

		//	get $end's parent from the heap
		$p_node = $Q[$end];

		//	recursive call of function using $end's parent
		$parent = BFS_get_path($start, $p_node);

		return $parent . '->' . $end;
	}

	function BFS_check_heap(){
		global $Q;

		for ($i = 0; $i < sizeof($Q); $i++){
			if ($Q[$i] < 0) return FALSE;
		}

		return TRUE;
	}

	//===================================================================================================
	//            E N D   O F   B F S   S E A R C H   A L G O R I T H M
	//===================================================================================================

	function getProvidersOf($product_name){
		global $nodes;

		$rez = '';
		for ($i = 0; $i < sizeof($nodes); $i++){
			if ($nodes[$i]['serves'] === $product_name)
				$rez .= $i . ',';
		}

		$rez = trim($rez, ',');
		$rez = explode(',', $rez);

		return $rez;
	}

	function getProviders ($buyer_node){
		global $nodes;

		//returns a list of other nodes which sell the products buyer_node requests
		//the list is ordered by the priority of the request (most required -> least required)
		$requests = $nodes[$buyer_node]['requests'];
		$requests = explode('^', $requests);

		if ($requests[0] === '') return '';

		$max_priority = 0;

		foreach ($requests as &$request) {
			$request = explode('|', $request);

			if ($request[2] > $max_priority)
				$max_priority = $request[2];
		}

		$rez = [];

		while ($max_priority >= 0) {
			foreach ($requests as $request) {
				if ($request[2] === $max_priority)
					$rez = array_merge ($rez, getProvidersOf($request[0]));
			}

			$max_priority--;
		}

		return $rez;
	}
