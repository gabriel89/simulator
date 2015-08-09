(function(){
	IO = function(elt){
		var dom = $(elt)
		var _dialog = dom.find('.dialog')
		var _animating = false
		var days = 40
		var dayCounter = 0
		var myVar = null
		var that = {
			init:function(){
				dom.find('.ctrl > a').live('click', that.menuClick) 
				return that
			},

			menuClick:function(e){
				var button = (e.target.tagName=='A') ? $(e.target) : $(e.target).closest('a')
				var type = button.attr('class').replace(/\s?(selected|active)\s?/,'')
				if (type == 'new'){
					/*$.ajax({
						url: "src/clear.php",
						success: function(){}
					});*/
					$(that).trigger({type:"clear"});
					return false;
				}
				else if (type == 'start'){
					if ($('#start_stop').data('started')){
						$('#start_stop').data('started', false).text('stop');
						myVar = setInterval(function(){ printOnStartClick() }, 1000);
						printOnStartClick();
					} else {
						$('#start_stop').data('started', true).text('start');
						clearInterval(myVar);
						console.log('stopped');
					}
				}
			}
		}

		return that.init()    
	}

var i = 1;

function printOnStartClick() {
    t = 'nod'+i+' -- nod'+i*2;
    i=i+1;
    console.log(t);
    //$('#code').append(t+'\n');
    /*$.ajax({
		type: "POST",
		url: "src/save.php",
		data: {t},
		success: function() {}
	});*/
}})()