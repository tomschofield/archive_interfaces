<?php
	
	include '../read_archive.php';
	$authorList = getAuthorListFromPoetryByAuthor();
	//print_r($authorList);
	$toJson = array();
	$count = 0;
	$file = "monadic_python.json";
	//load the python created from the scrape
	$subjectList = json_decode(file_get_contents($file), true);
	
	//for all the authors
	foreach ($authorList as $key => $value) {
		# code...
		//print_r();
		//check the bio isn't blank
		if(strlen(getInfoForAuthor($value)["bio"])>1){
			$indices = array();
			//get indices from subjects

		    for ($i=0; $i <sizeof($subjectList) ; $i++) { 
		    	# code...
		    	$allIndices = $subjectList[$i]["links"];
		    	for($j=0;$j<sizeof($allIndices);$j++){
		    		if($allIndices[$j]==10000+$count){
		    			array_push($indices, 30000+$i);
		    		}
		    	}
		    }
		    //print_r($indices);
		    //make an object for this author
			$thisAuthorJson = array(
			    "id" => 10000+$count,
			    "type" => 0,
			    "title" => $value,
			    "text" => getInfoForAuthor($value)["bio"],
			    "links" => $indices
			);
			 array_push($toJson,$thisAuthorJson);
			$count++;
		}
	}
	//combine the 2 json arrays
	$merged = array_merge($subjectList,$toJson);
	//save it out
	file_put_contents("complete_monadic.json", json_encode($merged));
	
?>