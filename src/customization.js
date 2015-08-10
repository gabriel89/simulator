// Function to read initializer.csv to set initial setting
var product_index = 0;
var was_produced = was_demanded = false;

var readInitial = function(){
	var res 		= $.ajax({type: 'GET', url: 'data/initializer.csv', async: false}).responseText;
	var result		= ";--------------------------------\n;        INITIAL SETTINGS\n;--------------------------------\n";
	var rows 		= res.split("\n");
	var headings 	= rows[0].split(";");
	
	headings.shift();
	
	for (var i=1; i < rows.length; i++){
		var row 	= rows[i].split(";");
		var node 	= row[0].replace("\r", "").replace("\n", "");
		
		for (var j=1; j < row.length; j++){
			if (parseInt(row[j].replace("\r", "").replace("\n", "")) == 1){
				var local_heading = headings[j-1].replace("\r", "").replace("\n", "");

				result = result.concat((Math.random()<.5) ? generateProducer(node) : generateDemander(node));
				result = result.concat(node, " -- ", local_heading, "\n");
			}				
		}
	}
	
	return result;
}
// End read initial setting

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

// Function to generate the demander node
var generateDemander = function(node){
	var gen = Math.floor((Math.random() * 100) + 1);
	var node_data = (gen < 40) ? node + ' {color: orange, productID: ' + product_index + ', canPay: ' + Math.random().toFixed(2) + '}\n' : '';	
	
	setProduct('demander');
	return node_data;
}
// End generate demander


// Function to write initial data to the log
var writeInitialLog = function(content){
	/*$.ajax({
		type: "POST",
		url: "src/save.php",
		data: {whatToInsert: content},
		success: function() {}
	});*/
}
// End write initial