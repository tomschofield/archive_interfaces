<?php

	// creates the JSON necessary for the D3 bubble chart in script.js
	$rawJson = json_decode(file_get_contents("OCRdata.json"), true);

	$wholeJson = array();
	$wholeJson["name"] = "poets";
	// $wholeJson["value"] = 255;

	$cleanJson = array();

	foreach ($rawJson as $item) {

		$anItem = array(
			"name"		=> $item["ID"],
			"value" 	=> $item["PDFS"]
			
		);
		array_push($cleanJson, $anItem);
		//$cleanJson = array_merge($anItem, $cleanJson);
		
	}
	$wholeJson["children"] = $cleanJson;
	file_put_contents('cleanJson.json', json_encode($wholeJson));

?>