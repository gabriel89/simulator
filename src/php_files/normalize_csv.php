<?php
	// include files
	include_once ('common_functions.php');

	$new_content = read_original_csv_file (file_get_contents ('../../data/initializer.csv'));

	// write in the csv file the new content
	normalize_csv_file ($new_content);

	// ----------------------------------------------------------------------------------------------------------

	function  read_original_csv_file ($content){
		// split file-content into individual lines
		$lines = explode ("\n", $content);

		// get the number of nodes
		$node_count = sizeof (explode (";", $lines[0])) - 1;

		$container = array();
	
		for ($i = 0; $i < $node_count; $i++) {
			for ($j = 0; $j < $node_count; $j++){
				$container[$i][$j] = 0;
			}
		}

		// get rid of first line of the file | DON'T NEED ANYMORE
		for ($i = 0; $i < $node_count; $i++) {
			// get corresponding line
			$line_links = explode(";", $lines[$i]);
			// get rid of first element | REDUNDANT
			array_shift($line_links);

			for ($j = 0; $j < $node_count; $j++) {
				if ($line_links[$j] != 0) {
					$container[$i][$j] = 1;
					$container[$j][$i] = 1;
				}
			}
		}

		return array('node_count' => $node_count, 'links' => $container);
	}

	// function to write in the csv file the new content
	function normalize_csv_file ($container) {
		$myfile = fopen("../../data/initializer.csv", "w") or die("Unable to open file!");

		fwrite($myfile, "Normalized\n");

		for($i = 0; $i < $container['node_count']; $i++){
			fwrite($myfile, ';n' . $i);
		}

		fwrite($myfile, "\n");

		for($i = 0; $i < $container['node_count']; $i++){
			fwrite($myfile, 'n' . $i . ';');
			
			for ($j = 0; $j < $container['node_count']; $j++) {
				fwrite($myfile, $container['links'][$i][$j] . ';');
			}

			fwrite($myfile, "\n");
		}

		fclose($myfile);
	}
