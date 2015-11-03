<?php
	// include files
	include_once ('sql_execute.php');

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
	prepareSim (1, $con);

	// close connection
	$con->close ();

	// ----------------------------------------------------------------------------------------------------------

	$Q = []; //global declaration of heap

	// function to set iteration number of the 
	function prepareSim ($exec_count, $con) {
		for ($exec_iterator = 0; $exec_iterator < $exec_count; $exec_iterator++){
			startSim ($con);
		}
	}

	// function to start the simulator iterating over the nodes in the graph
	function startSim ($con) {
		consumerPhase ($con);
		economicPhase ($con);
	}

	// function to treat information related
	function consumerPhase ($con) {
		$possibleBuyers = [];
		$nodes 			= execute_sql_and_return ('<simulator.php>', $con, "SELECT * FROM nodes");
		
		while ($row = mysqli_fetch_assoc ($nodes)) {
			// set producer to "true"
			$row['is_producer'] = 1;

			// get a list of possible buyers
			// will return an array, where each index is the name of the node, and the value is a list of nodes representing the neighbours
			$buyers = execute_sql_and_return ('<simulator.php>', $con, "SELECT name, link_to FROM nodes WHERE (needs_product = '".$row['has_product']."') AND (name <> '".$row['name']."')");
			
			// find path to each potential buyer and return array containing them

			// using the returned vector, get the shortest paths from $row['name'] (producer) to each node (consumer)
			// returned variable contains a list of nodes, where the parent node is $row['name']
			$path_list = getShortestPath($con, $row, $nodes, $buyers);

			// set producer to "false"
			$row['is_producer'] = 0;

		}
	}

	// function to return a list of shortest pathds to the respective $row['name'] node (which is the seller)
	function getShortestPath ($con, $row, $nodes, $buyers) {
		$list = [];

		// for each potential buyer, find the shortest path and add it to the list
		while ($consumerNode = mysqli_fetch_assoc ($buyers)){
			$my_res = BFS ($Q, $nodes, $row['name'], $consumerNode['name']);
			$list 	= array_merge($list, $my_res);
		}

		return $list;
	}

	// function to treat economic phase
	function economicPhase ($con) {
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
	function addToLog ($content) {
		$file = fopen ('../../data/log.txt', 'a+');

		fwrite ($file, $content);
		fclose ($file);
	}

	// implementation of BREADTH-FIRST-SEARCH algorithm for unweighted graphs
	//	&$Q -> global variable reprezenting the heap containing parent nodes, indexed by child nodes
	//	$Q structure : $Q[$child_node] -> $parent_node
	//	function will determin shortest path from $source to $end and return a string cointaining the path
	function BFS (&$Q, $nodes, $source, $end) {
		// heap resetting ; the value for each node, except $source, will have its parent set to 0 (for testing)
		$Q = array();

		foreach ($nodes as $node){
			$Q[$node['name']] = 0;
		}

		//	the $source node will always have its parent set to itself ; this is the condition for ending the path search
		$Q[$source] = $source;

		// step 1 : populate the heap according to BFS algorithm
		BFS_populate_heap ($Q, $nodes, $source, $end);

		// TODO: check if this is needed
		addToLog ("\n#" . implode(",", $Q));

		// step 2 : verify the heap to reconstruct the path
		$result = BFS_get_path($Q, $source, $end);

		return $result;
	}

	//	function to populate the heap according to BFS
	//	$Q 				-> global heap
	//	$nodes 			-> hash table containing all the nodes of the graph
	//	$start_nodes 	-> list of nodes for which in the current iteration we search for neighbours
	//	$end 			-> the target node for which we are look for the path ; used for recursion loop exit
	function BFS_populate_heap (&$Q, $nodes, $start_nodes, $end) {
		//	test if the heap is full; this will ensure that for the same $source node, the function isn't recalled unnecessarily
		if (!BFS_check_full_heap ($Q)) {
			// turn the starting node list into an array for iteration
			$starts = explode(",", $start_nodes);

			//	prepare the next set of starting nodes (this will consist of a list of the unvisited neighbours of this set of starting nodes)
			$start_next = "";

			//	for every starting node, we search through all neighbours
			foreach ($starts as $st_node) {
				//	find the node adjacent to the name of this starting node
				$st_node_neighbours = nodeOf ($st_node, $nodes);

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
				//	iterate through these neighbours
				foreach ($st_node_neighbours as $snn) {
					//	set the parent of this neighbour to $st_node
					$Q[$snn] = $st_node;

					//	add this neighbour to the next set of starting nodes
					$start_next = $start_next . ',' . $snn;
				}
			}

			//	due to concatenation with the starting value of $start_next which was "", an extra ',' is present in the list -> correction
			$start_next = substr ($start_next, 1);

			//	recursive call of function on the next set of starting nodes (the neighbours that were visited this iteration)
			BFS_populate_heap ($Q, $nodes, $start_next, $end);
		}
	}

	//	function to return the shortest path from $start to $end
	function BFS_get_path($Q, $start, $end) {
		//	if $end's parent is itself, return $end ; this means we've reached the source node as it is the only one with this property
		if ($Q[$end] === $end) {
			return $end;
		}

		//	get $end's parent from the heap
		$p_node = $Q[$end];

		//	recursive call of function using $end's parent
		$parent = BFS_get_path ($Q, $start, $p_node);

		return $parent . ',' . $end;
	}

	//	function to return the node corresponding to the name of $node_name
	function nodeOf ($node_name, $nodes) {
		foreach ($nodes as $nd) {
			if ($node_name === $nd['name']) {
				return $nd;
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