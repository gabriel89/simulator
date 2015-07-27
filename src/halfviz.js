(function(){
	trace = arbor.etc.trace
	objmerge = arbor.etc.objmerge
	objcopy = arbor.etc.objcopy
	var parse = Parseur().parse
	
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
					result = result.concat(node, generateProducer(), " -- ", headings[j-1], "\n");
					noLink = false;
				}				
			}
			
			if (noLink && node != ''){
				result = result.concat(node, generateProducer(), "\n");
			}
		}
		
		return result;
	}
	// End read initial setting
	
	// Function to generate the producer node
	var generateProducer = function(){
		var gen = Math.floor((Math.random() * 100) + 1);
		
		return (gen < 35) ? ' {color: red, shape: dot}' : '';
	}
	// End generate producer

	var HalfViz = function(elt){
		var dom = $(elt)

		sys = arbor.ParticleSystem(35000, 512, 0.5)
		sys.renderer = Renderer("#viewport") // our newly created renderer will have its .init() method called shortly by sys...
		sys.screenPadding(20)

		var _ed = dom.find('#editor')
		var _code = dom.find('textarea')
		var _canvas = dom.find('#viewport').get(0)
		var _grabber = dom.find('#grabber')

		var _updateTimeout = null
		var _current = null // will be the id of the doc if it's been saved before
		var _editing = false // whether to undim the Save menu and prevent navigating away
		var _failures = null

		var that = {
			dashboard:Dashboard("#dashboard", sys),
			io:IO("#editor .io"),
			init:function(){
				$(window).resize(that.resize)
				that.resize()
				that.updateLayout(Math.max(1, $(window).width()-340))

				_code.keydown(that.typing)
				_grabber.bind('mousedown', that.grabbed)

				// $(that.io).bind('get', that.getLastDoc)
				$(that.io).bind('clear', that.newDoc)
				
				return that
			},
			/*
			getLastDoc:function(e){
			$.getJSON('library/'+e.id+'.json', function(doc){

			  // update the system parameters
			  if (doc.sys){
				sys.parameters(doc.sys)
				that.dashboard.update()
			  }

			  // modify the graph in the particle system
			  _code.val(doc.src)
			  that.updateGraph()
			  that.resize()
			  _editing = false
			})

			},

			*/

			newDoc:function(){
				var content = readInitial();
				// var content = "; some example nodes\nhello {color:red, label:HELLO}\nworld {color:orange}\n\n; some edges\nhello -> world {color:yellow}\nfoo -> bar {weight:5}\nbar -> baz {weight:2}"

				_code.val(content).focus()
				$.address.value("")
				that.updateGraph()
				that.resize()
				_editing = true
			},

			updateGraph:function(e){
				var src_txt = _code.val()
				var network = parse(src_txt)
				$.each(network.nodes, function(nname, ndata){
					if (ndata.label===undefined) ndata.label = nname
				})
				sys.merge(network)
				_updateTimeout = null
			},

			resize:function(){        
				var w = $(window).width() - 40
				var x = w - _ed.width()
				that.updateLayout(x)
				sys.renderer.redraw()
			},

			updateLayout:function(split){
				var w = dom.width()
				var h = _grabber.height()
				var split = split || _grabber.offset().left
				var splitW = _grabber.width()
				_grabber.css('left',split)

				var edW = w - split
				var edH = h
				_ed.css({width:edW, height:edH})
				if (split > w-20) 
					_ed.hide()
				else 
					_ed.show()

				var canvW = split - splitW
				var canvH = h
				_canvas.width = canvW
				_canvas.height = canvH
				sys.screenSize(canvW, canvH)
						
				_code.css({height:h-20,  width:edW-4, marginLeft:2})
			},

			grabbed:function(e){
				$(window).bind('mousemove', that.dragged)
				$(window).bind('mouseup', that.released)
				return false
			},
			
			dragged:function(e){
				var w = dom.width()
				that.updateLayout(Math.max(10, Math.min(e.pageX-10, w)) )
				sys.renderer.redraw()
				return false
			},
			
			released:function(e){
				$(window).unbind('mousemove', that.dragged)
				return false
			},
			
			typing:function(e){
				var c = e.keyCode
				if ($.inArray(c, [37, 38, 39, 40, 16])>=0){
				  return
			}

			if (!_editing){
				$.address.value("")
			}
			_editing = true

			if (_updateTimeout) clearTimeout(_updateTimeout)
				_updateTimeout = setTimeout(that.updateGraph, 900)
			}
		}

		return that.init()    
	}


	$(document).ready(function(){
		var mcp = HalfViz("#halfviz")    
	})

  
})()