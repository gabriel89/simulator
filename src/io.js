(function(){
	IO = function(elt){
		var dom = $(elt)
		var _dialog = dom.find('.dialog')
		var _animating = false
		var startedStatus = 0
		var days = 40
		var dayCounter = 0

		var that = {
			init:function(){
				dom.find('.ctrl > a').live('click', that.menuClick) 
				return that
			},

			menuClick:function(e){
				var button = (e.target.tagName=='A') ? $(e.target) : $(e.target).closest('a')
				var type = button.attr('class').replace(/\s?(selected|active)\s?/,'')
				if (type == 'new'){
					$.ajax({
						url: "src/clear.php",
						success: function(){}
					});
					$(that).trigger({type:"clear"});
					return false;
				}
				else if (type == 'start'){
					if (startedStatus == 0){
						dayCounter++
						//document.getElementById('buttonsbar').innerHTML = '<a href="#" class="start">stop</a><a href="#" class="new" id="loadTreeData">reset</a>'
						$('#buttonsbar').html('<a href="#" class="start">stop</a><a href="#" class="new" id="loadTreeData">reset</a>')
						//$('#code').append('----DAY '+dayCounter+'----\n')
						startedStatus = 1
						for (i=0; i < days; i++){
							//console.log('element on step ' +i);
							//$('#code').append('test' + i + ' -> TEST' +i*2+ '\n')
							console.log('nod' + i + '-- nod' + i*2)
							sleep(400)
							$.ajax({
								type: "POST",
								url: "src/save.php",
								data: {whatToInsert: 'nod' + i + '-- nod' + i*2},
								success: function() {}
							});
							return false;
							}
					} else {
						document.getElementById('buttonsbar').innerHTML = '<a href="#" class="start">start</a><a href="#" class="new" id="loadTreeData">reset</a>';
						console.log('stopped');
						startedStatus = 0;
					}
				}
				else if (type == 'showlogtext'){
					 $('#showlog').popupWindow({ 
						centerBrowser:1,
						width: 800,
						height: 700
					});
					 /*var div = document.getElementById("dom-target")
     				 var myData = div.textContent
				 	 $('#code').val(myData)
					   $("#code").keypress(function(e){
					   	e = jQuery.Event("keypress")
					   	e.which = 13
						     console.log('update');
						    }).trigger(e)*/
				}

				return false
			}
		}

		return that.init()    
	}

	function sleep(milliseconds) {
	  var start = new Date().getTime();
	  for (var i = 0; i < 1e7; i++) {
	    if ((new Date().getTime() - start) > milliseconds){
	      break;
	    }
	}
}
  
})()