//$( document ).ready(function() {
	//$('#svgbasics').svg(drawCircle);
$(function() {
	$('#svgbasics').svg({onLoad: drawShapes});
});
var svgWidth =	1400;//window.innerWidth;
var svgHeight = 800;//window.innerHeight;

    // var width = window.innerWidth;
    // var height = window.innerHeight;
var mainRad = svgHeight/5;
var shortRad = mainRad/2;
var numElements = 63.0;
var angleInc=360.0/numElements;
var radiansInc=(2*Math.PI)/numElements;
var dragging;
var mouseDownAngle=0;
var previousAngle =0;
var adjusted=0;
var degree=0;
var mappedNumbers =new Array(1,2,3,4,5,6,11,12,13,14,15,16,17,20,22,23,24,25,26,27,28,29,30,31,32,33,34,36,38,39,40,41,42,44,47,49,50,51,52,53,54,55,59,60,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80);
	function drawInitial(svg) {
		svg.circle(75, 75, 50, {fill: 'none', stroke: 'red', 'stroke-width': 3});
		var g = svg.group({stroke: 'black', 'stroke-width': 2});
		svg.line(g, 15, 75, 135, 75);
		svg.line(g, 75, 15, 75, 135);
	}
var i =3;
	
	
	var colours = ['purple', 'red', 'orange', 'yellow', 'lime', 'green', 'blue', 'navy', 'black'];


	function drawShapes(svg){
		setupRotate();
		drawCircles(svgWidth/2,svgHeight/2,mainRad);


		for (var i = 0; i <numElements; i+=1.0) {

			$.post( "get_record.php", { id: i } , function( data ) {
				
				addText(data.id*angleInc,data.labels.labels, data.id, data.summary_contents.summary_contents,data.boxref.boxref, data.data_pairs,i);
				if(i==numElements){
					//compareDataAndDrawCurves();
				}
			}, "json");
		};
			$("#svgbasics").click(function() {
   			//compareDataAndDrawCurves();
   			//drawConnections();
		});
	}
	function rotateWholeDiv(rotation){
		
				$('#svgbasics').css({'-webkit-transform' : 'rotate('+ rotation +'deg)',
                 '-moz-transform' : 'rotate('+ rotation +'deg)',
                 '-ms-transform' : 'rotate('+ rotation +'deg)',
                 'transform' : 'rotate('+ rotation +'deg)'});
	}




	function addText(angle, text, myid, meat, box_ref, data_pairs,index){



		var svg = $('#svgbasics').svg('get');
		//g2 = svg.group(g1, {transform: 'rotate(-45)'}); 
			
			var g1 = svg.group( {transform: 'translate('+(svgWidth/2)+','+ svgHeight/2 +')'}); 
			var g2 = svg.group(g1,({transform: 'rotate('+angle+')'})); 
			var g3 = svg.group(g2,{transform: 'translate('+(mainRad+5)+',0)'}); 
			//you can apply one formation on top of another!
			var shortText ="";
			var lengthLimit =25;
			var displayText = mappedNumbers[myid].toString();
			displayText+=':';
			displayText+=text;
			if(displayText.length>lengthLimit){
				shortText=displayText.substring(0,lengthLimit)+"...";
			}
			else{
				shortText =displayText;				
			}

			svg.text(g3, 0, 0, shortText, {id: myid, fontSize: 12, fontFamily: 'Verdana', stroke: 'none', fill: '#b2a889'});//.on('click',function(){ console.log("thes") }); 	
			$("#"+(myid)).data("id",myid);
			$("#"+(myid)).data("longText",text);
			$("#"+(myid)).data("shortText",shortText);
			$("#"+(myid)).data("meat",meat);
			$("#"+(myid)).data("box_ref",box_ref);
			$("#"+(myid)).data("data_pairs",data_pairs);
			
			//if mouse over box item
			$("#"+(myid)).on('mouseenter',function(){ 
				$(this).css("fill","white");
				
				///lengthen the title
				$(this).text($(this).data("longText"));
				var astring ='';

				if(typeof $(this).data("data_pairs")!='undefined' ){
					var str1 = $(this).data("data_pairs");
					//var pairs = str1.split(",");
					//var i=0;
					var title = $(this).data('id');
					astring+='<span style= "color:white;font-weight:bold">' ;
					astring+='Box #';
					astring+=mappedNumbers[title].toString() ;
					astring+=' has the following connected items:';
					astring+='</span>'
					for(var i=0;i<data_pairs.length;i++){
						$.each( data_pairs[i], function( key, val ) {

							
	    					astring+='<p id = "data_pair_'+ i  +'">';

	    					astring+='<span style= "color:white">' ;//<span style="color:blue">
							astring+=key;//pairs[i];//aPair[0];
							astring+='</span>'
							//astring+=' ';
							$.each(val, function(key, val){
								var myKeys = Object.keys(val);
    							$.each(val, function(key, val){
    								astring+=', ';
									astring+=mappedNumbers[key-1].toString();
								});
    						});
							
							astring+='<br>';
							astring+='</p>';
	 		 			});
					}
					
				
					$("#invisible").html(astring).css("visibility","visible");
					
					
					for (var i = 0; i < data_pairs.length; i++) {
						//console.log(' pairs.length ', pairs.length ,pairs[0]);
						$('#data_pair_'+ i).on('mouseenter',function(){ 
							console.log($(this).find('p').text);
							//var aPair = pairs[i].split(":");
							///$('#'+aPair[1]).css('stroke', 'red');
						});
					}
				}
				drawConnectionsForIndex(myid);
			});
			$("#"+(myid)).on('mouseleave',function(){ 
				$(this).css("fill",'#b2a889');
				$(this).text($(this).data("shortText"));
				$(".arcid").remove();
				//$("#invisible").css("visibility","hidden");

			});
	}

	function drawConnectionsForIndex(i){
		
		var connected_pairs = $("#"+i).data("data_pairs");
		//console.log('making connections at ', i);
		if(typeof connected_pairs!='undefined' ){
			
			for(var j=0;j<connected_pairs.length;j++){
				var count =0;
				$.each( connected_pairs[j], function( key, val ) {
    				
    				$.each(val, function(key, val){
    					
    					var myKeys = Object.keys(val);
    					
    					$.each(val, function(key, val){
    						
    						edgeToEdgeCurve( (Math.PI *0.5) - $("#"+i).data("id")*radiansInc, (Math.PI *0.5) - (key-1)*radiansInc, val);
    					});

    				});
	 		 	});
			}
		}
	}
	
	function drawConnections(){
		for (var i = 0; i < numElements; i++) {
			var connected_pairs = $("#"+i).data("data_pairs");
			//console.log('making connections at ', i);
			if(typeof connected_pairs!='undefined' ){
				for(var j=0;j<connected_pairs.length;j++){
					$.each( connected_pairs[j], function( key, val ) {
	    				
	    				$.each(val, function(key1, val1){
	    					
	    					edgeToEdgeCurve(   $("#"+i).data("id")*radiansInc,  key1*radiansInc, 3);
	    				});
		 		 	});
				}
			}
		}
	}
	
	function edgeToEdgeCurve(angle1,angle2, weight){
		var svg = $('#svgbasics').svg('get');
		
		
		var point1= getCircleEdgePoint(svgWidth/2,svgHeight/2,mainRad,angle1);
		var controlPoint1= getCircleEdgePoint(svgWidth/2,svgHeight/2,shortRad,angle1);
		var point2= getCircleEdgePoint(svgWidth/2,svgHeight/2,mainRad,angle2);
		var controlPoint2= getCircleEdgePoint(svgWidth/2,svgHeight/2,shortRad,angle2);

		drawArc(point1[0],point1[1],controlPoint1[0],controlPoint1[1], controlPoint2[0],controlPoint2[1], point2[0],point2[1],weight);
	}
	function drawLines(){
		var svg = $('#svgbasics').svg('get');
		//g2 = svg.group(g1, {transform: 'rotate(-45)'}); 

			var g1 = svg.group({transform: 'translate('+(svgWidth/2)+','+ svgHeight/2 +')'}); 
			//you can apply one formation on top of another!
			var g3 = svg.group(g1, {fill: 'none', stroke: 'blue', strokeWidth: 3}); 
			var g4 = svg.group(g3, ({transform: 'rotate(-45)'})); 
			svg.line(g4, 0, 0, mainRad, 0); 
			
			svg.text(g4, 0, 0, 'An example', {fontSize: 20, fontFamily: 'Verdana', stroke: 'none', fill: 'black'}); 
	}

	function setupRotate(){
		$(function() {
			var target = $('#svgbasics');
			target.mousedown(function(e) {
				//var radians = Math.atan2(mouse_x - svgWidth/2, mouse_y - svgHeight/2);
		        //var currentAngle = (radians * (180 / Math.PI) * -1) + 90;
		        var mouse_x = e.pageX;
		        var mouse_y = e.pageY;

		        var centre_x = svgWidth/2;
		        var centre_y = svgHeight/2;
		        var radians = Math.atan2(mouse_x - svgWidth/2, mouse_y - svgHeight/2);
		        mouseDownAngle = (radians * (180 / Math.PI) * -1) + 90;


				dragging = true;

			})
			$(document).mouseup(function(e) {
		   		dragging = false;
		   		var mouse_x = e.pageX;
		        var mouse_y = e.pageY;

		        var centre_x = svgWidth/2;
		        var centre_y = svgHeight/2;
		        var radians = Math.atan2(mouse_x - svgWidth/2, mouse_y - svgHeight/2);
		        previousAngle = adjusted;//(radians * (180 / Math.PI) * -1) + 90;
		   		//currentAngle +=degree;
			})
			$(document).mousemove(function(e) {
		    	if (dragging) {

		        var mouse_x = e.pageX;
		        var mouse_y = e.pageY;

		        var centre_x = svgWidth/2;
		        var centre_y = svgHeight/2;
		        var radians = Math.atan2(mouse_x - svgWidth/2, mouse_y - svgHeight/2);
		        degree = (radians * (180 / Math.PI) * -1) + 90;
		       	
		       	adjusted =(degree-mouseDownAngle)+previousAngle;

		        target.css('-moz-transform-origin', svgWidth/2+' '+svgHeight/2);
		        target.css('-moz-transform', 'rotate(' + adjusted + 'deg)');

		        target.css('-webkit-transform-origin', svgWidth/2+' '+svgHeight/2);
		        target.css('-webkit-transform', 'rotate(' + adjusted + 'deg)');

		        target.css('-o-transform-origin', svgWidth/2+' '+svgHeight/2);
		        target.css('-o-transform', 'rotate(' + adjusted + 'deg)');
		        
		        target.css('-ms-transform-origin', svgWidth/2+' '+svgHeight/2);
		        target.css('-ms-transform', 'rotate(' + adjusted + 'deg)');
		        
		   		}
			})
		})
	}

	//return the point
	function getCircleEdgePoint(x,y,rad,radians){
		///vars are circle centre x,y radius of circle and degrees
		var xpos = rad * Math.sin(radians);
		var ypos = rad * Math.cos(radians);

		var circleEdgeX= x+xpos;
		var circleEdgeY= y+ypos;
		var coords=new Array(circleEdgeX,circleEdgeY);
		return coords;

	}

	function drawArc(x,y,x1, y1, x2, y2, x3, y3, weight){
		//variables are, in order, start xy, control point 1 xy, control point 2 xy, end point xy
		var svg = $('#svgbasics').svg('get');
		
		var path = svg.createPath(); 
		svg.path( path.move(x, y).curveC(x1, y1, x2, y2, x3, y3, 0),  
    	{class: 'arcid', fill: 'none', stroke: '#F7EFE8', strokeWidth: weight});

	}
	function drawCircles(x,y,z){
		var svg = $('#svgbasics').svg('get');
		$('#svgbasics').css("border","none");
		svg.circle(x,y,z,
			{class:"circle", fill: "none", stroke: "grey",
				'stroke-width': 1});
	}
		

	function random(range) {
		return Math.floor(Math.random() * range);
	}
//});