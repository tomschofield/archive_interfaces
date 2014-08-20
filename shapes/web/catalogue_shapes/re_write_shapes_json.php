<?php
	$db = json_decode(file_get_contents('shapes.json'),true);
	$full_db = json_decode(file_get_contents('bloodaxe_db.json'),true);
	$books = $full_db['books'];
	echo sizeof($db);
	 // print_r($db);
	$items = array();
	foreach ($db as $key=> $value) {
		# code...
		// // print_r($item);
		// print_r($value['isLandscape']);
		$id = "";
		// foreach ($books as $key1=> $value1) {
		// 	$individualFiles = $value1['individualFiles'];
			

		// 	  if(sizeof($individualFiles)>0){
		// 		foreach ($individualFiles as $file) {
		// 			$imagePaths = $file['imagePaths'];
		// 			if(sizeof($imagePaths)>0){
		// 				foreach ((array)$imagePaths as $path) {
		// 					# code...
							
		// 					$exploded = explode('/', $path);
		// 					if( $exploded[sizeof($exploded)-1]  ==$key){
		// 						// echo "found one \n";
		// 						$id  = $value1['id'];

		// 					}
		// 				}
		// 			}
		// 		}
		// 	 }
		// }
		$id = substr($key,0,13  );//$value['fname'], 0,13);
		$id = str_replace("-", "/", $id);
		 echo $id,"\n";
		$thisItem= array(
			'id'=>$id,
			'fname'=>$key,
			'isLandscape'=>$value['isLandscape'],
			'values'=>$value['values']
		);
		// echo $thisItem['fname'],"\n";
		//array_push($items, $thisItem);
		$items[$thisItem['fname']] = $thisItem;

	}


	file_put_contents('fshapes.json', json_encode($items));
?>