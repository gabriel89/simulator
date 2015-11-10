<?php
	//global declaration of heap
	$Q = []; 

	function getConsumerPath ($con, $nodes) {
		addToLog ("\n\n\n;--------------------------------\n;        ESTABLISHING TRANSACTION PATH\n;--------------------------------");

		$consumer_path 	= [];
		$nodes_array 	= [];

		foreach ($nodes as $nd){
			$nodes_array = array_merge ($nodes_array, [$nd]);
		}

		foreach ($nodes as $node) {
			$Q = [];
			$list 			= [];
			$buyers_array 	= [];
			$buyers 		= execute_sql_and_return ('<simulator.php>', $con, "SELECT name FROM nodes WHERE (needs_product = '".$node['has_product']."') AND (name <> '".$node['name']."')");

			foreach ($buyers as $nd){
				$buyers_array = array_merge ($buyers_array, [$nd]);
			}

			foreach ($buyers_array as $buyer){
				$list = array_merge ($list, array (BFS ($Q, $nodes_array, $node['name'], $buyer['name'])));
			}
			
			$consumer_path = array_merge ($consumer_path, $list);

			addToLog ("\n\n" . implode ("\n", str_replace (',', ' -> ' , $list)));
		}

		// unset large arrays that are not used anymore
		unset ($nodes_array);

		return $consumer_path;
	}

	// function to finalize transaction:
	// for each element of $consumer_path we need to sell the product from the producer (first node) to the consumer (last node)
	// each intermediate node will add a personal profit to the total value of the previous node, thus product final price = initial price + profit node1 + profit node2 + ...
	function finalizeTransaction ($con, $nodes, $consumer_path) {
		addToLog ("\n\n\n;--------------------------------\n;        FINALIZING TRANSACTIONS\n;--------------------------------\n\n");

		$nodes_array 		= [];
		$last_sold_out_msg 	= '';

		foreach ($nodes as $nd) {
			$nodes_array = array_merge ($nodes_array, [$nd]);
		}
		
		// iterate through each set of paths | $consumer_path contains the set of every path to the potential buyers of each node
		foreach ($consumer_path as $current_path) {

			// retrieve the names of the nodes involved in this transaction | the first node is the seller | the last node is the buyer
			$inter_nodes = explode (',', $current_path);

			// retrieve seller and buyer nodes from path
			$seller_node = &$nodes_array[indexTo ($inter_nodes[0], $nodes_array)];	
			
			//	check if the seller_node still has product to sell
			if (check_seller_has_product ($seller_node)) {

				$rev 		= array_reverse ($inter_nodes);
				$buyer_node = &$nodes_array[indexTo ($rev[0], $nodes_array)];

				unset ($rev);
				//	check if buyer still needs to purchase products
				if (check_buyer_needs_product ($buyer_node)) {
					// finalize transaction
					complete_purchase ($inter_nodes, $buyer_node, $seller_node, $nodes_array, $con);
				}
			}
		}

		//	update database with new values for money and product quantities
		update_post_tranzaction ($con, $nodes_array);
	}


	//	refactorization of code to make code easier to manipulate
	//	functions to add : 	check_seller_has_product (); check_buyer_affords_product (); get_final_purchase_amount (); get_final_purchase_cost_ppc ();
	//						intermediate_profit_get (); check_buyer_needs_product ()
	//===============================================================R=E=F=A=C=T=O=R=I=Z=A=T=I=ON===================================================\\
	function check_seller_has_product ($seller_node){
		//	returns TRUE if seller has a positive amount of product to sell (least amount is 0 -> returns FALSE)
		if ($seller_node ['has_product_count'] > 0) return TRUE;

		return FALSE;
	}

	function check_buyer_needs_product ($buyer_node){
		//	returns TRUE if buyer still needs a quantity of product to buy (least amount is 0 -> returns FALSE)
		if ($buyer_node ['needs_product_count'] > 0) return TRUE;

		return FALSE;
	}

	function get_initial_cost_ppc ($buyer_node, $con){
		//	returns the value (float) of ONE instance of the product the buyer needs
		$product = execute_sql_and_return ('<simulator.php>', $con, "SELECT name, value FROM products WHERE name = '" . $buyer_node['needs_product'] . "'");
		$product = mysqli_fetch_array($product);

		return $product ['value'];
	}

	function get_final_cost_ppc ($inter_nodes, $buyer_node, $seller_node, $initial_cost_ppc){
		//	returns the value (float) of ONE instance of product after it has passed through all intermediary nodes
		$price = $initial_cost_ppc;

		foreach ($inter_nodes as $intermediary) {
			if ($intermediary !== $seller_node['name'] && $intermediary !== $buyer_node['name']) {
				$price = calculateNewPrice ($price);
			}
		}

		return $price;
	}

	function get_final_purchase_amount ($buyer_node, $seller_node, $final_cost_ppc){
		//	returns the amount (int) of product the buyer can afford to buy from seller at the final per-piece cost
		$affordable_amount = (int) ($buyer_node ['money'] / $final_cost_ppc);

		if ($affordable_amount > $buyer_node ['needs_product_count']){
			$affordable_amount = $buyer_node ['needs_product_count'];
		}

		if ($affordable_amount <= $seller_node ['has_product_count']){
			return $affordable_amount;
		}

		return $seller_node ['has_product_count'];
	}

	function get_final_cost_whole ($final_purchase_amount, $final_cost_ppc){
		//	returns the value (float) of the ENTIRE settled amount to buy
		return $final_purchase_amount * $final_cost_ppc;
	}

	function complete_purchase (&$inter_nodes, &$buyer_node, &$seller_node, &$nodes_array, $con){
		//	complete tranzaction ; after tranzaction is completed, each intermediary node involved in the transaction receives its share of the total profit
		$initial_cost_ppc 		= get_initial_cost_ppc ($buyer_node, $con);
		$final_cost_ppc 		= get_final_cost_ppc ($inter_nodes, $buyer_node, $seller_node, $initial_cost_ppc);
		$final_purchase_amount 	= get_final_purchase_amount ($buyer_node, $seller_node, $final_cost_ppc);
		$final_cost_whole 		= get_final_cost_whole ($final_purchase_amount, $final_cost_ppc);

		$buyer_node ['needs_product_count']	-= $final_purchase_amount;
		$buyer_node ['money']				-= $final_cost_whole;

		foreach ($inter_nodes as $intermediary){
			// each intermediary node involved in the transaction will receive 10% of the final cost
			if ($intermediary !== $seller_node['name'] && $intermediary !== $buyer_node['name']) {
				$inter_node 			= &$nodes_array[indexTo ($intermediary, $nodes_array)];
				$interm_profit 			= ($final_cost / 11);
				$inter_node['money']	+= $interm_profit;
				$final_cost_whole 		-= $interm_profit;
			}
		}

		$seller_node ['has_product_count']	-= $final_purchase_amount;
		$seller_node ['money']				+= $final_cost_whole;
	}
	//===========================================================================E=N=D==============================================================\\

	//	update database with new values of product counts and moneys
	function update_post_tranzaction ($con, $nodes_array){
		foreach($nodes_array as $nd){
			execute_sql_and_return('<simulator.php>', $con, "UPDATE nodes SET has_product_count = ".$nd['has_product_count'].", needs_product_count = ".$nd['needs_product_count'].", money = ".$nd['money']." WHERE name = '" . $nd['name']. "'");
		}
	}
	
	// implementation of BREADTH-FIRST-SEARCH algorithm for unweighted graphs
	// &$Q -> global variabl
	//	refactorization of code to make code easier to manipulate
	//	functions to add : 	check_seller_has_product (); checke reprezenting the heap containing parent nodes, indexed by child nodes_buyer_affords_product (); get_final_purchase_amount (); get_final_purchase_cost_ppc ();
	//						intermediate_profit_get ();
	// $Q structure : $Q[$child_node] -> $parent_node

	function BFS (&$Q, $nodes, $source, $end) {
		// heap resetting ; the value for each node, except $source, will have its parent set to 0 (for testing)
		$nodes_array = [];
		
		foreach ($nodes as $nd){
			$Q[$nd['name']] = 0;
			$nodes_array 	= array_merge ($nodes_array, [$nd]);
		}

		//	for all isolated nodes, initialize heap value with itself
		foreach ($nodes_array as $nd){
			if ($nd['link_to'] === "" || $nd['link_to'] === NULL){
				$Q[$nd['name']] = $nd['name'];
			}
		}
		
		//	the $source node will always have its parent set to itself ; this is the condition for ending the path search
		$Q[$source] = $source;

		// step 1 : populate the heap according to BFS algorithm
		BFS_populate_heap($Q, $nodes_array, $source, $end);

		// step 2 : verify the heap to reconstruct the path
		$result = BFS_get_path($Q, $source, $end);

		return $result;
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
			execute_sql ('<simulator.php>', $con, "UPDATE nodes SET money = '".($node['money'] + frand (20))."' WHERE id = '".$node['id']."'");
		}

	}

	//	function to populate the heap according to BFS
	//	$Q 				-> global heap
	//	$nodes 			-> hash table containing all the nodes of the graph
	//	$start_nodes 	-> list of nodes for which in the current iteration we search for neighbours
	//	$end 			-> the target node for which we are look for the path ; used for recursion loop exit
	function BFS_populate_heap(&$Q, $nodes, $start_nodes, $end) {
		//	test if the heap is full; this will ensure that for the same $source node, the function isn't recalled unnecessarily
		if (!BFS_check_full_heap($Q)) {
			// turn the starting node list into an array for iteration
			$starts = explode(',', $start_nodes);

			//	prepare the next set of starting nodes (this will consist of a list of the unvisited neighbours of this set of starting nodes)
			$start_next = '';

			//	for every starting node, we search through all neighbours
			foreach($starts as $st_node){

				//	find the node adjacent to the name of this starting node
				$st_node_neighbours = $nodes[indexTo ($st_node, $nodes)];

				//	retrieve the list of its neighbours
				$st_node_neighbours = $st_node_neighbours['link_to'];

				//	transform the list into array for iteration
				$s_n_n = explode(",", $st_node_neighbours);
				$st_node_neighbours = [];

				//	filter out previously visited nodes
				foreach ($s_n_n as $snn) {
					if ($Q[$snn] === 0) {
						$st_node_neighbours = array_merge($st_node_neighbours, [$snn]);
					}
				}

				// at this point, $st_node_neighbours contains all the neighbours of $st_node that HAVE NOT BEEN visited before
				// iterate through these neighbours
				foreach ($st_node_neighbours as $snn) {
					//	set the parent of this neighbour to $st_node
					$Q[$snn] = $st_node;

					//	add this neighbour to the next set of starting nodes
					$start_next = $start_next . ',' . $snn;
				}
			}

			// due to concatenation with the starting value of $start_next which was "", an extra ',' is present in the list -> correction
			$start_next = substr ($start_next, 1);

			// recursive call of function on the next set of starting nodes (the neighbours that were visited this iteration)
			BFS_populate_heap($Q, $nodes, $start_next, $end);
		}
	}

	//	function to return the shortest path from $start to $end
	function BFS_get_path ($Q, $start, $end) {
		//	if $end's parent is itself, return $end ; this means we've reached the source node as it is the only one with this property
		if ($Q[$end] === $end) {
			return $end;
		}

		//	get $end's parent from the heap
		$p_node = $Q[$end];

		//	recursive call of function using $end's parent
		$parent = BFS_get_path($Q, $start, $p_node);

		return $parent . "," . $end;
	}

	function indexTo ($node_name, $nodes) {
		$max_size = sizeof ($nodes);
		for ($i = 0; $i < $max_size; $i++){
			$nd = &$nodes[$i];
			if ($node_name === $nd['name']) {
				return $i;
			}
		}
	}

	function BFS_check_full_heap ($Q) {
		foreach ($Q as $my_q) {
			if ($my_q === 0)
				return FALSE;
		}

		return TRUE;
	}