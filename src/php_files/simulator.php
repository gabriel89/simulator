<?php
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

	// start simulator by setting the execution number
	prepareSim (10, $con);

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

		decideUponInvestment();
	}

	function updateRevenue () {
	}

	// function to decide upon investment options
	function decideUponInvestment ($idx) {
		//investment is decided in regard to the minimum cost of the investment option
		//ex: if production costs more than links => chose links
		//ex2: if 
		/*
		MAYBE investInProduction();
		MAYBE investInLinks();
		MAYBE investInExpansion();
		*/
		$linkTargets = getLinkTargets ($idx);
		$linkInvestmentCost = getLinkInvestmentCost ($idx);
		// investment decision is made based upon the minimum investment cost
		// 0 == LINKS
		// 1 == PRODUCTION
		// 2 == EXPANSION
		// 3 == RESIGNATION
		$investmentDecision = 0; 

		if ($investmentDecision === 0){
			investInLinks ($idx, $linkTargets);
		}
	}