function angControl($scope, $http){ //scope is the thing that relates angular to the scope - scope is where the data modle lives
	
	$scope.books =  [];
	$scope.people =  [];
	$scope.paths = [];
	$scope.dates = [];
	// $scope.textfilter = "";
	$scope.path = "/Users/cmdadmin/Documents/PDF/jpegs/";
	$scope.stats=[];
	// $scope.move = false;
	$scope.correctImgURL =function(url){
		var exploded = url.split(".");
		var newFname = "";
		for (var i = 0; i < exploded.length; i++) {
			newFname+=exploded[i];
			newFname+=".";
		};
		newFname+="jpg";

		return $scope.path + newFname;
	}
	// $source.fish = function(){
	// 	return '#0000FF';
	// }
	$scope.setFilter =function(year){
		console.log(year[0]);
		$scope.txtfilter=year[0];
		
	}
	$scope.setAuthorFilter =function(author){
		console.log(author);
		$scope.txtfilter=author;
		
	}
	$scope.getBarWidth= function(yearStats){
		var w = 620/yearStats.length;
		//console.log('yearStats.length ',yearStats.length,' w ',w)
		return w;
	}
	// $scope.getAuthors= function(){
	// 	$authors = [];
	// 	for (var i = 0; i < $scope.people.length; i++) {
	// 		$authors.push($scope.people[i].author );
	// 	};
	// 	return $authors;
	// }

	
	$scope.getYearStats = function(){
		var numYears = 2014-1978;
		var mult = 1;
		// console.log("test ",Math.random());
		var stats = [];
		for (var i = 0; i < numYears; i++) {
			stats.push(mult*i);//Math.random());
		};
		
		return stats;
		// var stats = [30.6,70.8,10.1,19.9];
		// return stats;
	}
	$scope.testColor = function(move){
		var color;
		if (move){
			color='red';
		}
		else{
			color = 'teal';
		}
		return color;
	}
	$scope.test = function(){
		//console.log("test success");
		var filters = ['MATERIALITY','GESTURE','BLOCKS','POEMS','MARGINALIA','ART' , 'PARTICIPATION', 'COLLABORATION','CRITICALITY' ,'STAMPS' ,'LETTERS' ,'EDITING' ];
		return filters;
	}
	$scope.print = function(){
		console.log("test success");
	}
	function ArrNoDupe(a) {
	    var temp = {};
	    for (var i = 0; i < a.length; i++)
	        temp[a[i]] = true;
	    var r = [];
	    for (var k in temp)
	        r.push(k);
	    return r;
	}
	$scope.search = function(query) {
		var URL ='bloodaxe_db.json';
		// var numYears = 2014-1977;
		// console.log("test ",Math.random());
		// var stats = [];
		// for (var i = 0; i < numYears; i++) {
		// 	stats.push(10*Math.random());
		// };

		$http.get(URL).success(function(data){
			//$scope.collection = data.response.docs;
			$scope.books = data.books;
			$scope.people = data.people;

			//console.log(data);//$scope.collection);
			
			for(var i=1978;i<2014;i++){
				var o = [i,0];
				$scope.dates.push(o);
			}
			for(var i=0;i<data.books.length;i++){
				//console.log(data.books[i].date);
				//$scope.dates.push(data.books[i].date);
				//if this date exists then incremeent
				for(var j=0;j<$scope.dates.length;j++){
					if(data.books[i].date==$scope.dates[j][0]){
						$scope.dates[j][1]++;
					}
				}
				// if(data.books[i].hasImages){
				// 	console.log("got");

				// }
			}
			//$scope.dates = ArrNoDupe(ds);
			// console.log('dates');
			 console.log($scope.dates);
		});
	}
	$scope.search();
	//$scope.harvest();
}