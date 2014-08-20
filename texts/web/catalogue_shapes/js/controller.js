var angControllers  = angular.module('angControllers', ['ngSanitize','ngAnimate','ngTouch','ngMouseDrag']);



angControllers.factory('Data', function($http){
	var URL ='bloodaxe_db.json';
	return {
    getDataAsync: function(callback) {
      $http.get(URL).success(callback);
    }
  };
});

//this is the shared service for filters

angControllers.factory('sharedService', function() {  
    return {
        thing : {
            txtfilter : ""
        }
    };
});

function ArrNoDupe(a) {
	var temp = [];
	for (var i = 0; i < a.length; i++) {
		var exists = false;
		for (var j = 0; j < temp.length; j++) {
			if(a[i]==temp[j]) {
				exists =true;
				// //console.log(a[i], " ",temp[j]);
			}
		}
		if(!exists){
			temp.push(a[i]);
		}
		
	};
	return temp;
}
// angular.module('filterModule', []).filter('myFilter', function() {
//   return function(input, searchText){
//          var returnArray = [];
//          var searchTextSplit = searchText.toLowerCase().split(' ');
//         for(var x = 0; x < input.length; x++){
//              var count = 0;
//             for(var y = 0; y < searchTextSplit.length; y++){
//                 if(input[x].toLowerCase().indexOf(searchTextSplit[y]) !== -1){
//                     count++;
//                 }
//             }
//             if(count == searchTextSplit.length){
//                  returnArray.push(input[x]);   
//             }
//         }
//         return returnArray;
//     }
// });


// angular.module('myFilters', []).
//   filter('myFilterFunction', function() {
//     return function(movies,genres) {
//       var out = [];
//       // Filter logic here, adding matches to the out var.
//       return out;
//     }
//   });

angControllers.controller('GridDetailCtrl', ['$scope', '$timeout', '$http','$routeParams','Data','sharedService',
  function($scope, $timeout, $http, $routeParams, 	 Data,sharedService) {
  	window.scrollTo(0, 0);
// angControllers.controller('GridDetailCtrl', ['$scope', '$http','$routeParams',
//   function($scope,$http, $routeParams) {
	$scope.sharedFilter = sharedService.thing;
    $scope.name = "GridDetailCtrl Controller";
    // $scope.show_me=true;
    $scope.show_me = true;
	Data.getDataAsync(function(results) {
	    // //console.log('dataController async returned value');
	    
		$timeout(function() {
		    $scope.$apply('show_me = false');
		  }, 200);
	


  	var URL ='http://localhost:8888/DH/wordpress/?json_route=/posts';
  	$scope.getTags = function(){
   		
   		return $scope.mytags;
   	}
   	$scope.setFilter =function(filter){
   		//console.log(filter.count);
	   	$scope.sharedFilter.txtfilter = filter.count;
   	} 
   	$scope.setFilter2 =function(filter){
   		//console.log(filter);
	   	$scope.sharedFilter.txtfilter = filter.name;
   	} 
   	$scope.getDisplay = function(){
		//console.log($scope.myDisplay);
		return $scope.myDisplay;
	}
	$scope.toggleDisplay =function(){
		$scope.myDisplay = !$scope.myDisplay;
	}
	$scope.getDisplaySrc=function(){
		return $scope.displaySrc;
	}
	$scope.getDisplayBook = function(){
		return $scope.displayBook;
	}
	$scope.setDisplay =function(src, book){
		// [Log] /Users/cmdadmin/Documents/PDF/jpegs/BXB-1-2-AFL-1 4-28-page20.jpg (controller.js, line 75)
		$scope.displayBook = book
		var splitSource = src.split('/');
		var fname = splitSource[splitSource.length-1];

		var newSrc = "/Users/cmdadmin/Documents/PDF/jpegs_r/"+fname;
		$scope.displaySrc = newSrc;
		////console.log($scope.display);
		$scope.myDisplay = !$scope.myDisplay;
		////console.log($scope.display);
	}

   	
  	$scope.posts =  [];
	$scope.mytags =  [];
	$scope.thisPost;

    $scope.title = $routeParams.title;
  //   $http.get(URL).success(function(data){
		// //console.log(data);
		$scope.posts = results['books'];

		var count= 0;
		$scope.myTempTags =  [];
		for (var apost in $scope.posts){
			// console.log('apost ',apost, '$scope.posts[apost]',$scope.posts[apost]);
			// console.log($scope.posts[apost].title);
			if($scope.posts[apost].title ==$scope.title ){
				$scope.thisPost = $scope.posts[apost];
				
			}
		}
		console.log($scope.thisPost );
		var noDupe = ArrNoDupe($scope.myTempTags);
		for (var i = 0; i < noDupe.length; i++) {
			var o = {count:noDupe[i]};
			$scope.mytags.push(o);
		}

		var container = document.querySelector('#container');

		// var msnry = new Masonry( container, {
		//   // options
		//   columnWidth: 200,
		//   itemSelector: '.post'
		// });

	});
  }]);

// angControllers.controller('ListCtrl', function( $scope, Data ){
//     //console.log(Data);
// });

angControllers.controller('ListCtrl', ['$scope', '$http','$routeParams','$filter','Data','sharedService',
  function($scope,$http, $routeParams, $filter, Data,sharedService) {
  	$scope.sharedFilter = sharedService.thing;
    $scope.name = "ListCtrl Controller";

    var shapesJSON = 'fshapes.json';
    
    var leftButtonDown = false;
    
    
	document.body.onmousedown = function() { 
	  leftButtonDown = true;
	}
	document.body.onmouseup = function() {
	  leftButtonDown = false;

	}

    function findPos(obj) {
	    var curleft = curtop = 0;
	    if (obj.offsetParent) {
	        curleft = obj.offsetLeft
	        curtop = obj.offsetTop
	        while (obj = obj.offsetParent) {
	            curleft += obj.offsetLeft
	            curtop += obj.offsetTop
	        }
	    }
	    return [curleft,curtop];
	}


  	Data.getDataAsync(function(results) {
	    // //console.log('dataController async returned value');
	    // //console.log(results);
	  
	  	// //console.log(Data);
	  	$scope.imageArrays = [];
	  	$http.get(shapesJSON).success(function(data){

		    $scope.imageArrays = data;
		    console.log($scope.imageArrays);
		  //   for (var key in $scope.imageArrays) {
			 //    	console.log('key ',key, ' object ',$scope.imageArrays[key]);
			 // };
	    });
		$scope.posts =  [];
		$scope.books =  [];
		$scope.mytags =  [];
		$scope.dates = [];
		$scope.liveDates = [];
		$scope.stats=[];
		$scope.grid = [];
		$scope.filteredByShape = [];
	    var bW = 10;
		var bH = 15;
		for (var i = 0; i < bW; i++) {
	    	var row = [];
	    	$scope.grid.push([]);
	    	// console.log(grid.length);
	    	for (var j = 0; j < bH; j++) {
	    		 $scope.grid[i].push(0);
	    	};
	    	// grid[i]=row;
	    	
		};

		// $scope.txtfilter = "";
		$scope.single_view = false;

		//comparator for ordering my resuts
		function compare(a,b) {
		  if (a.hitRate > b.hitRate)
		     return -1;
		  if (a.hitRate < b.hitRate)
		    return 1;
		  return 0;
		}
	   	var URL ='bloodaxe_db.json';
	   	$scope.clearGrid =function(){
	   		for (var i = 0; i < bW; i++) {
		    	for (var j = 0; j < bH; j++) {
		    		$scope.grid[i][j]=0;
		    	};	
			};

	   	}
	   	$scope.onMouseDrag = function ($event) {
	   		console.log('called');
	   		console.log('leftButtonDown ',leftButtonDown);
	   		if(leftButtonDown){
	   			var draggableObject = document.getElementById('blockChooser');
			    // draggableObject.style.top = $event.pageY + 'px';
			    // draggableObject.style.left = $event.pageX + 'px';
			    var elementPosition = findPos(draggableObject);
			    var x = $event.pageX-elementPosition[0];
			    var y = $event.pageY-elementPosition[1];

			    var i=parseInt(x/10);
			    var j=parseInt(y/10);
			    // console.log(i,j,$scope.grid.length );
			    if(i<10 && j<15){
			    	$scope.grid[i][j] =1;
		   		 }
	   		}
		    
		    document.body.onmouseup = function() {
			  // leftButtonDown = false;
			  // $scope.compareGrid();
			}
		    //  $scope.updateSquareColor(i,j);
		    // $scope.compareGrid();
		  }
	   	$scope.updateSquareColor = function(i,j){
	   		
	   		if($scope.grid[i][j]==0){

	   			$scope.grid[i][j]=1;
	   		}
	   		else{
	   			$scope.grid[i][j]=0;
	   		}
	   		$scope.compareGrid();
	   	}
	   	$scope.updateSquareColorAsBlack = function(i,j){
	   		// console.log('calling');
	   		if(leftButtonDown){
	   			$scope.grid[i][j]=1;
	   		}
	   		
	   		// $scope.compareGrid();
	   	}
	   	$scope.compareGrid =function(){
	   		console.log('comparing');
	   		var flatGrid = [];
	   		//make a one dimensionsal version of our array for easy comparison
	   		for (var i = 0; i < bW; i++) {
		    	for (var j = 0; j < bH; j++) {
		    		flatGrid.push($scope.grid[i][j]);
		    	};	
			};
			//this will hold the results of our comparison
	   		var unsortedArray = [];
	   		//for (var i = 0; i < $scope.imageArrays.length; i++) {
	   		for (var key in $scope.imageArrays) {
	   			var hitRate = 0;
	   			for (var j = 0; j < $scope.imageArrays[key]['values'].length; j++) {
	   				
	   				if($scope.imageArrays[key]['values'][j] == flatGrid[j]){
	   					// if(flatGrid[j]==0){
	   					// 	//0 is weight
	   					// 	hitRate+=0.7;
	   					// }
	   					// else{
	   					// 	hitRate+=0.3;
	   					// }
	   					hitRate++;
	   					//console.log('hit');

	   				}
	   			}
	   		 	var resultObject = {};

	   		 	resultObject['hitRate']=hitRate;
	   		 	resultObject['fname']= $scope.imageArrays[key]['fname'];
	   		 	resultObject['values']= $scope.imageArrays[key]['values'];
	   		 	resultObject['id']= $scope.imageArrays[key]['id'];
	   		 	unsortedArray.push(resultObject);
	   		}
	   		unsortedArray.sort(compare);
	   		//console.log(unsortedArray);
	   		$scope.filteredByShape = [];
	   		for(var i=0;i<36;i++){
	   			$scope.filteredByShape.push(unsortedArray[i]);
	   		}
	   		
	   	}
	   	$scope.addFilePath = function(shapeObject){
	   		var newPath = '/Users/cmdadmin/Documents/PDF/jpegs_r/'+shapeObject['fname'];
	   		// console.log(newPath);
	   		return newPath;
	   	}
	   	$scope.getSquareColor = function(i,j){
	   		// if(val==0){
	   		// 	return 'white';
	   		// }
	   		// else{
	   		// 	return 'black';
	   		// }
	   		if($scope.grid[i][j]==0){
	   			return 'white';
	   		}
	   		else{
	   			return 'black';
	   		}
	   	}
	   	$scope.getState = function(i,j){
	   		console.log('updates');
	   		if($scope.grid[i][j]==0){
	   			return true;
	   		}
	   		else{
	   			return false;
	   		}
	   	}
	   	$scope.getBState = function(i,j){
	   		console.log('updates');
	   		if($scope.grid[i][j]==1){
	   			return true;
	   		}
	   		else{
	   			return false;
	   		}
	   	}
	   	$scope.getFeaturedImage = function(post){
	   		// var url = 'http://localhost:8888/DH/wordpress/wp-content/uploads/'+post.featured_image.attachment_meta.file;
	   		// //console.log(url);
	   		var baseUrl = post.cover_info.fname;
	   		var url = '../BloodAxeBooks_400/'+baseUrl;
	   		// //console.log(url);
	   		return url;
	   	}
	   	$scope.customSearch = function(searchTerm){
	   		// //console.log('called for',searchTerm);
	   		if($scope.sharedFilter.txtfilter=="") return true;
	   		
	   		var exploded_search_terms = $scope.sharedFilter.txtfilter.split(', ');
	   		for (var i = 0; i < searchTerm.terms.post_tag.length; i++) {
	   			//console.log(searchTerm.terms.post_tag[i].name);
	   			for (var j = 0; j < exploded_search_terms.length; j++) {
	   				if(exploded_search_terms[j].toLowerCase() == searchTerm.terms.post_tag[i].name.toLowerCase() ){
	   					return true;
	   				}
	   			}

	   		};
		    // return searchTerm.ID === 49;
		};
	   	$scope.getTags = function(){
	   		
	   		return $scope.mytags;
	   	}
	   	$scope.setFilter =function(filter){
	   		$scope.sharedFilter.txtfilter = filter[0];
	   	} 

	   	$scope.getBarWidth= function(yearStats){
			var w = parseInt(600/yearStats.length);
			// //console.log('yearStats.length ',yearStats.length,' w ',w)
			return w;
		}
		$scope.getYearStats = function(){
			var numYears = 2014-1978;
			var mult = 1;
			// //console.log("test ",Math.random());
			var stats = [];
			for (var i = 0; i < numYears; i++) {
				stats.push(mult*i);//Math.random());
			};
			
			return stats;
		}
		$scope.printRelatedItem  = function(shapeObject){
			var bookId = shapeObject['id'];
			var relatedBook = $scope.books[bookId];
			// console.log("round this related item ",bookId, relatedBook);
			//return relatedBook['title'];
		}
		$scope.getTitleForThumb = function(shapeObject){
			var bookId = shapeObject['id'];
			var relatedBook = $scope.books[bookId];
			// console.log("round this related item ",bookId, relatedBook);
			return relatedBook['title'];
		}
		$scope.$watch('sharedFilter.txtfilter', function(newValue, oldValue) {
        	
        	var filteredResults = $filter('filter')($scope.posts, $scope.sharedFilter.txtfilter);
        	
        	for(var i=0;i<$scope.liveDates.length;i++){
        		$scope.liveDates[i][1] = 0;
        	}
        	for (var i = 0; i < filteredResults.length; i++) {
        		// //console.log('filtered date ',filteredResults[i].date);
	        	for(var j=0;j<$scope.liveDates.length;j++){
	        		if(filteredResults[i].date==$scope.liveDates[j][0]){
	        			$scope.liveDates[j][1] ++ ;
	        		}
	        	}
        	}
        	// //console.log('filteredResults ',$scope.liveDates);
        	
		});
			$scope.books = results['books'];
			$scope.posts = results['books'];

			for(var i=1978;i<2014;i++){
				var o = [i,0,0];
				var o2 = [i,0,0];
				$scope.dates.push(o);
				$scope.liveDates.push(o2);
			}

			// //console.log($scope.posts);
			var count= 0;
			$scope.myTempTags =  [];
			//TODO
			for (var i = 0; i < $scope.posts.length; i++) {

				for(var j=0;j<$scope.dates.length;j++){
					if($scope.posts[i].date==$scope.dates[j][0]){
						$scope.dates[j][1]++;
					}
				}
				
				// for( var j=0;j<$scope.posts[i].terms.post_tag.length;j++){
				// 	var thisTag = $scope.posts[i].terms.post_tag[j].name;
				// 	var o = {count:thisTag};
				// 	$scope.myTempTags.push(thisTag);
				// 	count++;
				// }
				
			};

			////console.log('dates ',$scope.dates);
			////console.log('dates ',$scope.liveDates);
			// var noDupe = ArrNoDupe($scope.myTempTags);
			// for (var i = 0; i < noDupe.length; i++) {
			// 	var o = {count:noDupe[i]};
			// 	$scope.mytags.push(o);
			// }

			var container = document.querySelector('#container');


	 //});
	});
		
  }]);
// });
     


	