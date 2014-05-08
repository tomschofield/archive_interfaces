<?php

// base

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
		$key = "INSERT YOUR KEY";

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
		if ($entity["_type"]=="PersonLocation") {
			if (!in_array($entity["locationstring"], $places))
				array_push($places, $entity["locationstring"]);		
		}
		else if ($entity["_type"]=="Country" || $entity["_type"]=="City") {
			if (!in_array($entity["name"], $places))
				array_push($places, $entity["name"]);
		}
	}
		
	return $places;
}


$inc = 20000;

$placesIndex = array();
$places = array();

$items = json_decode(file_get_contents("complete_monadic.json"), true);

// link people that are mentioned
for ($i=0; $i < count($items); $i++) { 
	for ($j=0; $j < count($items); $j++) { 
		if ($i==$j || trim($items[$j]["text"])=="") continue;
		
		$title = str_replace(" ", "", strtolower($items[$i]["title"]));
		$text = str_replace(" ", "", strtolower($items[$j]["text"]));
		
		if (strpos($text, $title)!==false) {
			array_push($items[$i]["links"], $items[$j]["id"]);
			array_push($items[$j]["links"], $items[$i]["id"]);
		}		
	}
}


// link places with people
for ($i=0; $i < count($items); $i++) { 

	if ($items[$i]["type"]!=0) continue;

	$itsplaces = getPlaces($items[$i]["title"], $items[$i]["text"]);
	
	for ($j=0; $j < count($itsplaces); $j++) { 
		$pl = $itsplaces[$j];		
		if (array_key_exists($pl, $placesIndex)) $id = $placesIndex[$pl];
		else {
			$id = $inc;			
			$place = array(
				"id" => $id,
				"type" => 1,
				"title" => $pl,
				"text" => "",
				"links" => array()
			);

			$placesIndex[$pl] = $id;
			$places[$id] = $place;
				
			$inc++;
		}
		
		array_push($places[$id]["links"], $items[$i]["id"]);
		array_push($items[$i]["links"], $id);
	}
}

$items = array_merge($places, $items);

// pruning categories that have no poet
$pruned = array();
foreach ($items as $it) {
	if (count($it["links"])==0 && $it["type"]==2) continue;
	array_push($pruned, $it);
}

file_put_contents("data.json", json_encode($pruned));


?>