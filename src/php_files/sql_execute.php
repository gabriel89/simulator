<?php	
	ini_set('max_execution_time', 300); //300 seconds = 5 minutes
	// execution of sql command and error handling
	function execute_sql ($file, $con, $sql) {
		if ($con->query($sql) !== TRUE) {
			echo "$file SQL: '$sql' - ERROR: $con->error \n";
		}
	}

	// execution of sql command return value
	function execute_sql_and_return ($file, $con, $sql) {
			return mysqli_query($con, $sql);
	}
