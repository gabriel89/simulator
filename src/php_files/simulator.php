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
			while ($buy = mysqli_fetch_assoc ($buyers)) {
				$possibleBuyers[$buy['name']] = $buy['link_to'];
			}

			// testing the result, optional
			//rint_r($possibleBuyers);

			// using the returned vector, get the shortest paths
			// must contain a list of nodes, where the parent node is $row['name']
			// $shortestPaths = getShortestPath ($con, $row, $nodes, $possibleBuyers);

			// testing the result, optional
			//print_r($shortestPaths);

			// set producer to "false"
			$row['is_producer'] = 0;

		}
		$my_r = getShortestPath ($con, $row, $nodes, $possibleBuyers);
	}

	// function to return a list of shortest pathds to the respective $row['name'] node (which is the seller)
	function getShortestPath ($con, $row, $nodes, $buyers) {
		$list = [];
		// http://codereview.stackexchange.com/questions/75641/dijkstras-algorithm-in-php
		// http://stackoverflow.com/questions/6598791/how-to-optimize-dijkstra-code-in-php
		// http://stackoverflow.com/questions/4867716/more-than-640-000-elements-in-the-array-memory-problem-dijkstra
		// https://en.wikipedia.org/wiki/Dijkstra's_algorithm
		// https://github.com/phpmasterdotcom/DataStructuresForPHPDevs/blob/master/Graphs/graph-dijkstra.php
		// http://odino.org/the-shortest-path-problem-in-php-demystifying-dijkstra-s-algorithm/
		$visited =array();
		foreach($nodes as $nd){
			$visited[$nd['name']] = 0;
		}
		$myres = updatePath ("n0", $nodes, nodeOf("n0", $nodes), nodeOf("n19", $nodes), nodeOf("x", $nodes), "n0", $visited);
		$file = fopen("../../data/log.txt", "a");
		$myres = explode(",", $myres);
		$myres = array_reverse($myres);
		$myres = implode(",", $myres);
		fwrite($file, $myres . "\n");
		fclose($file);

		foreach ($buyers as $cosumerNode => $neighbours){
			//trb calc de la $row['name'] la $consumerNode 
		}

		return $list;
	}

	function nodeOf($name, $nodes){
		foreach($nodes as $node){
			if($node['name'] == $name){
				return $node;
			}
		}
		return NULL;
	}

	// used to determine the path from one node to another using an adapted version of DIJKSTRA'S SHORTEST PATH
	// $start is the node from where we are searching for new nodes
	// $end is the destination node | it has the same VALUE at each iteration, regardless of $start's value
	// $result is the string containing the path | has default value of ""
	// $visited is a hash table having KEY = node name and VALUE = 1 / 0 (depending if the node was visited before or not)
	function updatePath ($origin, $nodes, $start, $end, $prev, $result, $visited){
		if($start == NULL){
			return "NO WAEH, JOSE";
		}else{
		// find neighbours of $start
		$nbrs = $start['link_to']; // take neighbour list of $start
		$nbrs = explode(",", $nbrs);
		foreach($nbrs as $nbr){
			//check if $nbr was visited before
			if($prev['name'] == $nbr){
			}
			else if($visited[$nbr] != 1){
				if($start['name'] == $origin){
					$result = "$origin";
				}
				// if NOT VISITED, add it to the path
				$result = implode(",", array ($nbr, $result));
				$visited[$nbr] = 1;

				// if $nbr is the node where we want to end up, we return the full path
				if($nbr == $end['name']){
					return $result;
				}
				// if $nbr is different from the destination node, we continue the path search through its neighbours
				return updatePath ($origin, $nodes, nodeOf($nbr, $nodes), $end, $start, $result, $visited);
			}
		}
		// if we get here, then either all nodes were visited and the destination was not reached
		// OR we have reached a dead end node (node whose neighbours were all visited)
		$result = "";
		}
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
	function addToLog ($content){
		$file = fopen ('../../data/log.txt', 'a+');

		fwrite ($file, $content);
		fclose ($file);
	}






