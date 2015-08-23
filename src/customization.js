var product_index = 0;

var productsCount = 5; // specify number of products
var products = {};

var was_produced = was_demanded = false;

// Function to read settings from arbor.txt
var readArbor = function(){
	var arbor = '';

	$.ajax({
	    url: 'data/arbor.txt',
	    type: 'GET',
	    async: false,
	    success: function(e) {
	    	console.log('reading from arbor.txt');

	    	arbor = createVisual(jQuery.parseJSON(e));

	    	writeInitialLog(arbor, 'w+');
	    }
	});

	return arbor;
}
// End read arbor.txt

// read fro CSV the initial data (directional)
// TODO: check consistency before using it
var readCSV_D = function(content){
	var rows 		= content.split("\n");
	var headings 	= rows[0].split(";");
	var result 		= {};

	generateProducts();

	headings.shift();

	for (var i=1; i < rows.length; i++){
		var links 		= ''
		var row   		= rows[i].split(";");
		var rowNode  	= row[0].replace("\r", "").replace("\n", "");

		for (var j=1; j < row.length; j++){
			if (parseInt(row[j].replace("\r", "").replace("\n", "")) == 1){
				var local_heading = headings[j-1].replace("\r", "").replace("\n", "");

				links += (local_heading + ',');
			}				
		}

		if (rowNode){
			links 			= links.replace(/(^\s*,)|(,\s*$)/g, '');
			product 		= products[Math.floor(Math.random() * productsCount) + 0];
			result[rowNode] = {linkTo: links, producer: (Math.random()<.3), money: (Math.floor(Math.random() * 30) + 0.57), needsProduct: {'name': product.name, 'value': product.value} }
		}  
	}

	return result
}

// read fro CSV the initial data (non-directional)
var readCSV_ND = function(content){
	var rows 		= content.split("\n");
	var headings 	= rows[0].split(";");
	var headingsArr = [];
	var result 		= {};

	generateProducts();

	// pop empty element from the list
	headings.shift(); 

	// create object from array
	headingsArr = toObjectArray(headings);

	// pop headings from the list
	rows.shift();

	for (key in rows){
		var row   		= rows[key].split(";");
		var rowNode  	= row[0].replace("\r", "").replace("\n", ""); 

		// pop row[0] from the list
		row.shift();

		for (key2 in row){
			if (parseInt(row[key2].replace("\r", "").replace("\n", "")) == 1){
				var local_heading = headings[key2].replace("\r", "").replace("\n", "");

				if (rowNode && rowNode != ''){
					headingsArr[local_heading] += (headingsArr[local_heading] == '') ? rowNode : ',' + rowNode;
					// also add the inverse of it to have the linkTo attribute set
					headingsArr[rowNode] += (headingsArr[rowNode] == '') ? local_heading : ',' + local_heading;
				}
			}
		}
	}

	for (key in headingsArr) {
		product 	= products[Math.floor(Math.random() * productsCount) + 0];
		linkTo 		= headingsArr[key];
		product 	= {'name': product.name, 'value': product.value};
		producer 	= Math.random()<.3;
		money 		= Math.floor(Math.random() * 30) + 0.57;

		result[key] = {linkTo: linkTo, producer: producer, money: money};
		if (producer)
			result[key].hasProduct 	= product;
		else
			result[key].needsProduct = product;
	}

	return result
}

var createVisual = function(content){
	var visual = '';

	$.each(content, function(node, attr){
		nodeProperty = '';

		// set node visual properties
		if (attr['producer']){
			nodeProperty += 'color:red, shape:dot, hasProduct:' + attr['hasProduct']['name'] + '(' + attr['hasProduct']['value'] + ')';
		}else
			nodeProperty += 'needsProduct:' + attr['needsProduct']['name'] + '(' + attr['needsProduct']['value'] + ')';
		nodeProperty += ', money:' + attr['money'];

		// add node properties to the rest of the arbor
		visual = visual.concat(node, '{' + nodeProperty + '}', "\n\n");

		// set links
		if (attr['linkTo'] != ''){
			$.each(attr['linkTo'].split(','), function(index, localNode){
				v1 = node + '--' + localNode;
				v2 = localNode + '--' + node;

				// only add the link if none of the permutations have been added so far to avoid duplicate links
				// this only works (?) in case of non-directed graphs
				if (!(visual.indexOf(v1) > -1) && !(visual.indexOf(v2) > -1))
					visual = visual.concat(v1, "\n\n");
			});
		}
	});

	return visual;
}

// Function to write initial data to the log
var writeInitialLog = function(content, action){
	content = ";--------------------------------\n;        INITIAL SETTINGS\n;--------------------------------\n" + content

	$.ajax({
		type: "POST",
		async: false,
		url: "src/save.php",
		data: {whatToInsert: content, file: '../data/log.txt', action: action}
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
		data: {whatToInsert: JSON.stringify(products), file: '../data/products.txt', action: 'w+'}
	});
}
// end generate products

// function to create object from an array of headings
function toObjectArray(arr) {
	var rv = [];

	for (key in arr) {
		rv[arr[key].replace("\r", "").replace("\n", "")] = '';
	}

	return rv;
}


































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
