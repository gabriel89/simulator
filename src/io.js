(function(){
	var io_arbor = '';
	var maxDist = 0;
	var localDist = 0;
	var nodePath = [];

	IO = function(elt){
		var days 			= 40 //set cycle number

		var dom 			= $(elt)
		var _dialog 		= dom.find('.dialog')
		var _animating 		= false
		var dayCounter 		= 0
		var fileContent 	= ''
		var myVar 			= null
		var that 			= ''

		that = {
			init:function(){
				dom.find('.ctrl > a').live('click', that.menuClick) 
				return that
			},

			menuClick:function(e){
				var button = (e.target.tagName=='A') ? $(e.target) : $(e.target).closest('a')
				var type = button.attr('class').replace(/\s?(selected|active)\s?/,'')
				
				if (type == 'new'){
					$.ajax({
						type: 'POST',
						url: "src/clear.php",
						data: {file: "../data/log.txt", },
						success: function(){}
					});
					$(that).trigger({type:"clear"});
					
					renderTree();
					
					return false;
				}
				else if (type == 'start'){
					if ($('#start_stop').data('started')){
						// set html property
						$('#start_stop').data('started', false).text('stop');

						// print status
						console.log('started');

						// read arbor.txt content
						// fileContent = $.ajax({ 	type: 'POST', 
						// 						url: 'src/backbone.php', 
						// 						async: false,
						// 						success: function(data) {
						// 							console.log(data);
						// 						},
						// 						error: function(data) {
						// 							console.log('error');
						// 							console.log(data);
						// 						},
						// 					});


						// fileContent 	= $.ajax({type: 'GET', url: 'data/arbor.txt', async: false}).responseText;
						// io_arbor 		= jQuery.parseJSON(fileContent);

						// start cycle
						// myVar = setInterval(function(){ printOnStartClick() }, 1000);
						// printOnStartClick();
					} else {
						// set html property
						$('#start_stop').data('started', true).text('start');

						// write new content to arbor.txt
						$.ajax({type: "POST", url: "src/save.php", async: false, data: {whatToInsert: JSON.stringify(io_arbor), file: '../data/arbor.txt', action: 'w+'}});

						// end cycle
						clearInterval(myVar);
						console.log('stopped');
					}
				}
				else if (type == 'showlogtext') {
					$('#popuplogcontent').val($.ajax({type: 'GET', url: 'data/log.txt', async: false}).responseText);
				}
				// reads from initializer and stores it in the arbor.txt on load-link
				else if (type == 'ForceInit') {
					// var initializer = $.ajax({type: 'GET', url: 'data/initializer.csv', async: false});
					// var ri = readCSV_ND(initializer.responseText);
					$.ajax({
						type: "POST",
						url: "src/php_files/create_initial_arbor.php",
						async: false,
						success: function() {console.log('Loaded fresh data from initializer.csv into database');},
						error: function() {console.log('Error loading fresh data from initializer.csv into database');}
					});
				}
			}
		}

		return that.init()    
	}

	var iterator = 1;
	function printOnStartClick() {

		// ajax call here with io_arbor as param

		// print headers in log
		var header = "\n;--------------------------------\n;        ITERATION " + iterator + "\n;--------------------------------\n";
		var content = ''

		content = 'nod' + iterator + ' -- nod' + iterator*2;
		iterator += 1;

		addToLog(header, 'a+');
		addToLog(content + "\n\n", 'a+');

		// broadcast each node's needs
		for (iterator in io_arbor) {
			maxDist += 1;
			localDist = 0;

			//console.log('Checking max ' + maxDist + ' nodes');

			for (key in io_arbor) {
				var local = io_arbor[key];

				if (!local.producer){
					searchNeighbours(io_arbor, key, '');
				}
			}
		}
	}

	function renderTree() {
		var e = jQuery.Event("keydown");
		e.which = 13;
		$("#code").trigger(e);
	}

	function addToLog(content, action){
		$.ajax({
			type: "POST",
			url: "src/save.php",
			data: {whatToInsert: content, file: '../data/log.txt', action: action}
		});
	}

	// function to check "node"-s neighbours if they have what "node" needs
	// cNode = current node
	// pNode = parent node
	function searchNeighbours(arbor, cNode, pNode){
		var thisNode = arbor[cNode];

		localDist += 1;

		if (localDist <= maxDist){
			// if (pNode == ''){
			// 	console.log('dedesubt de pnode');

			// } else {
				$.each(thisNode.linkTo.split(','), function(index, localNode){
					// check to see if cNode is set and is not the same as the localNode, to avoid backwards referencing
					if ((cNode != '') && (cNode != localNode)){
						$.ajax({
							type: "POST",
							url: "src/matrix.php",
							async: false,
							data: {cNode: cNode, pNode: pNode},
							succes: function(e) {console.log('this shit is done');},
							error: function() {console.log('2');}
						});
						// also check that the following node has a linkTo property, else it will error out later on in the function
						if (arbor[localNode]){
							searchNeighbours(arbor, localNode, cNode);
						}
					}
				});
			// }
		}
	}
})()