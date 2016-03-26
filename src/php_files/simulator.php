<?php
	// increase maximum execution time (in seconds, 300 seconds = 5 minutes)
	ini_set('max_execution_time', 300);

	// include files
	include_once ('sql_execute.php');
	include_once ('common_functions.php');
	include_once ('consumer_phase.php');
	include_once ('economic_phase.php');
	include_once ('globals.php');

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
	
	// start measurement of code execution time
	$time_start = microtime(true); 

	// start simulator by setting the execution number
	prepareSim (150, $con);
	
	// end measurement of code execution time
	$time_end = microtime(true);

	//execution time of the script in
	echo 'Simulator runtime: ' . ((($time_end - $time_start) < 60) ? (($time_end - $time_start) . ' seconds') : (($time_end - $time_start) / 60) . ' minutes');
	// close connection
	$con->close ();

	// ----------------------------------------------------------------------------------------------------------

	// function to set iteration number of the 
	function prepareSim ($exec_count, $con) {
		addToLog ("\n\n\n\n\n\n\n\n\n\n\n;--------------------------------\n;        STARTING SIMULATOR\n;--------------------------------\n\n\n");
		// calculate pay-day recurrence
		$pay_day = floor ($exec_count / 12);

		for ($exec_iterator = 1; $exec_iterator <= $exec_count; $exec_iterator++) {
			// make sure we refresh the money only in 1/12th of the total number of iteration
			
			if ($pay_day > 0)
				if (($pay_day > ($exec_count / 12)) && ($exec_iterator % $pay_day == 0)) {
				    payDay ($con, $nodes);
				}
			
			startSim ($con, $exec_iterator, $exec_count);
		}

		addToLog ("\n;--------------------------------\n;        ENDING SIMULATOR\n;--------------------------------\n");
	}

	// function to start the simulator iterating over the nodes in the graph
	function startSim ($con, $exec_iterator, $exec_count) {
		addToLog (";--------------------------------\n;        SIMULATOR PHASE $exec_iterator/$exec_count\n;--------------------------------");
		print_r ("On phase $exec_iterator/$exec_count\n");

		consumerPhase ($con);
		//economicPhase ($con, $nodes);

		addToLog ("\n\n\n\n\n\n\n\n\n\n\n");
	}

	// function to treat information related to the consumer phase
	function consumerPhase ($con) {
		global $nodes;

		$nodes = checkNodesGlobalVariable ($con);

		// retrieve a list of lists representing an economic path from producer to consumer
		$consumer_path = getConsumerPath ($con, $nodes);

		// make transaction for each possible consumer
		finalizeTransaction ($con, $consumer_path);

		// unset $consumer_path array once it is not used anymore
		unset ($consumer_path);
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