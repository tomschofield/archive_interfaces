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

$text = "Packets labelled: The Little Book of Judas Brendan Kennelly> Adrian Mitchell The Shadow Knows MS + Proofs> Mrs Perkins and Oedipus Elizabeth Bartlett> A Red Cherry on a White-Tiled Floor Translator's Proof> unlabelled - The Essential Rilke New Revised Edition> Lou-Lou Selima Hill 2 Proof Stages> Ground Water Matthew Hollis Author's Proof> Bloodaxe World Poets 2 Mary Oliver Wild Geese";

$items = getPlaces("test",$text);
print_r($items);

?>