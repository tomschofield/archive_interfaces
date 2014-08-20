<?php
	$db = json_decode(file_get_contents('shapes.json'),true);
	echo sizeof($db);
	 // print_r($db);
	$items = array();
	foreach ($db as $key=> $value) {
		# code...
		// // print_r($item);
		// print_r($value['isLandscape']);
		$thisItem= array(
			'fname'=>$key,
			'isLandscape'=>$value['isLandscape'],
			'values'=>$value['values']
		);
		array_push($items, $thisItem);

	}
	file_put_contents('fshapes.json', json_encode($items));
?>