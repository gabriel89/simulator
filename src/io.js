(function(){
	IO = function(elt){
		var dom = $(elt)
		var _dialog = dom.find('.dialog')
		var _animating = false
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
				console.log(type);
				if (type == 'new'){
					$.ajax({
						url: "src/clear.php",
						success: function(){}
					});
					$(that).trigger({type:"clear"});
					return false;
				}
				else if (type == 'start'){
					if ($('#start_stop').data('started')){
						$('#start_stop').data('started', false).text('stop');
						// dayCounter++
						//$('#code').append('----DAY '+dayCounter+'----\n')
						for (i=0; i < days; i++){
							//console.log('element on step ' +i);
							//$('#code').append('test' + i + ' -> TEST' +i*2+ '\n')
							console.log('nod' + i + '-- nod' + i*2);
							sleep(400);
							$.ajax({
								type: "POST",
								url: "src/save.php",
								data: {whatToInsert: 'nod' + i + '-- nod' + i*2},
								success: function() {}
							});
						}
					} else {
						$('#start_stop').data('started', true).text('start');
						console.log('stopped');
					}
				}
				else if (type == 'showlogtext'){
					if ($('#showlog').data('showing')){
						$('#showlog').data('showing', false).text('hide log');
						
						// var w = window.open();
						  // var html = $("#toNewWindow").html();

							// $(w.resizeTo(600,600).document.body).html(html);
					}else{
						$('#showlog').data('showing', true).text('show log');
						console.log('not displayed');
					}
					// $('#dialog').dialog(); 
					// $('#showlog').popupWindow({ 
						// centerBrowser:1,
						// width: 800,
						// height: 700
					// });
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