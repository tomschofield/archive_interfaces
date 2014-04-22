<?php
$filename = 'time_table_plain_text.txt';
$fp = @fopen($filename, 'r'); 
$array = array();

// Add each line to an array
if ($fp) {
		$array = explode("\n", fread($fp, filesize($filename)));
}

$merged="";
for ($i=0; $i <sizeof($array) ; $i++) { 
	$merged.= $array[$i];
	$merged.=" ";
}

$words = (explode(" ",$merged));

$numbers = array();

for ($i=0; $i <sizeof($words) ; $i++) { 
	$trimmed = trim($words[$i]);
	
	$stripped =  preg_replace('/[^a-z0-9]+/i', '', $trimmed);
	if(is_numeric ( $stripped )) {
		array_push($numbers, $stripped);
	}
}


function getMostPopularBoxes($numList){
	
	$listOfThree = array();
	//make a long empty array
	for ($i=0; $i < 100; $i++) { 
		array_push($listOfThree, 0);
	}
	//copy num list into it
	for ($i=0; $i <sizeof($numList) ; $i++) { 
		//echo $numList[$i];
		//echo ", ";
		$listOfThree[$numList[$i]]++;		

	}
	//sort list by size
	arsort($listOfThree);
	

	$firstvalue = current($listOfThree);
	$sentence = "";
	$count = 0;
	while(current($listOfThree)==$firstvalue){
		//echo current($listOfThree);
		//add a comma before every number except the first one
		if($count!=0){
			$sentence.=", ";
			
		}
		$sentence.=key(($listOfThree));
		
		next($listOfThree); 
		$count++;
	}
	//echo $sentence;
	$explodedSentence = split(",", $sentence);
	$htmlcode="";
	if (sizeof($explodedSentence)<2) {
		$htmlcode=" is <span style= \"color:white; font-weight:bold; font-size:24pt;\">";
		for ($i=0; $i <sizeof($explodedSentence)-1 ; $i++) { 
			//$htmlcode.=$explodedSentence[$i];
			
		}
		$htmlcode.=$explodedSentence[0];
		$htmlcode.="</span>";
	}
	else {
		$htmlcode="es are <span style= \"color:white; font-weight:bold; font-size:24pt;\">";
		for ($i=0; $i <sizeof($explodedSentence)-1 ; $i++) { 
			$htmlcode.=$explodedSentence[$i];
			$htmlcode.=",";
		}
		$htmlcode.="</span>";
		$htmlcode.=" and ";
		$htmlcode.="<span style= \"color:white; font-weight:bold; font-size:24pt;\">";
		$htmlcode.=$explodedSentence[sizeof($explodedSentence)-1];
		$htmlcode.="</span>";
	}
	//echo $htmlcode;
	return $htmlcode;
}
   echo getMostPopularBoxes($numbers);
		
		//$jsonArray = json_decode($array[$_POST["id"]]);
		//echo json_encode(array("boxref"=>$jsonArray[0],"labels"=>$jsonArray[1],"accessed_by"=>$jsonArray[2],"summary_contents"=>$jsonArray[3],"data_pairs"=>$jsonArray[4],"dates_covered"=>$jsonArray[5],"id"=>$_POST["id"],"meat"=>$exploded[5]));
		//echo $jsonArray[4][0];

?>