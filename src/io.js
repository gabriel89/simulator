(function(){
	// var maxDist = 0;
	// var localDist = 0;
	// var nodePath = [];

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
						url: "src/php_files/clear.php",
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
						console.log('Clicked on START');

						myVar = setInterval(function(){ printOnStartClick() }, 1000);
						printOnStartClick();
					} else {
						// set html property
						$('#start_stop').data('started', true).text('start');

						// end cycle
						clearInterval(myVar);

						// print status
						console.log('Clicked on STOP');
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

	function printOnStartClick() {
		$.ajax({
		    url: 'src/php_files/simulator.php',
		    type: 'GET',
		    async: false,
		    success: function(e) {console.log('Running simulation'); console.log(e)},
			error: function(e) {console.log('Error running simulation');}
		});
	}

	function renderTree() {
		var e = jQuery.Event("keydown");
		e.which = 13;
		$("#code").trigger(e);
	}
})()