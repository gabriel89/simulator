<?php
	// include files
	include_once ('sql_execute.php');
	include_once ('common_functions.php');

	// connect to DB
	$servername = "localhost";
	$username 	= "sim";
	$password 	= "sim";
	$dbname 	= "sim";

	// Create connection
	$con = new mysqli ($servername, $username, $password, $dbname);

	// Check connection
	if ($con->connect_error) {
	    die ("Connection failed: " . $conn->connect_error);
	}

	// start simulator by setting the execution number
	prepareSim (10, $con);

	// close connection
	$con->close ();

	// ----------------------------------------------------------------------------------------------------------

	$Q = []; //global declaration of heap

	// function to set iteration number of the 
	function prepareSim ($exec_count, $con) {
		addToLog ("\n;--------------------------------\n;        STARTING SIMULATOR\n;--------------------------------\n");

		// calculate pay-day recurrence
		$pay_day = floor ($exec_count / 12);

		for ($exec_iterator = 1; $exec_iterator <= $exec_count; $exec_iterator++) {
			// retrieve all nodes from the DB
			$nodes = execute_sql_and_return ('<simulator.php>', $con, "SELECT * FROM nodes");

			// make sure we refresh the money only in 1/12th of the total number of iteration
			if (($pay_day > ($exec_count / 12)) && ($exec_iterator % $pay_day == 0)) {
			    payDay ($con, $nodes);
			}
			
			startSim ($con, $nodes, $exec_iterator, $exec_count);
		}
	}

	// function to start the simulator iterating over the nodes in the graph
	function startSim ($con, $nodes, $exec_iterator, $exec_count) {
		addToLog ("\n\n;--------------------------------\n;        SIMULATOR PHASE $exec_iterator/$exec_count\n;--------------------------------");
		print_r ("On phase $exec_iterator/$exec_count\n");

		consumerPhase ($con, $nodes);
		economicPhase ($con, $nodes);
	}

	// function to treat information related to the consumer phase
	function consumerPhase ($con, $nodes) {
		// retrieve a list of lists representing an economic path from producer to consumer
		$consumer_path = getConsumerPath ($con, $nodes);

		// make transaction for each possible consumer
		finalizeTransaction ($con, $nodes, $consumer_path);

		// unset $consumer_path array once it is not used anymore
		unset ($consumer_path);
	}

	// function to return a list of shortest pathds to the respective $row['name'] node (which is the seller)
	function getShortestPath ($con, $row, $nodes, $buyers) {
		$list = [];

		//	for each potential buyer, find the shortest path and add it to the list
		while ($consumerNode = mysqli_fetch_assoc ($buyers)){
			$my_res = BFS ($Q, $nodes, $row['name'], $consumerNode['name']);
		}

		$list = array_merge ($list, [$my_res]);

		return $list;
	}

	// function to treat economic phase
	function economicPhase ($con, $nodes) {
		updateRevenue ();

		decideUponInvestment ();
	}

	function updateRevenue () {

	}

	// function to decide upon investment options
	function decideUponInvestment () {
		investInLink ();

		investInProduction ();
	}

	// function to set attributes in order to add a new link to the graph
	function investInLink () {
	}

	// function set attributes in order to improve production
	function investInProduction () {
	}

	// function to write to log important events on every cycle
	function addToLog ($content){
		$file = fopen ('../../data/log.txt', 'a+');

		fwrite ($file, $content);
		fclose ($file);
	}

	function getConsumerPath ($con, $nodes) {
		$consumer_path 	= [];
		$nodes_array 	= [];

		while ($nodes_array[] = mysqli_fetch_assoc ($nodes));

		array_pop ($nodes_array);

		reset ($nodes_array);

		foreach ($nodes as $node) {
			$Q = [];
			$list 			= [];
			$buyers_array 	= [];
			$buyers 		= execute_sql_and_return ('<simulator.php>', $con, "SELECT name FROM nodes WHERE (needs_product = '".$node['has_product']."') AND (name <> '".$node['name']."')");

			while ($buyers_array[] = mysqli_fetch_assoc ($buyers));

			array_pop ($buyers_array);

			reset ($buyers_array);

			foreach ($buyers_array as $buyer){
				$list = array_merge ($list, array (BFS ($Q, $nodes_array, $node['name'], $buyer['name'])));
			}
			
			$consumer_path = array_merge ($consumer_path, $list);

			addToLog("\n\n" . implode("\n", $list));
		}

		// unset large arrays that are not used anymore
		unset ($nodes_array);

		return $consumer_path;
	}

	// function to finalize transaction:
	// for each element of $consumer_path we need to sell the product from the producer (first node) to the consumer (last node)
	// each intermediate node will add a personal profit to the total value of the previous node, thus product final price = initial price + profit node1 + profit node2 + ...
	function finalizeTransaction ($con, &$nodes, $consumer_path) {
		// hard-coded the initial price, but this needs to be retrieved from each producer dynamically
		$price 				= 100;
		$nodes_array = [];
		foreach($nodes as $nd){
			$nodes_array = array_merge ($nodes_array, [$nd]);
		}
		//	iterate through each set of paths | $consumer_path contains the set of every path to the potential buyers of each node
		
		// retrieve path set for current node and execute transaction accordingly
		foreach($consumer_path as $current_path){

			$transaction_log 	= "";

			// retrieve the names of the nodes involved in this transaction | the first node is the seller | the last node is the buyer
			$path_nodes = explode(",", $current_path);

			// retrieve seller and buyer nodes from path
			$seller_index = indexTo ($path_nodes[0], $nodes_array);	
			
			
			//	check if the seller_node still has product to sell
			if ($nodes_array[$seller_index]['has_product_count'] > 0) {
				$buyer_index = indexTo ($path_nodes[0], $nodes_array);

				if($nodes_array[$buyer_index]['needs_product_count'] == 0){
					// this buyer node no longer needs products
					$transaction_log .= $nodes_array[$buyer_index]['name'] . " no longer needs any products\n\n";
				} else {
					// retrieve product the seller_node wants to sell
					$product = execute_sql_and_return ('<simulator.php>', $con, "SELECT name, value FROM products WHERE name = '" . $nodes_array[$seller_index]['has_product'] . "'");
					$product = mysqli_fetch_array($product);


					$transaction_log .= $nodes_array[$seller_index]['name'] . " wants to sells to " . $nodes_array[$buyer_index]['name'] . "\n";
					//	set initial transaction cost 
					$transaction_cost_piece = $product['value'];

					$transaction_log .= $nodes_array[$seller_index]['name'] . " sets initial piece cost to " . $transaction_cost_piece . "\n";

					//	determine final transaction cost
					foreach ($path_nodes as $intermediary){

						//	cost will be increased by 0.1 for each intermediary node involved in the transaction
						if($intermediary !== $nodes_array[$seller_index]['name'] && $intermediary !== $nodes_array[$buyer_index]['name']){
							$profit_interm = $transaction_cost_piece * 0.1;
							$transaction_cost_piece = $transaction_cost_piece + $profit_interm;

							$transaction_log .= $intermediary . " mediates transaction, raising piece cost to " . $transaction_cost_piece . "\n";
						}
					}

					//	at this point, the final transaction cost per piece is known and the buyer node will buy as much product as it can without going broke
					// the maximum quantity buyer_node can afford without going broke
					$max_affordable_quantity = floor ($nodes_array[$buyer_index]['money'] / $transaction_cost_piece);
					$desired_amount = $nodes_array[$buyer_index]['needs_product_count'] - $max_affordable_quantity;
					
					if($desired_amount < 0){
						//	if buyer needs less than he can afford, try to buy it all
						$desired_amount = $nodes_array[$buyer_index]['needs_product_count'];
					} else {
						//	if buyer needs more than he can afford, try to buy as much as he can
						$desired_amount = $max_affordable_quantity;
					}

					// difference between amount the seller has and amount the buyer wishes to buy
					$amount_to_buy = $nodes_array[$seller_index]['has_product_count'] - $desired_amount;
					
					if($amount_to_buy < 0) {
						// if $amount_remaining is negative, then the seller_node has less product than buyer needs, so he sells out
						$amount_to_buy = $nodes_array[$seller_index]['has_product_count'];
					} else {
						// if $amount_remaining is positive or 0, then the seller has at least the amount the buyer wants to buy
						$amount_to_buy = $desired_amount;
					}

					//	execute transaction : 	seller_node sells the quantity and receives the money, as does every intermediary node
					//							buyer_node receives the quantity and spends the money
					$final_cost = $amount_to_buy * $transaction_cost_piece;
					$nodes_array[$buyer_index]['money'] -= $final_cost;
					$nodes_array[$buyer_index]['needs_product_count'] -= $amount_to_buy;

					$transaction_log .= $nodes_array[$buyer_index]['name'] . " buys " . $amount_to_buy . " products from " . $nodes_array[$seller_index]['name'] . " for a total cost of " . $final_cost . "\n";

					//	foreach intermediary node we need to calculate its cut of the total profit (which is 0.1 * the profit of the previous intermediary node)
					$path_nodes = array_reverse ($path_nodes);
					foreach ($path_nodes as $intermediary){

						//	each intermediary node involved in the transaction will receive 10% of the final cost
						if($intermediary !== $nodes_array[$seller_index]['name'] && $intermediary !== $nodes_array[$buyer_index]['name']){
							$inter_index = indexTo ($intermediary, $nodes_array);
							$interm_profit = ($final_cost / 11);
							$nodes_array[$inter_index]['money'] += $interm_profit;
							$final_cost -= $interm_profit;

							$transaction_log .= $intermediary . " receives sum of " . $interm_profit . " for mediation\n";
						}
					}

					$nodes_array[$seller_index]['money'] += $final_cost;
					$nodes_array[$seller_index]['has_product_count'] -= $amount_to_buy; 

					$transaction_log .= $nodes_array[$seller_index]['name'] . " receives final payment of " . $final_cost . "\n\n";
				}
			} else $transaction_log .= $nodes_array[$seller_index]['name'] . " has sold out\n\n";

			//print_r($transaction_log);
		}

		//	update database with new values for money and product quantities
		foreach($nodes_array as $nd){
			execute_sql_and_return('<simulator.php>', $con, "UPDATE nodes SET has_product_count = ".$nd['has_product_count'].", needs_product_count = ".$nd['needs_product_count'].", money = ".$nd['money']." WHERE name = '" . $nd['name']. "'");
		}
		//print_r("Updated database\n\n");
		
		$price = calculateNewPrice ($price);
	}

	// implementation of BREADTH-FIRST-SEARCH algorithm for unweighted graphs
	// &$Q -> global variable reprezenting the heap containing parent nodes, indexed by child nodes
	// $Q structure : $Q[$child_node] -> $parent_node
	// function will determin shortest path from $source to $end and return a string cointaining the path
	function BFS (&$Q, $nodes, $source, $end) {
		// heap resetting ; the value for each node, except $source, will have its parent set to 0 (for testing)
		foreach ($nodes as $nd){
			$Q[$nd['name']] = 0;
		}

		//	the $source node will always have its parent set to itself ; this is the condition for ending the path search
		$Q[$source] = $source;

		// step 1 : populate the heap according to BFS algorithm
		BFS_populate_heap($Q, $nodes, $source, $end);

		// step 2 : verify the heap to reconstruct the path
		$result = BFS_get_path($Q, $source, $end);

		return $result;
	}

	// function to calculate the new price of a product, factoring in the old price + individual profit
	function calculateNewPrice ($old_price) {
		// constant profit expressed in percentage (%) of $old_price
		$personal_profit = 10;

		return ($personal_profit / 100) * $old_price;
	}

	// function to update money for each node
	function payDay ($con, $nodes) {
		addToLog ("\n\n;--------------------------------\n;        PAYDAY\n;--------------------------------");

		while ($node = mysqli_fetch_assoc ($nodes)) {
			execute_sql ('<simulator.php>', $con, "UPDATE nodes SET money = '".($node['money'] + frand (10))."' WHERE id = '".$node['id']."'");
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
				$st_node_neighbours = nodeOf($st_node, $nodes);

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

	//	function to return the node corresponding to the name of $node_name
	function nodeOf ($node_name, $nodes) {
		foreach ($nodes as $nd) {
			if ($node_name === $nd['name']) {
				return $nd;
			}
		}
	}

	function indexTo ($node_name, $nodes) {
		$max_size = sizeof ($nodes);
		for ($i = 0; $i < $max_size; $i++){
			if ($node_name === $nodes[$i]['name']) {
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
