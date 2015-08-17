var product_index = 0;

var productsCount = 5; // specify number of products
var products = {};

var was_produced = was_demanded = false;

// Function to read initializer.csv to set initial setting
var readInitial = function(){
	var arbor			= ''
	var content         = '';
	var initializer 	= $.ajax({type: 'GET', url: 'data/initializer.csv', async: false});
	var initializerSize = initializer.getResponseHeader('Content-Length');

	generateProducts();

	var x = $.ajax({
	    url: 'data/arbor.txt',
	    type: 'GET',
	    async: false,
	    error: function() {},
	    success: function(e) {
	    	var size = e.split("\n");

	    	if ((size[0].replace("\r", "").replace("\n", "")) == initializerSize){
	    		console.log('reading from arbor.txt ('+(size[0].replace("\r", "").replace("\n", ""))+' == '+initializerSize+')');
	    		content = jQuery.parseJSON(size[1]);

	    	}else{
	    		console.log('creating fresh set of data ('+(size[0].replace("\r", "").replace("\n", ""))+' != '+initializerSize+')');

	    		// read from initializer
	    		content = readInit(initializer.responseText);

	    		// truncate arbor.txt
	    		$.ajax({
					url: "src/clear.php",
					async: false,
					data: {file: '../data/arbor.txt'},
					success: function(){
						console.log('cleared data from arbor.txt');
					}
				});

	    		// write in data size and object
	    		$.ajax({
					type: "POST",
					url: "src/save.php",
					async: false,
					data: {whatToInsert: initializerSize + "\n" + JSON.stringify(content), file: '../data/arbor.txt', action: 'w+'},
					success: function() {
						console.log('added fresh content to arbor.txt');
					}
				});
	    	}

	    	arbor = ";--------------------------------\n;        INITIAL SETTINGS\n;--------------------------------\n" + createVisual(content);

	    	writeInitialLog(arbor, 'w+');
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

			result[rowNode] = {linkTo: links, producer: (Math.random()<.3), money: Math.random().toFixed(2)*10, product: products[Math.floor(Math.random() * productsCount) + 0]  }
		}
	}

	return result
}

var createVisual = function(content){
	var links = '';
	var linkContent = '';

	$.each(content, function(node, attr){
		nodeProperty = '';

		// set node visual properties
		if (attr['producer']){
			nodeProperty += 'color: red, shape: dot, ';
		}

		nodeProperty += 'money:' + attr['money'];
		nodeProperty += ', product:' + attr['product']['name'] + '(' + attr['product']['value'] + ')';

		links = links.concat(node, '{' + nodeProperty + '}', "\n");

		// set links
		$.each(attr['linkTo'].split(','), function(index, localNode){
			links = links.concat(node, '--', localNode, "\n");
		});
	});

	return links;
}

// Function to write initial data to the log
var writeInitialLog = function(content, action){
	$.ajax({
		type: "POST",
		async: false,
		url: "src/save.php",
		data: {whatToInsert: content, file: '../data/log.txt', action: action},
		success: function() {}
	});
}
// End write initial

// function to generate products and their values
function generateProducts(){
	for(var local=0; local < productsCount; local++){
		products[local] = {name: 'P'+local, value: Math.random().toFixed(2)};
	}

	$.ajax({
		type: "POST",
		async: false,
		url: "src/save.php",
		data: {whatToInsert: JSON.stringify(products), file: '../data/products.txt', action: 'w+'},
		success: function() {}
	});
}
// end generate products




































// FOLLOWING FUNCTIONS ARE NOT USED ANYMORE
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
