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
				var txtContentLog = '';
				if (type == 'new'){
					$.ajax({
						url: "src/clear.php",
						success: function(){
							console.log('teoretic e gol logu');
						}
					});
					$(that).trigger({type:"clear"});
					txtContentLog = $('code').text();
					console.log(txtContentLog);
					$.ajax({
						type: "POST",
						url: "src/save.php",
						//data: {whatToInsert: txtContentLog},
						data: {whatToInsert: ';--------------------------------\n;        INITIAL SETTINGS\n;--------------------------------\nn0 -- n5\nn0 -- n22\nn1 -- n22\nn1 {color: red, shape: dot, productID: 0, value: 0.80} -- n26\nn2 -- n29\nn3 {color: orange, productID: 1, canPay: 0.45} -- n17\nn4 -- n24\nn4 -- n29\nn5 {color: red, shape: dot, productID: 2, value: 0.30} -- n28\nn6 {color: orange, productID: 2, canPay: 0.37} -- n16\nn6 {color: orange, productID: 3, canPay: 0.18} -- n25\nn6 -- n28\nn7 {color: orange, productID: 4, canPay: 0.38} -- n10\nn8 -- n17\nn8 -- n23\nn8 -- n29\nn9 -- n20\nn10 -- n7n\n10 {color: red, shape: dot, productID: 6, value: 0.21} -- n9\nn11 -- n15\nn11 -- n19\nn12 -- n0\nn13 {color: orange, productID: 7, canPay: 0.30} -- n25\nn14 {color: orange, productID: 7, canPay: 0.69} -- n24\nn15 {color: orange, productID: 7, canPay: 0.18} -- n21\nn15 -- n22\nn15 -- n28\nn15 -- n29\nn16 -- n18\nn16 {color: orange, productID: 9, canPay: 0.44} -- n23\nn17 {color: orange, productID: 9, canPay: 0.97} -- n0\nn17 -- n9\nn18 -- n26\nn18 -- n27\nn19 -- n12\nn19 {color: red, shape: dot, productID: 10, value: 0.98} -- n17\nn20 -- n28\nn21 -- n17\nn22 -- n10\nn22 -- n15\nn23 -- n8\nn23 {color: red, shape: dot, productID: 11, value: 0.34} -- n16\nn24 -- n9n\n25 -- n11\nn25 {color: orange, productID: 11, canPay: 0.54} -- n16\nn26 -- n10\nn27 -- n10\nn27 {color: orange, productID: 12, canPay: 0.27} -- n17\nn28 {color: orange, productID: 13, canPay: 0.97} -- n29\nn29 -- n14'},
						success: function() {}
					});
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
    $('#code').append(t+'\n');
    $.ajax({
		type: "POST",
		url: "src/save.php",
		data: {whatToInsert: t},
		success: function() {}
	});
}})()