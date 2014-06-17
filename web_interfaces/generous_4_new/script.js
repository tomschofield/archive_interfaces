$( document ).ready(function() {
	var image_directory  = '../BloodAxeBooks_800/';
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
  	function getDB(sortType){
		var sort_type = "date";

			$.post( "get_book_db.php", {  sort: sort_type } , function( data ) {
			//console.log(data);
				data.sort(sortType);
				console.log("adding elements");
		 	for (var i = 0; i < data.length; i++) {

		 		
		 		var dir_plus_name = image_directory + data[i].fname;
		 		console.log((data[i]));
		 		//console.log(dir_plus_name.length;
		 		var new_element = "<div id='new"+i+"' class='book'><p> <img src='"+dir_plus_name+"' width = '200'>" + "</p></div>" ;
		 		$("#container").add(new_element).appendTo( "#container");

			}
			console.log("adding callback");
			for (var i = 0; i < data.length; i++) {
				console.log(i);
				$(document).on( "mouseenter", "#new"+i, function(event){
		 			
			        var this_id = $(this).attr('id');
			        var index =  this_id.substring(3,this_id.length);
			        var p = $( "#new"+index);
		          	var position = p.position();
		          	var myheight =p.find("img").css("height");

		          	var strippedHeight = parseInt( myheight.substring(0,myheight.length-2) );
		          	var creators = "";
		          	console.log("position ",position);
		          	for (var j = 0; j < data[index].creators.length; j++) {
		          		creators+=data[index].creators[j];
		          		creators+="<br>";
		          	};
		          	var content = '<font size="3pt">'+data[index].title+ '</font> '+ data[index].date+ '<br>'+creators;

		          	$("#secret").stop().css('visibility','visible').css('top',position.top+strippedHeight).css('left',position.left).css('overflow','scroll').css('height','100px').html(content).hide().fadeIn( "slow").on( "mouseleave", "#new"+i, function(event){
		 			
			        var this_id = $(this).attr('id');
			        var index =  this_id.substring(3,this_id.length);

		          	$("#secret").stop().fadeOut( "slow",function(){
		          		$("this").css("visibility","hidden");
		          	});

		 		});;

		 		});
			}
			}, "json");
		}

  getDB(compareDateAscend);
  var mouseOverColour = "black";
  var mouseLeaveColour = "#F4EFDD";
 
$("#bydateAscend").on("click",function () {
	$("#container").empty();
	$("#container").add('<div id= "secret"></div>').appendTo( "#container");
	getDB(compareDateAscend);
});
$("#bydateDescend").on("click",function () {
	$("#container").empty();
	$("#container").add('<div id= "secret"></div>').appendTo( "#container");
	getDB(compareDateDescend);
});
$("#bytitleAscend").on("click",function () {
	$("#container").empty();
	$("#container").add('<div id= "secret"></div>').appendTo( "#container");
	getDB(compareTitleAscend);
});
$("#bytitleDescend").on("click",function () {
	$("#container").empty();
	$("#container").add('<div id= "secret"></div>').appendTo( "#container");
	getDB(compareTitleDescend);
});


  
  $("#bydateAscend").on("mouseover",function () {
  	$(this).css("color",mouseOverColour);
  });
  $("#bydateAscend").on("mouseleave",function () {
  	$(this).css("color",mouseLeaveColour);
  });

    $("#bydateDescend").on("mouseover",function () {
  	$(this).css("color",mouseOverColour);
  });
  $("#bydateDescend").on("mouseleave",function () {
  	$(this).css("color",mouseLeaveColour);
  });

    $("#bytitleAscend").on("mouseover",function () {
  	$(this).css("color",mouseOverColour);
  });
  $("#bytitleAscend").on("mouseleave",function () {
  	$(this).css("color",mouseLeaveColour);
  });

    $("#bytitleDescend").on("mouseover",function () {
  	$(this).css("color",mouseOverColour);
  });
  $("#bytitleDescend").on("mouseleave",function () {
  	$(this).css("color",mouseLeaveColour);
  });

  // $(".sort").on("mouseover" ,function (){
  // 	$('document').css( 'cursor', 'pointer' );
  // });

  $("h1").on("mouseover",function () {
  	$(".sort").css("visibility","visible").hide().fadeIn(1000);
  });
  $(".sort").on("mouseleave",function () {
  	$(".sort").css("visibility","hidden");
  	$('selector').css( 'cursor', 'auto' );
  });


});

