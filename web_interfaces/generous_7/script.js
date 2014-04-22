

$( document ).ready(function() {
	var image_directory  = '/BloodAxeBooks_thumbs/';
	var large_image_directory  = '/BloodAxeBooks_800/';
	console.log("getting fbanes");
	$.post( "get_file_list.php", { imagedir: image_directory } , function( data ) {
		//console.log(data.fnames);
		for (var i = 0; i < data.fnames.length; i++) {
			var dynamic = "image_id_"+i.toString();
			var img = $('<img id='+ dynamic+' >'); //Equivalent: $(document.createElement('img'))
			img.attr('src', '/'+data.fnames[i]);
			//console.log('/'+data.fnames[i]);
			img.appendTo('#image_container');
			$('#'+dynamic).on( "click", function(event){
				console.log($(this).attr('src'));
				console.log("clicked");
//   /BloodAxeBooks_thumbs/BXB821-ASTESS-01.jpg
				var exploded = $(this).attr('src').split("/");
				console.log(exploded[2]);
				postAndLoad(exploded[2]);   //$(this).attr('src'));
			});
			
		};
		


	}, "json");

	var tId,pjs,cnt=0;

	var pic_width ;// = 1002;
	var pic_height ;//= 715;
	var i = 0;
	var count = 0;
	var idCount = 0;
	
	var fileList = ['holub1.png','dear.png','pic.png'];
	var idList = ['__processing0','__processing1','__processing2'];
	
	var isFirstTime =true;

	$('#refresh').click(function(){
	
    	setup();
	});
	function postAndLoad(fname){
		if(!isFirstTime){
			//$('#'+idList[idCount]).remove();
			$('#__processing'+idCount.toString()).remove();
			idCount++;
		}
		isFirstTime =false;
		$.post( "get_image_size.php", { id: i, filename: fname, imagedir: large_image_directory } , function( data ) {
			//console.log(fname);
			pic_width = data.mywidth;
			pic_height = data.myheight;
			console.log("width and height"+pic_width+" "+pic_height);

			var canvasRef = document.createElement('canvas');
	  		var p = Processing.loadSketchFromSources(canvasRef, ['/generous_7/burn_image_viewer_4.pde']);
	  		
	  		 $('#content').append(canvasRef);
	  		 

	  		 //wait until the processing sketch has loaded to adjust the css width etc
		  	var timer = 0,
		    timeout = 3000,
		    mem = setInterval(function () {
		    var sketch = Processing.getInstanceById('__processing'+idCount.toString());//"__processing0");

		        if (sketch) {
		            console.log("SKETCH HAS LOADED");
		            console.log(fname+ ' '+pic_width+' '+pic_height);
		            sketch.setupEverything(large_image_directory+fname,pic_width,pic_height);
		            $('#__processing'+idCount.toString()).css('width',pic_width).css('height',pic_height);
		            clearInterval(mem);
		        } else {
		            timer += 10;
		            if (timer > timeout) {
		                console.log("FAILED TO LOAD SKETCH");
		                clearInterval(mem);
		            }
		        }
		    }, 10);

			function getProcessingSketchId () { return '#__processing'+idCount.toString(); }
		}, "json");
	}
	function setup(){
		postAndLoad(fileList[count]);
    	console.log(" count is "+count+' '+fileList[count]);
  		count++;
  		if(count>=fileList.length){
  			count=0;
  		}
	};
	//setup();
	
});
