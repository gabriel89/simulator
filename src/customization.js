// Function to read initializer.csv to set initial setting
var product_index = 0;
var was_produced = was_demanded = false;

var readInitial = function(){
	var arbor			= ''
	var content         = '';
	var initializer 	= $.ajax({type: 'GET', url: 'data/initializer.csv', async: false});
	var initializerSize = initializer.getResponseHeader('Content-Length');

	var x = $.ajax({
	    url: 'data/data.txt',
	    type: 'GET',
	    error: function() {},
	    success: function(e) {
	    	var size = e.split("\n");

	    	if ((size[0].replace("\r", "").replace("\n", "")) == initializerSize){
	    		console.log('reading from data.txt ('+(size[0].replace("\r", "").replace("\n", ""))+' == '+initializerSize+')');
	    		content = jQuery.parseJSON(size[1]);

	    	}else{
	    		console.log('creating fresh set of data ('+(size[0].replace("\r", "").replace("\n", ""))+' != '+initializerSize+')');

	    		// read from initializer
	    		content = readInit(initializer.responseText);

	    		// truncate data.txt
	    		$.ajax({
					url: "src/clear.php",
					async: false,
					data: {file: '../data/data.txt'},
					success: function(){
						console.log('cleared data from data.txt');
					}
				});

	    		// write in data size and object
	    		$.ajax({
					type: "POST",
					url: "src/save.php",
					async: false,
					data: {whatToInsert: initializerSize + "\n" + JSON.stringify(content), file: '../data/data.txt', action: 'w+'},
					success: function() {
						console.log('added fresh content to data.txt');
					}
				});
	    	}

	    	arbor = ";--------------------------------\n;        INITIAL SETTINGS\n;--------------------------------\n" + createVisual(content);
	    	$('#code').val(arbor);
	    }
	});
}
// End read initial setting

var readInit = function(content){
	var rows 		= content.split("\n");
	var headings 	= rows[0].split(";");
	var result 		= {};

	headings.shift();

	for (var i=1; i < rows.length; i++){
		var links = ''
		var row   = rows[i].split(";");
		var rowNode  = row[0].replace("\r", "").replace("\n", "");


		for (var j=1; j < row.length; j++){
			if (parseInt(row[j].replace("\r", "").replace("\n", "")) == 1){
				var local_heading = headings[j-1].replace("\r", "").replace("\n", "");

				links += (local_heading + ',');
			}				
		}

		if (rowNode){
			links = links.replace(/(^\s*,)|(,\s*$)/g, '');

			result[rowNode] = {linkTo: links, producer: (Math.random()<.3), money: Math.random(), productID: 43}
			// createVisual(content);
		}
	}

	return result
}

var createVisual = function(content){
	var links = '';
	var linkContent = '';

	$.each(content, function(node, attr){
		linkContent = '';

		// set node visual properties
		if (attr['producer']){
			linkContent = 'color: red, shape: dot';
		}

		links = links.concat(node, '{' + linkContent + '}', "\n");

		// set links
		$.each(attr['linkTo'].split(','), function(index, localNode){
			links = links.concat(node, '--', localNode, "\n");
		});
	});

	return links;
}



	// $.ajax({
	// 	type: "POST",
	// 	url: "src/save.php",
	// 	data: {whatToInsert: result, file: '../data/data.txt'},
	// 	success: function() {console.log('am save-uit!!!');}
	// });



	// var result		= ";--------------------------------\n;        INITIAL SETTINGS\n;--------------------------------\n";
	// var rows 		= res.split("\n");
	// var headings 	= rows[0].split(";");
	// var dataTxtFile = '';
	//var dataTxtFile = $.ajax({type: 'GET', url: 'data/data2.txt', async: false}).responseText;

	
	// headings.shift();
	
	// for (var i=1; i < rows.length; i++){
	// 	var row 	= rows[i].split(";");
	// 	var node 	= row[0].replace("\r", "").replace("\n", "");
		
	// 	for (var j=1; j < row.length; j++){
	// 		if (parseInt(row[j].replace("\r", "").replace("\n", "")) == 1){
	// 			var local_heading = headings[j-1].replace("\r", "").replace("\n", "");

	// 			result = result.concat((Math.random()<.5) ? generateProducer(node) : generateDemander(node));
	// 			result = result.concat(node, " -- ", local_heading, "\n");
	// 		}				
	// 	}
	// }
	
	// return result;

// Function to generate product number
var setProduct = function(what){
	if (what == 'producer'){
		was_produced = true;
	}else if (what == 'demander'){
		was_demanded = true;
	}

	if (was_demanded && was_produced){
		was_demanded = was_produced = false;
		product_index += 1;
	}
}
// End generate product

// Function to generate the producer node
var generateProducer = function(node){
	var gen = Math.floor((Math.random() * 100) + 1);
	var node_data = (gen < 30) ? node + ' {color: red, shape: dot, productID: ' + product_index + ', value: ' + Math.random().toFixed(2) + '}\n' : '';
	
	setProduct('producer');
	return node_data;
}
// End generate producer	

// Function to generate the consumer node
var generateConsumer = function(node){
	var gen = Math.floor((Math.random() * 100) + 1);
	var node_data = (gen < 40) ? node + ' {color: orange, productID: ' + product_index + ', canPay: ' + Math.random().toFixed(2) + '}\n' : '';	
	
	setProduct('demander');
	return node_data;
}
// End generate consumer


// Function to write initial data to the log
var writeInitialLog = function(content){
	$.ajax({
		type: "POST",
		url: "src/save.php",
		data: {whatToInsert: content},
		success: function() {}
	});
}
// End write initial