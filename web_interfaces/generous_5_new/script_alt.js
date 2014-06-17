$( document ).ready(function() {
	var image_directory  = '../BloodAxeBooks_thumbs_new/';
	var    large_image_directory  = '../BloodAxeBooks_400/';
	var width = window.innerWidth;
    var height = window.innerHeight;
    
    function getOriginalWidthOfImg(img_element) {
	    var t = new Image();
	    t.src = (img_element.getAttribute ? img_element.getAttribute("src") : false) || img_element.src;
	    return t.width;
	}

	function compareDateAscend(a,b) {
	  if (a.date < b.date)
	     return -1;
	  if (a.date > b.date)
	    return 1;
	  return 0;
	}
	function compareDateDescend(a,b) {
	  if (a.date < b.date)
	     return 1;
	  if (a.date > b.date)
	    return -1;
	  return 0;
	}
	function compareTitleAscend(a,b) {
	  if (a.title < b.title)
	     return -1;
	  if (a.title > b.title)
	    return 1;
	  return 0;
	}
	function compareTitleDescend(a,b) {
	  if (a.title < b.title)
	     return 1;
	  if (a.title > b.title)
	    return -1;
	  return 0;
	}
	String.prototype.width = function(font) {
	  var f = font || '32px arial',
	      o = $('<div>' + this + '</div>')
	            .css({'position': 'absolute', 'float': 'left', 'white-space': 'nowrap', 'visibility': 'hidden', 'font': f})
	            .appendTo($('body')),
	      w = o.width();

	  o.remove();

	  return w;
	}
	function transformAndRotate(target, w, h, rot){
		target.css('-moz-transform-origin', w+' '+h);
        target.css('-moz-transform', 'rotate(' + rot + 'deg)');

        target.css('-webkit-transform-origin', w+' '+h);
        target.css('-webkit-transform', 'rotate(' + rot + 'deg)');

        target.css('-o-transform-origin', w+' '+h);
        target.css('-o-transform', 'rotate(' + rot + 'deg)');
        
        target.css('-ms-transform-origin', w+' '+h);
        target.css('-ms-transform', 'rotate(' + rot + 'deg)');
	}
  	function getDB(sortType, layout){
		var sort_type = "date";

			$.post( "get_book_db.php", {  sort: sort_type } , function( data ) {
			//console.log(data);
				data.sort(sortType);
				//console.log("adding elements");
				var yearCounter = 0;
				var xCount = 0;
				var pDate="0";
				var xShift =50;
				var yShift =80;
				var imageWidth = 30;
		 		var rowHeight = 50;
		 		var colWidth = 50;
		 		var shiftFactor =34;
		 		var x = xShift;
		 		var y =  yShift;
		 		//start off with the first date label
		 		// var dateLabel = "<div id='dateLabel_"+yearCounter+"' class='dateLabel'><p> "+ data[0].date+ " </p></div>" ;
			 	// $("#container").add(dateLabel).css('left', 10).css('top', y).data('date',data[0].date ).appendTo( "#container");
		 		for (var i = 0; i < data.length; i++) {
		 			//if this date doesn't match the previous one then add a label
		 			var dir_plus_name = image_directory + data[i].fname;
			 		//console.log((data[i]));
			 		
			 		
			 		var yPos = data[i].date-1978;
			 		
		 			if(data[i].date.trim()!=pDate.trim() ){
			 			
			 			
			 			
			 			var dateLabel = "<div id='dateLabel_"+yearCounter+"' class='dateLabel'><p> "+ data[i].date+ " </p></div>" ;
			 			
			 			var labelX = -5+xShift + (colWidth*(data[i].date-1978 ) );
			 			
			 			$("#container").add(dateLabel).css('left', labelX).css('top', 15+yShift).data('date',data[i].date ).appendTo( "#container");
			 			//if(i>0){
			 				//transformAndRotate(target ,x,y, 90);
			 			yearCounter++;
			 		//}
			 			
			 			xCount = 0;
			 			console.log("new date is ",data[i].date, 'yearCounter', yearCounter );
			 		}
			 		if(layout=='sineWave'){
			 			 x = xShift + (colWidth*(data[i].date-1978 ) )+(10*Math.sin(xCount) ) ;//(xCount*imageWidth);
			 		}
			 		else{
			 			 x = xShift + (colWidth*(data[i].date-1978 ) );
			 		}	
			 		var textWidth = data[i].date .width();//$('#dateLabel_'+yearCounter).width();
			 		console.log('textWidth ',textWidth);
					 y =  textWidth + yShift +(rowHeight*xCount);// 0;//yShift+(yPos*rowHeight);

			 		var new_element = "<div id='new"+i+"' class='book'><p> <img src='"+dir_plus_name+"' width = '"+imageWidth+"'>" + "</p></div>" ;
			 		//console.log(data[i].date);
			 		$("#container").add(new_element).css('left', x).css('top', y).data('date',data[i].date ).data('fname',data[i].fname ).appendTo( "#container");
			 		//var new_secret_element = "<div id='secret_"+ data.id+"' class='secret'><p>"+ content + " </p></div>" ;
			 		xCount++;
			 		pDate = data[i].date;
			 		


				}



				//console.log("adding callback");
				for (var i = 0; i < data.length; i++) {
				//console.log(i);
					$(document).on( "mouseenter", "#new"+i, function(event){
					    var this_id = $(this).attr('id');
					    var index =  this_id.substring(3,this_id.length);
					    var p = $( "#new"+index);
					  	var position = p.position();
					  	var myheight =p.find("img").css("height");

					  	var strippedHeight = parseInt( myheight.substring(0,myheight.length-2) );
					  	var creators = "";
					  	console.log('this_id ',$(this).data('date'));
					});

					$(document).on( "click", "#new"+i, function(event){
						
						//$("#secret").data("masterId",$(this).attr('id') );
						var source = $(this).data('fname');
						var myElement = $(this);
						var largeSource =large_image_directory + source;
						console.log('largeSource ',source);
						
						$("#secret").find("img").attr("src", largeSource).load(function() { 

							console.log("real widht ",this.width)
							//console.log('width ',awidth);//$("#secret").find("img").width());
							var x = (width*0.5)-(0.5*this.width);
							var y =100;
							console.log('x ',x);
							//$("#secret").
							$('#secret').css("visibility","visible").css('left', x).css('top', y).stop().hide().fadeIn(2000);
						          	var creators = "";
					          	//console.log("position ",position);	
					          	var this_id = $(myElement).attr('id');
					          	console.log("this is ",this_id);
						        var index =  this_id.substring(3,this_id.length);

					          	for (var j = 0; j < data[index].creators.length; j++) {
					          		creators+=data[index].creators[j];
					          		creators+="<br>";
					          	};

					          	var textX = (width*0.5)+(0.5*this.width);
					          	var content = '<font size="3pt">'+data[index].title+ '</font> '+ data[index].date+ '<br>'+creators;
					          	$("#label").css('visibility','visible').css('top',y).css('left',textX).css('overflow','scroll').css('height','100px').html(content).stop().hide().fadeIn(2000);
							//$.dimBackground();
						});

						////////////add a lable
						// var this_id = $(this).attr('id');
			   //      	var index =  this_id.substring(3,this_id.length);
						// var p = $( "#new"+index);
			   //        	var position = p.position();

						// var myheight =p.find("img").css("height");

			   //        	var strippedHeight = parseInt( myheight.substring(0,myheight.length-2) );


			          // 	$("#label").stop().css('visibility','visible').css('top',position.top+strippedHeight).css('left',position.left).css('overflow','scroll').css('height','100px').html(content).hide().fadeIn( "slow").on( "mouseleave", "#new"+i, function(event){
				 			
					        // var this_id = $(this).attr('id');
					        // var index =  this_id.substring(3,this_id.length);

				         //  	$("#label").stop().fadeOut( "slow",function(){
				         //  		$("this").css("visibility","hidden");
				         //  	});
			          // 	});


					});

				}
			}, "json");
		}

  getDB(compareDateAscend, "sineWave");
  // $("body").click(function(){
  //   $("#secret").fadeOut(1000,function(){
  //   	$.undim();
  //   });
  // });
  $("#secret").click(function(){
  	console.log("click");
    $(this).fadeOut(1000,function(){
    	//$.undim();
    	$(this).css("visibility","hidden").css("left",-1000);
    	
    });
    //$("#label").fadeOut(1000,function(){
    	$("#label").css('visibility','hidden').css("left",-1000);

   // });


  });
  	$( window ).resize(function() {
  			 width = window.innerWidth;
     height = window.innerHeight;
	});
});


  
  // $("#bydateAscend").on("mouseover",function () {
  // 	$(this).css("color",mouseOverColour);
  // });
  // $("#bydateAscend").on("mouseleave",function () {
  // 	$(this).css("color",mouseLeaveColour);
  // });

  //   $("#bydateDescend").on("mouseover",function () {
  // 	$(this).css("color",mouseOverColour);
  // });
  // $("#bydateDescend").on("mouseleave",function () {
  // 	$(this).css("color",mouseLeaveColour);
  // });

  //   $("#bytitleAscend").on("mouseover",function () {
  // 	$(this).css("color",mouseOverColour);
  // });
  // $("#bytitleAscend").on("mouseleave",function () {
  // 	$(this).css("color",mouseLeaveColour);
  // });

  //   $("#bytitleDescend").on("mouseover",function () {
  // 	$(this).css("color",mouseOverColour);
  // });
  // $("#bytitleDescend").on("mouseleave",function () {
  // 	$(this).css("color",mouseLeaveColour);
  // });

  // // $(".sort").on("mouseover" ,function (){
  // // 	$('document').css( 'cursor', 'pointer' );
  // // });

  // $("h1").on("mouseover",function () {
  // 	$(".sort").css("visibility","visible").hide().fadeIn(1000);
  // });
  // $(".sort").on("mouseleave",function () {
  // 	$(".sort").css("visibility","hidden");
  // 	$('selector').css( 'cursor', 'auto' );
  // });


// });

