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
		$nodes = execute_sql_and_return ('<simulator.php>', $con, "SELECT * FROM nodes");

		updateConsumer ($nodes);

		decideUponInvestment ();
	}


	// function to update the consumer list
	function updateConsumer () {
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






