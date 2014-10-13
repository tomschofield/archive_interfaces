<?php

// base

function getPeople($title, $text) {

		if (trim($text)=="") {
			error_log("no bio for ".$title);
			return array();
		}

		if (file_exists("cache/".$title)) {
			$res = file_get_contents("cache/".$title);
		}
		else {
			echo "contacting calais\n";
			sleep(.5);
			$url = "http://api.opencalais.com/tag/rs/enrich";
			$key = "INSERT YOU API KEY HERE";

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

function consolidateText(){
	$dir    = './PDF_text';
	$files1 = scandir($dir);
	//print_r($files1);
	$allText = "";

	for ($i=0; $i < sizeof($files1); $i++) { 
		$pieces = explode(".", $files1[$i]);
		if(sizeof($pieces)>0){
			if($pieces[sizeof($pieces)-1]=="txt"){
				echo $files1[$i],"\n";	# code...
				$allText.=file_get_contents("PDF_text/".$files1[$i]);
			}
		}
	}
	file_put_contents("outputAll.txt", $allText);
}

function API_for_each(){

	$dir    = './PDF_text';
	echo $dir;
	$files1 = scandir($dir);
	print_r($files1);
	$allText = "";

	for ($i=0; $i < sizeof($files1); $i++) { 
		$pieces = explode(".", $files1[$i]);
		if(sizeof($pieces)>0){
			if($pieces[sizeof($pieces)-1]=="txt"){
				echo "checking Calais for ",$files1[$i],"\n";	# code...
				//$thisText = file_get_contents("PDF_text/".$files1[$i]);

				$str = file_get_contents("PDF_text/".$files1[$i]);
				$thisText = iconv($in_charset = 'UTF-16LE' , $out_charset = 'UTF-8' , $str); 
				$items = getPeople($files1[$i],$thisText);
				print_r($items) ;
				$allText.=$items;
			}
		}
	}
	file_put_contents("results.txt", $allText);
}

function cmp($a, $b)
{
    return strcmp($a['authorName'], $b['authorName']);
}

function cmpPop($a, $b)
{
    return strcmp($b['popularity'], $a['popularity']);
}

function consolidateNames(){
	$dir    = './cache';
	$files1 = scandir($dir);
	$namesList = array();
	for ($i=0; $i < sizeof($files1); $i++) { 
		//echo mb_detect_encoding(file_get_contents("cache/".$files1[$i])), "\n";
		$contents = json_decode(file_get_contents("cache/".$files1[$i]), true);
//		print_r( $contents);
		foreach ($contents as $entity) {
			//print_r( $res);
			//echo "\n";
			
			if ($entity["_type"]=="Person" ) {
				//echo $entity["name"],"\n";
				$instances = $entity["instances"]; 
				
				$suffixes = array();
				$prefixes = array();
				$detection = array();



				foreach ($instances as $instance) {
					//echo $instance["suffix"] ,"\n";
					$bits = explode(" ", $instance["prefix"]);
					echo $instance["prefix"],"\n";//trim($bits[sizeof($bits)-1]);
					if(trim($bits[sizeof($bits)-1])=='for'){
						echo "found a dedication : ".$instance["detection"];
					}

					array_push($suffixes, $instance["suffix"]);
					array_push($prefixes, $instance["prefix"]);
					array_push($detection, $instance["detection"]);
				}
				//clean up the name
				$lowerName = strtolower($entity["name"]);
				$pieces = explode(" ", $lowerName);
				$capsName = "";
				
				foreach ($pieces as $piece) {
					$capsName.=ucfirst($piece);
					$capsName.=" ";
				}
				
				$trimmedName = trim($capsName);

				$thisEntity = array(
				    "fname" => $files1[$i],
				    "authorName" => $trimmedName ,
				    //"prefixes" =>$prefixes,
				    //"suffixes" => $suffixes,
				    "detection" => $detection   
				);
				array_push($namesList, $thisEntity);
			}
		}
		// //echo sizeof($contents),"\n";
		//print_r($contents);
	}
	file_put_contents("names.json", json_encode($namesList));
	$fContents = "";

	usort($namesList, "cmp");
	$prevAuthor = "";

	$maxPopularity = 0;
	$pop = 0;
	$mostPopularName = "";

	$namesAndPopularity = array();

	foreach ($namesList as $entity) {
		if ($entity['authorName']!=$prevAuthor){

			$thisEntity = array(
			    "name" => $prevAuthor,
			    "popularity" => $pop 
			);
			
			array_push($namesAndPopularity, $thisEntity);

			if($pop>$maxPopularity){
				$mostPopularName = $prevAuthor;
				$maxPopularity = $pop;
			}
			$pop = 0;
			$fContents.="______PERSON______:". $entity['authorName'] ." \n";
			$prevAuthor = $entity['authorName'];
		}
		$fContents.="INSTANCE"."\n";
		$fContents.=$entity['fname'];
		$fContents.="\n";
		$fContents.=$entity['authorName'];
		$fContents.="\n";

		$detections = $entity['detection'];
		foreach ($detections as $aDetection) {
			$fContents.=$aDetection;
			$fContents.="\n";
		}
		$fContents.="\n";
		$pop++;
	}
	print_r($mostPopularName);
	file_put_contents("people.txt", $fContents);
	usort($namesAndPopularity, "cmpPop");
	file_put_contents("popularity.txt", json_encode($namesAndPopularity));
}
consolidateNames();
//API_for_each()

?>