var angControllers  = angular.module('angControllers', ['ngSanitize','ngAnimate']);


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
	


  	// var URL ='http://localhost:8888/DH/wordpress/?json_route=/posts';
  	$scope.getTags = function(){
   		
   		return $scope.mytags;
   	}
   	$scope.setFilter =function(filter){
   		////console.log(filter.count);
	   	$scope.sharedFilter.txtfilter = filter.count;
   	} 
   	$scope.setFilter2 =function(filter){
   		////console.log(filter);
	   	$scope.sharedFilter.txtfilter = filter.name;
   	} 
   	$scope.getDisplay = function(){
		////console.log($scope.myDisplay);
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
	$scope.getAuthor = function(author){
		if(author=="none"){
			return "anthology or collection"
		}
		else{
			return author;
		}
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
		console.log( ' $scope.title ',$scope.title);

		$scope.posts = results['books'];

		var count= 0;
		$scope.myTempTags =  [];

		var compressedTitle = $scope.title;//.replace(/\//g, '');
		
		for (var apost in $scope.posts){
			// console.log('apost ',apost, '$scope.posts[apost]',$scope.posts[apost]);
			// console.log($scope.posts[apost].title);

			var compressedId = $scope.posts[apost].id.replace(/\//g, '');

			//if($scope.posts[apost].title ==$scope.title ){
			if(compressedTitle==compressedId){
				$scope.thisPost = $scope.posts[apost];
				console.log('mtach');
			}
		}
		
		console.log("this post" ,$scope.thisPost);
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
    $scope.numToDisplay = 50;
  	Data.getDataAsync(function(results) {
	    // //console.log('dataController async returned value');
	    var mybooks = results['books'];//['BXB/1/1/ZEP/2']

	    console.log(mybooks);

	  
	  	// //console.log(Data);
	  	//posts is an array, books is an object
		$scope.posts =  [];
		$scope.books = [];
		$scope.mytags =  [];
		$scope.dates = [];
		$scope.liveDates = [];
		$scope.stats=[];

		// $scope.txtfilter = "";
		$scope.single_view = false;

	   	var URL ='bloodaxe_db.json';
	   	$scope.debug = function(post){
	   		//if(post.title=='ALPHABET'){
	   			console.log(post);
	   		//}
	   		
	   		return "placeholder";
	   	}
	   	$scope.getShortId = function(longid){
	   		var shortId= longid.replace(/\//g, '');
	   		return shortId;
	   	}
	   	$scope.getFeaturedImage = function(post){
	   		// var url = 'http://localhost:8888/DH/wordpress/wp-content/uploads/'+post.featured_image.attachment_meta.file;
	   		// //console.log(url);
	   		$scope.baseUrl = post.cover_info.fname;
	   		$scope.url = '../BloodAxeBooks_400/'+$scope.baseUrl;
	   		// //console.log(url);
	   		if( $scope.baseUrl===undefined){
	   			console.log('problem with ',post);
	   		}
	   		else{
	   		//	console.log('no problem with ',post);
	   		}
	   		//console.log(baseUrl,post);
	   		return $scope.url;
	   	}
	   	$scope.customSearch = function(){
	   		// //console.log('called for',searchTerm);
	   		if($scope.sharedFilter.txtfilter=="") return true;
	   		//return false;
	   		console.log($scope.sharedFilter.txtfilter);
	   		var matches = [];
	   		for(var key in $scope.posts){

	   			//s.indexOf("oo") > -1
	   			//console.log('title',$scope.posts[key].title);
	   			var lcTitle = $scope.posts[key].title.toLowerCase();
	   			var lcSearchTerm = $scope.sharedFilter.txtfilter.toLowerCase();
	   			if( lcTitle.indexOf(lcSearchTerm) > -1 ) {
	   				console.log('match',$scope.posts[key].title);
	   				//return true;
	   				matches.push($scope.posts[key]);
	   			}
	   			
	   		}

	   		return matches;
	   		// var exploded_search_terms = $scope.sharedFilter.txtfilter.split(', ');
	   		// for (var i = 0; i < searchTerm.terms.post_tag.length; i++) {
	   		// 	////console.log(searchTerm.terms.post_tag[i].name);
	   		// 	for (var j = 0; j < exploded_search_terms.length; j++) {
	   		// 		if(exploded_search_terms[j].toLowerCase() == searchTerm.terms.post_tag[i].name.toLowerCase() ){
	   		// 			return true;
	   		// 		}
	   		// 	}

	   		// };
		    // return searchTerm.ID === 49;
		};
		$scope.loadMore = function(){
			$scope.numToDisplay+=50;
		}
		$scope.loadFewer = function(){
			$scope.numToDisplay-=50;
		}
	   	$scope.getTags = function(){
	   		
	   		return $scope.mytags;
	   	}
	   	$scope.setFilter =function(filter){
	   		////console.log(filter);
	   		//console.log($scope.posts.length);
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
			// var stats = [30.6,70.8,10.1,19.9];
			// return stats;
		}
		$scope.$watch('sharedFilter.txtfilter', function(newValue, oldValue) {
        	
        	var filteredResults = $filter('filter')($scope.posts, $scope.sharedFilter.txtfilter);
        	//clear out the foun dates
        	// console.log('filt' ,filteredResults);
        	for(var i=0;i<$scope.liveDates.length;i++){
        		$scope.liveDates[i][1] = 0;
        	}
        	for (var i = 0; i < filteredResults.length; i++) {
        	//	for (var key in filteredResults) {
        		////console.log('filtered date ',filteredResults[i].date);
	        	for(var j=0;j<$scope.liveDates.length;j++){
	        		if(filteredResults[i].date==$scope.liveDates[j][0]){
	        		//if(filteredResults[key].date==$scope.liveDates[j][0]){
	        			$scope.liveDates[j][1] ++ ;
	        		}
	        	}
        	}
        	// //console.log('filteredResults ',$scope.liveDates);
        	
		});

		 //$http.get(URL).success(function(data){
			// //console.log(data);
			$scope.posts ;//= results['books'];//.slice(2,results['books'].length);
			//console.log()
			$scope.books = results['books'];
			for(var key in $scope.books){
				//console.log('post  ',$scope.books[key]);
				$scope.posts.push($scope.books[key]);
			}
			//console.log('posts ',$scope.posts);

			for(var i=1978;i<2014;i++){
				var o = [i,0,0];
				var o2 = [i,0,0];
				$scope.dates.push(o);
				$scope.liveDates.push(o2);
			}

			// //console.log($scope.posts);
			var count= 0;
			$scope.myTempTags =  [];
			// for (var i = 0; i < $scope.posts.length; i++) {
			for (var key in $scope.books) {

				for(var j=0;j<$scope.dates.length;j++){
				//	console.log($scope.books[key].date);
					if($scope.books[key].date==$scope.dates[j][0]){
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

			// console.log('dates ',$scope.dates);
			// console.log('dates ',$scope.liveDates);
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
     


	