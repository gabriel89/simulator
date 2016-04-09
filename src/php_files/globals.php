<?php
	include_once ('common_functions.php');
	$null_refs = [];
	/*
	after a node will resign it's index will be added in this array
	so that it won't be necessary to remove the node from the database
	when a node will want to be added the null_refs will be looked in and 
	searched for positions where there is an empty position in the nodes array
	and the new node will be inserted in that index
	*/


	$nodes = [];
	/*
	nodes will hold information regarding the user network
	contains the following fields : 
		- ID | coincides with the ID in array
		- links | IDs of nodes directly connected to -this- node in the network
		- requests | serialized string containing list of requests
			#	a request is structured as follows:
				productID|quantity|priority
			#	requests in serialized string are separated by the character '^'
		- serves | productID of the product the node serves
		- quantity | how much of served product is available at this node
		- is_producer | ????
		- misc | ????
	fields will be updated during simulation and updated in the DB
	*/


	$products = [];
	/*
	nodes will hold information regarding the user network
	contains the following fields : 
		- ID | DB index of the product
		- name | identifier for the product
		- base_cost | inityially set to ZERO (i.e. 64b'0) | is updated after each production stage
			(this includes initial stage where the network is constructed and initial product quantity is set)
			(IS GENERATED ON ACCOUNT OF GLOBAL QUANTITY AND max_cost)
		- max_cost | generated randomly at simulation start
			(represents cost of the product when only 1 piece of it is available on market)
			(IS CONSTANT)
		- global_quantity | represents the quantity of the product available on the market
			(IS VOLATILE)
			(IS GENERATED ON ACCOUNT OF NEW PRODUCER VALUES AFTER EACH PRODUCER STAGE)
	fields will be updated during simulation and updated in the DB
	*/