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
	prepareSim (10, $con);

	// close connection
	$con->close ();

	// ----------------------------------------------------------------------------------------------------------


	// function to set iteration number of the 
	function prepareSim ($exec_count, $con) {
		for ($exec_iterator = 0; $exec_iterator < $exec_count; $exec_iterator ++){
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
			$buyers = execute_sql_and_return ('<simulator.php>', $con, "SELECT name, link_to FROM nodes WHERE needs_product='".$row['has_product']."'");
			while ($buy = mysqli_fetch_assoc ($buyers)) {
				$possibleBuyers[$buy['name']] = $buy['link_to'];
			}

			print_r($possibleBuyers);


			$possibleBuyers = getPossibleBuyers ($con, $nodes, $row);

			print_r($possibleBuyers);

			// set producer to "false"
			$row['is_producer'] = 0;

		}
	}

	// function to return a list of possible buyers using the basic Dijkstra's algorithm
	function getPossibleBuyers ($con, $nodes, $row) {
		$list = [];
		// http://codereview.stackexchange.com/questions/75641/dijkstras-algorithm-in-php
		// http://stackoverflow.com/questions/6598791/how-to-optimize-dijkstra-code-in-php
		// http://stackoverflow.com/questions/4867716/more-than-640-000-elements-in-the-array-memory-problem-dijkstra
		// https://en.wikipedia.org/wiki/Dijkstra's_algorithm
		// https://github.com/phpmasterdotcom/DataStructuresForPHPDevs/blob/master/Graphs/graph-dijkstra.php
		// http://odino.org/the-shortest-path-problem-in-php-demystifying-dijkstra-s-algorithm/








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
	function addToLog ($content){
		$file = fopen ('../../data/log.txt', 'a+');

		fwrite ($file, $content);
		fclose ($file);
	}






