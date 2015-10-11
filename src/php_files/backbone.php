<?php
	// $io_arbor = file_get_contents('../data/arbor.txt');


		// function printOnStartClick() {

		// // ajax call here with io_arbor as param

		// // print headers in log
		// var header = "\n;--------------------------------\n;        ITERATION " + iterator + "\n;--------------------------------\n";
		// var content = ''

		// content = 'nod' + iterator + ' -- nod' + iterator*2;
		// iterator += 1;

		// addToLog(header, 'a+');
		// addToLog(content + "\n\n", 'a+');

		// // broadcast each node's needs
		// for (iterator in io_arbor) {
		// 	maxDist += 1;
		// 	localDist = 0;

		// 	//console.log('Checking max ' + maxDist + ' nodes');

		// 	for (key in io_arbor) {
		// 		var local = io_arbor[key];

		// 		if (!local.producer){
		// 			searchNeighbours(io_arbor, key, '');
		// 		}
		// 	}
		// }
	}


	

	// 	// print headers in log
	// 	var header = "\n;--------------------------------\n;        ITERATION " + iterator + "\n;--------------------------------\n";
	// 	var content = ''

	// 	content = 'nod' + iterator + ' -- nod' + iterator*2;
	// 	iterator += 1;

	// 	addToLog(header, 'a+');
	// 	addToLog(content + "\n\n", 'a+');

	// 	// broadcast each node's needs
	// 	for (iterator in io_arbor) {
	// 		maxDist += 1;
	// 		localDist = 0;

	// 		//console.log('Checking max ' + maxDist + ' nodes');

	// 		for (key in io_arbor) {
	// 			var local = io_arbor[key];

	// 			if (!local.producer){
	// 				searchNeighbours(io_arbor, key, '');
	// 			}
	// 		}
	// 	}
	// }





	// // function to check "node"-s neighbours if they have what "node" needs
	// // cNode = current node
	// // pNode = parent node
	// function searchNeighbours(arbor, cNode, pNode){
	// 	var thisNode = arbor[cNode];

	// 	localDist += 1;

	// 	if (localDist <= maxDist){
	// 		// if (pNode == ''){
	// 		// 	console.log('dedesubt de pnode');

	// 		// } else {
	// 			$.each(thisNode.linkTo.split(','), function(index, localNode){
	// 				// check to see if cNode is set and is not the same as the localNode, to avoid backwards referencing
	// 				if ((cNode != '') && (cNode != localNode)){
	// 					$.ajax({
	// 						type: "POST",
	// 						url: "src/matrix.php",
	// 						async: false,
	// 						data: {cNode: cNode, pNode: pNode},
	// 						succes: function(e) {console.log('this shit is done');},
	// 						error: function() {console.log('2');}
	// 					});
	// 					// also check that the following node has a linkTo property, else it will error out later on in the function
	// 					if (arbor[localNode]){
	// 						searchNeighbours(arbor, localNode, cNode);
	// 					}
	// 				}
	// 			});
	// 		// }
	// 	}
















	

	// echo $io_arbor;
	


	// $cNode = $_POST['cNode'];
	// $pNode = $_POST['pNode'];

	// $mat = array(array('',''),
	// 			 array('',''));
		
	// $mat[$cNode][$pNode] = true;
	// $mat[$pNode][$cNode] = true;

	// echo $mat;