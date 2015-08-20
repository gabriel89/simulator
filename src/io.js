(function(){
	IO = function(elt){
		var days 			= 40 //set cycle number

		var dom 			= $(elt)
		var _dialog 		= dom.find('.dialog')
		var _animating 		= false
		var dayCounter 		= 0
		var fileContent 	= ''
		var io_arbor 		= ''
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
						fileContent 	= $.ajax({type: 'GET', url: 'data/arbor.txt', async: false}).responseText;
						io_arbor 		= jQuery.parseJSON(fileContent);

						console.log(io_arbor);

						// start cycle
						myVar = setInterval(function(){ printOnStartClick() }, 1000);
						printOnStartClick();
					} else {
						console.log(io_arbor);

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
					var initializer = $.ajax({type: 'GET', url: 'data/initializer.csv', async: false});
					var ri = readInit(initializer.responseText);
			    	$.ajax({
					type: "POST",
					url: "src/save.php",
					async: false,
					data: {whatToInsert: JSON.stringify(ri), file: '../data/arbor.txt', action: 'w+'},
					success: function() {
						console.log('Loaded fresh data from initializer.csv in arbor.txt');
					}
				});	    	
				}
			}
		}

		return that.init()    
	}

	var iterator = 1;
	function printOnStartClick() {
		// print headers in log
		var header = "\n;--------------------------------\n;        ITERATION " + iterator + "\n;--------------------------------\n";
		var content = ''

		content = 'nod' + iterator + ' -- nod' + iterator*2;
		iterator += 1;

		addToLog(header, 'a+');
		addToLog(content + "\n", 'a+');

		// broadcast each node's needs

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
			data: {whatToInsert: content, file: '../data/log.txt', action: action},
			success: function() {}
		});
	}
})()