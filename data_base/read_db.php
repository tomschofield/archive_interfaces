<?php
	$db = json_decode(file_get_contents('bloodaxe_db.json'),true);
	//print_r($db);
	$books = $db['books'];
	foreach ($books as $book) {
		echo $book['date'],"\n";
		# code...
	}
?>