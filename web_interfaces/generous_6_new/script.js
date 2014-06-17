$( document ).ready(function() {
	//$('#svgbasics').svg(drawCircle);
	
	var sizeLimit = 1300;
	var defaultWidth = sizeLimit;
	var defaultHeight = 700;
	var svgWidth =	window.innerWidth;
	var svgHeight = window.innerHeight;
	//$(function() {
		
		if(window.innerWidth>sizeLimit){
			console.log('size is ok');
			svgWidth =	window.innerWidth;
			svgHeight = window.innerHeight;
			
			$('#svgbasics').css ('width',window.innerWidth+'px').css('height',window.innerHeight+'px');   //{ width: 1400px; height: 700px; 
			$('#container').css ('width',window.innerWidth+'px').css('height',window.innerHeight+'px'); 

			$('#svgbasics').svg({onLoad: drawShapes});
		}
		else{
			console.log('resorting to default size');
			svgWidth =	defaultWidth;
			svgHeight = defaultHeight;
			
			
			
			$('#svgbasics').css ('width',defaultWidth+'px').css('height',defaultHeight+'px');   //{ width: 1400px; height: 700px; 
			$('#container').css ('width',defaultWidth+'px').css('height',defaultHeight+'px');  

			$('#svgbasics').svg({onLoad: drawShapes});
				 
		}
	//});

	
	// if(window.innerWidth>sizeLimit){
	// 	$('#container').css ('width',window.innerWidth+'px').css('height',window.innerHeight+'px'); 
	// 	console.log('size is ok')
	// }
	// else{
	// 	console.log('resorting to default size')
	// 	$('#container').css ('width',defaultWidth+'px').css('height',defaultHeight+'px');  
	// }
	
	var mouseOverCircleFill = '#F4EFDD';
	var circleFill= '#4a4c41';
	var circleStroke= '#F4EFDD';
	// var width = window.innerWidth;
	// var height = window.innerHeight;
	var mainRad = svgHeight/3;
	var shortRad = mainRad/2;
	var numElements = 204;//63.0;
	var numElementsInTranchOne  = 62;

	var numElementsInTranchTwo   = numElements-numElementsInTranchOne;
	var angleInc=360.0/numElements;
	
	var angleIncOne=360.0/numElementsInTranchOne;
	var angleIncTwo=360.0/numElementsInTranchTwo;

	var radiansInc=(2*Math.PI)/numElements;
	var radiansIncOne = (2*Math.PI)/numElementsInTranchOne;
	var radiansIncTwo = (2*Math.PI)/numElementsInTranchTwo;
	var ringSpacing = 40;
	var dragging;
	var mouseDownAngle=0;
	var previousAngle =0;
	var adjusted=0;
	var degree=0;
	
	function drawInitial(svg) {
		svg.circle(75, 75, 50, {fill: 'none', stroke: 'red', 'stroke-width': 3});
		var g = svg.group({stroke: 'black', 'stroke-width': 2});
		svg.line(g, 15, 75, 135, 75);
		svg.line(g, 75, 15, 75, 135);
	}
	var i =3;


	var colours = ['purple', 'red', 'orange', 'yellow', 'lime', 'green', 'blue', 'navy', 'black'];


	function drawShapes(svg){
		//setupRotate();
		//drawCircles(svgWidth/2,svgHeight/2,mainRad);

		
		$.post( "clean_box_log.php", { id: i } , function( data ) {

			//data.splice(0, 1);
			
			for (var i = 0; i <numElementsInTranchOne; i++) {

				addText(i*angleIncOne, data[i].boxID, data[i].boxDescription, data[i].boxContents, data[i].entities, data[i].date, i, data[i].correspondsTo, 1);
			}
			for (var i = 0; i <numElementsInTranchTwo; i++) {
				addText(i*angleIncTwo, data[numElementsInTranchOne+i].boxID, data[numElementsInTranchOne+i].boxDescription, data[numElementsInTranchOne+i].boxContents, data[numElementsInTranchOne+i].entities, data[numElementsInTranchOne+i].date, numElementsInTranchOne+i, data[numElementsInTranchOne+i].correspondsTo, 2);
			}

			
			bindCircles();

			
		}, "json");

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


	function bindCircles(){
		
		$(".edgeCircle").on('mouseenter', function(){
			//$(".edgeCircle").not($('#new3')).attr('stroke-opacity', '0.1');
			//console.log($(this).attr('id'),' ' ,$(this).attr('class'));


			
			
			
			var id = $(this).attr('id');
			
			

			var num = $(this).attr('id').substr(4,$(this).attr('id').length-1);
			
			var connections = $('#buddy'+num).data("correspondsTo");
			
			var thisTranche = $('#buddy'+num).data("tranch");
			console.log($('#buddy'+num).data('boxID'),' ' ,$('#buddy'+num).data('boxContents'));
			///ids are sequential but I'll need to subtract 62 from the outer ring before multiplying by the multiplier
			var combinedConnections = [];
			
			for (var key in connections) {
				//console.log(key);
				var value = connections[key];
				//console.log(value);
				for(var i=0;i<value.length;i++){
					combinedConnections.push(value[i]);
				}
			}
			
			var thisAngle = 0;
			
			//if the index of this element means it in the second row then subtract the number of elements in the first row so we can calculate the angle
			
			//var angle1Tranch = 0;
			if(num>=numElementsInTranchOne){
				thisAngle=num-numElementsInTranchOne;
			}
			else{
				thisAngle = num;
			}
			
			if(thisTranche==1){
				var point1 = (Math.PI *0.5) -(thisAngle*radiansIncOne);
			}
			else if(thisTranche==2){
				var point1 = (Math.PI *0.5) -(thisAngle*radiansIncTwo);
			}
			
			$('#rect'+num).attr('fill', mouseOverCircleFill);
			$('#text'+num).attr('fill', 'white');

			for(var i=0;i<combinedConnections.length;i++){
				var hackedConnectionIndex = combinedConnections[i]-1;
				$('#rect'+hackedConnectionIndex).attr('fill', mouseOverCircleFill);
				$('#text'+hackedConnectionIndex).attr('visibility', 'visible');
				//$('#rect'+combinedConnections[i]).attr('fill-opacity', 1);
				//.attr('stroke-opacity', '0.1');
				
				var thatAngle = 0;
				var thatTranche = $('#buddy'+combinedConnections[i]).data("tranch");
				
				if(combinedConnections[i]>=numElementsInTranchOne){
					thatAngle=combinedConnections[i]-numElementsInTranchOne;

				}
				else{
					thatAngle = combinedConnections[i];
				}
				//hack for index
				thatAngle-=1;
				if(thatTranche==1){
					var point2 =  (Math.PI *0.5) - (thatAngle*radiansIncOne);
				}
				else if (thatTranche==2){
					var point2 =  (Math.PI *0.5) - (thatAngle*radiansIncTwo);
				}
				

				
				
				//console.log('curve from ',thisAngle ,' to ',thatAngle, ' that angle is in tranche ',thatTranche);
				edgeToEdgeCurve(point1 , point2, thisTranche, thatTranche, 1);
			}
			//working call
//			$(".edgeCircle:not(#" +id+   ")").stop().fadeTo('fast', 0.1)    ;//attr('stroke-opacity', '0.6');
			
			var ids = "#rect"+num+",";
			for(var i=0;i<combinedConnections.length-1;i++){
				var hackedConnectionIndex = combinedConnections[i]-1;
				ids+= '#rect'+hackedConnectionIndex;//combinedConnections[i];
				ids+=',';

			}
			var hackedConnectionIndex = combinedConnections[combinedConnections.length-1]-1;
			ids+= '#rect'+hackedConnectionIndex;
			//console.log(ids);
			
			//$(".edgeCircle:not(" +ids+   ")").stop().fadeTo('fast', 0.1)    ;//attr('stroke-opacity', '0.6');
			$(".edgeCircle:not(" +ids+   ")").stop().animate({
    			opacity: 0.6,
  				}, 200, function() {
			    // Animation complete.
			 });

			var myText = '<span style="font-size:14pt; font-weight:bold; color:white"> Box ';
			myText+=$('#buddy'+num).data('boxID');
			//myText+=" : ";
			// var description = $('#buddy'+num).data('boxDescription');

			// if(description!='<'){
			// 	myText+=description;
			// }
			
			
			myText+="</span><br>";
			myText+="<br>";
			// myText+=thisAngle;
			// myText+=', radiansIncTwo : ';
			// myText+=radiansIncTwo;
			// myText+=', radiansIncTwo : ';
			for (var key in connections) {
				var value = connections[key];
				myText+=key;
				myText+=" can also be found in boxes ";

				for (var i = 0; i<value.length; i++) {
					var hackedConnectionIndex = $('#buddy'+value[i]).data("boxID")-1;
					myText+= hackedConnectionIndex;
					myText+=', ';
				};
				
				myText+="<br>";
				myText+="<br>";
			}
			$('#invisible').css('visiblity','visible').html(myText);
			//edgeToEdgeCurve(point1 , point2, 3);
			$(this).attr('fill', 'red');

		});
		$(".edgeCircle").on('mouseleave', function(){
			//$(".edgeCircle").not($('#new3')).attr('stroke-opacity', '0.1');
			//console.log($(this).attr('id'),' ' ,$(this).attr('class'));
			$(".edgeCircle").attr('fill', circleFill);
			//$(".edgeText").attr('visibility', 'hidden');
			$(".edgeText").attr('fill', '#b2a889');
			var id = $(this).attr('id');
			
			$(".edgeCircle:not(#"+id+")").stop().stop().animate({
    			opacity: 1,
  				}, 200, function() {
			    // Animation complete.
			 });
			$(".arcid").remove();
			
		});

		
	}
			

	function addText(angle, boxID, boxDescription, boxContents, entities, date, index, correspondsTo, tranch){



		var svg = $('#svgbasics').svg('get');
		//g2 = svg.group(g1, {transform: 'rotate(-45)'}); 
			var radShift;
			if(tranch ==1){
				radShift = 0;
			}
			else {
				radShift = ringSpacing;
			}
			var g1 = svg.group( {transform: 'translate('+(svgWidth/2)+','+ svgHeight/2 +')'}); 
			var g2 = svg.group(g1,({transform: 'rotate('+angle+')'})); 
			var g3 = svg.group(g2,{transform: 'translate('+(mainRad+radShift)+',0)'}); 
			//you can apply one formation on top of another!
			var shortText ="test";


			meat = index.toString();
			if(typeof(meat)!='undefined'){
			//console.log(meat);
			shortText = meat;//"Test";
			}
			//radius needs to 
			//console.log("#"+(index));
//			svg.circle(g3, 0,0, 5, {id: "#"+(index),  class:'edgeCircle', fill: circleFill, stroke: circleStroke, 'stroke-width': 2});
			svg.rect(g3, 0,-4, 15,8, {id: 'rect'+(index),  class:'edgeCircle', fill: circleFill, stroke: circleStroke, 'stroke-width': 2});
			
			svg.text(g3, 20, 0, boxID, {id: 'text'+(index), class:'edgeText', fontSize: 12, fontFamily: 'Verdana', stroke: 'none', fill: '#b2a889'});

//			svg.text(g3, 20, 0, boxID, {id: 'text'+(index), class:'edgeText', fontSize: 12, fontFamily: 'Verdana', stroke: 'none', visibility: 'hidden', fill: '#b2a889'});
			// if(index>4){
			// flip($('#text'+(index)));	
			// }

			var new_element = "<div id='buddy"+index+"' class='buddy'><p> </p></div>" ;

			
	 		$("#container").append(new_element);// "<p>Test</p>" );
			
		
			 $("#buddy"+index).data("boxID",boxID);
			 $("#buddy"+index).data("boxDescription",boxDescription);
			 $("#buddy"+index).data("boxContents",boxContents);
			 $("#buddy"+index).data("entities",entities);
			 $("#buddy"+index).data("date",date);
			 $("#buddy"+index).data("correspondsTo",correspondsTo);
			 $("#buddy"+index).data("tranch",tranch);
			
			

	}

	

	

	function edgeToEdgeCurve(angle1,angle2, thisTranche, thatTranche,weight){
		var svg = $('#svgbasics').svg('get');
		
		if(thisTranche==1){
			var point1= getCircleEdgePoint(svgWidth/2,svgHeight/2,mainRad,angle1);
			var controlPoint1= getCircleEdgePoint(svgWidth/2,svgHeight/2,shortRad,angle1);
		}else if(thisTranche ==2 ){
			var point1= getCircleEdgePoint(svgWidth/2,svgHeight/2,mainRad+ringSpacing,angle1);
			var controlPoint1= getCircleEdgePoint(svgWidth/2,svgHeight/2,shortRad,angle1);
		}
		
		if(thatTranche==1){
			var point2= getCircleEdgePoint(svgWidth/2,svgHeight/2,mainRad,angle2);
			var controlPoint2= getCircleEdgePoint(svgWidth/2,svgHeight/2,shortRad,angle2);
		}else if(thatTranche ==2 ){
			var point2= getCircleEdgePoint(svgWidth/2,svgHeight/2,mainRad+ringSpacing,angle2);
			var controlPoint2= getCircleEdgePoint(svgWidth/2,svgHeight/2,shortRad,angle2);
		}

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

	
	function flip(target){

		target.css('-moz-transform: scale(-1, 1)');
		target.css('-webkit-transform: scale(-1, 1)');
		target.css('-o-transform: scale(-1, 1)');
		target.css('-ms-transform: scale(-1, 1)');
		target.css('transform: scale(-1, 1)');

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
});