// Function to read initializer.csv to set initial setting
var readInitial = function(){
	var res 		= $.ajax({type: 'GET', url: 'data/initializer.csv', async: false}).responseText;
	var result		= ";------------------------\n;INITIAL SETTINGS\n;------------------------\n";
	var rows 		= res.split("\n");
	var headings 	= rows[0].split(";");
	
	headings.shift();
	
	for (var i=1; i < rows.length; i++){
		var row 	= rows[i].split(";");
		var node 	= row[0];
		var noLink 	= true;
		
		for (var j=1; j < row.length; j++){
			if (parseInt(row[j]) == 1.0){
				result = result.concat(node, ((Math.random()<.5) ? generateProducer() : generateDemander()), " -- ", headings[j-1], "\n");
				noLink = false;
			}				
		}
		
		if (noLink && node != ''){
			result = result.concat(node, ((Math.random()<.5) ? generateProducer() : generateDemander()), "\n");
		}
	}
	
	return result;
}
// End read initial setting

// Function to generate the producer node
var generateProducer = function(){
	var gen = Math.floor((Math.random() * 100) + 1);
	
	return (gen < 30) ? ' {color: red, shape: dot, value: 1}' : '';
}
// End generate producer	

// Function to generate the demander node
var generateDemander = function(){
	var gen = Math.floor((Math.random() * 100) + 1);
	
	return (gen < 40) ? ' {color: orange}' : '';
}
// End generate demander