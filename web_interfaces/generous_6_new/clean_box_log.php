<?php
	function getPlaces($title, $text) {

		if (trim($text)=="") {
			error_log("no bio for ".$title);
			return array();
		}

		if (file_exists("cache/".$title)) {
			$res = file_get_contents("cache/".$title);
		}
		else {
			sleep(.5);
			$url = "http://api.opencalais.com/tag/rs/enrich";
			$key = "2q6svbzkyawhzxgdyjnvn35d";

			$ch = curl_init($url);

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $text);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-calais-licenseID: ". $key,
				"content-type: text/raw",
				"Accept: application/json",
			  "Content-Length: " . strlen($text))
			);

			$res = curl_exec($ch);
			file_put_contents("cache/".$title, $res);		
		}

		$res = json_decode($res, true);

		$places = array();

		foreach ($res as $entity) {
			//print_r( $res);
			//echo "\n";
			if ($entity["_type"]=="Person" ) {

				array_push($places,$entity["name"]);
				//array_push($places,$entity["instances"][0]['suffix']);
			}
			
		}

		return $places;
	}



	
	$filename = 'output_clean.csv';
	$data = file_get_contents($filename);
	$Data = str_getcsv($data, "\n"); //parse the rows 

	$csvData = array();
	foreach($Data as &$Row) {
		$Row = str_getcsv($Row, ","); 
		array_push($csvData, $Row );
	}

	$fields = array();


	for ($i=0; $i <sizeof($csvData) ; $i++) { 
		
		$exploded = explode(";",$csvData[$i][2]);
		// echo sizeof($exploded);
		// echo "\n";

		$cleanExploded = array();
		for ($j=0; $j <sizeof($exploded) ; $j++) { 
			$trimmed = trim($exploded[$j]);
			if(strlen($trimmed)>0){
				array_push($cleanExploded, $trimmed);
			}
			
		}
		$label = 'record';
		$label.=$i;
		$entities = getPlaces($label,$csvData[$i][2]);

		$thisBox = array(
		    "index" => $i,
		    "boxID" => $csvData[$i][0],
		    "boxDescription" =>$csvData[$i][1],
		    "boxContents" => $cleanExploded,
		    "entities"=> $entities,
		    "date" => $csvData[$i][3],
		    "correspondsTo" => array()
		);
		// # code...
		 array_push($fields, $thisBox);
		# code...
	}

	//print_r(json_encode($fields));
	$bannedWords = array();
	array_push($bannedWords, "poetry");
	array_push($bannedWords, "poems");
	
	//now lets looks at correspondances

	// for($j=0;$j<sizeof($fields);$j++) {
	// 	$boxContents = $fields[$j]["entities"];
	// 	echo $j," ";
	// 	for($i=0;$i<sizeof($boxContents);$i++) {
	// 		echo $i," ";
	// 		echo $boxContents[$i];
	// 		echo "\n";

	// 	}
	// }
	$fields2 = $fields;
	$matchCount = 0;
	for ($i=0; $i <sizeof($fields) ; $i++) { 
		$entities1 = $fields[$i]['entities'];
		$matches = array();
		for ($j=0; $j <sizeof($fields) ; $j++) { 
			$entities2 = $fields[$j]['entities'];
			for ($k=0; $k <sizeof($entities1) ; $k++) { 
				//echo $i,' ',$entities1,"\n";
				for ($m=0; $m <sizeof($entities2) ; $m++) { 
					if($k!=$m && $entities1[$k]==$entities2[$m]){
						//echo $matchCount,' ',$entities1[$k],' ',$entities2[$m],"\n";
						$matchCount++;
						if(!array_key_exists($entities1[$k] ,$matches ) ){


							$matches[$entities1[$k]]=array();
							array_push($matches[$entities1[$k]], $j);
						}
						else{
							array_push($matches[$entities1[$k]], $j);
						}
					}

				}

			}


		}
		$fields[$i]['correspondsTo']=$matches;
		# code...
	}
	// for ($i=0; $i <sizeof($fields) ; $i++) { 
	// 	echo $i," \n";
	// 	if(sizeof($fields[$i]['correspondsTo'])>0){
	// 		print_r($fields[$i]['correspondsTo']);
	// 	}
	// }
	//array_slice($a,2)
	//echo json_encode($fields);
	echo json_encode(array_slice($fields,1) );
	// for($i=0;$i<sizeof($fields);$i++) {
	// 	echo ($fields[$i]->boxID);
	// 	echo "\n";
	// }
?>