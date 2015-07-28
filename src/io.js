(function(){
	IO = function(elt){
		var dom = $(elt)
		var _dialog = dom.find('.dialog')
		var _animating = false

		var that = {
			init:function(){
				dom.find('.ctrl > a').live('click', that.menuClick) 
				return that
			},

			menuClick:function(e){
				var button = (e.target.tagName=='A') ? $(e.target) : $(e.target).closest('a')
				var type = button.attr('class').replace(/\s?(selected|active)\s?/,'')

				if (type == 'new'){
				  $(that).trigger({type:"clear"})
				}
				else if (type == 'textingload'){
					 var div = document.getElementById("dom-target")
     				 var myData = div.textContent
				 	 $('#code').val(myData)
					   $("#code").keypress(function(e){
					   	e = jQuery.Event("keypress")
					   	e.which = 13
						     console.log('update');
						    }).trigger(e)
				}

				return false
			}
		}

		return that.init()    
	}
  
})()