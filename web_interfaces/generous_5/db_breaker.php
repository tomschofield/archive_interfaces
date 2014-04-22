<?php
	$filename = 'BloodAxeDB_tab.txt';

	// Open the file
	$fp = @fopen($filename, 'r'); 
	$array = array();
	// Add each line to an array
	if ($fp) {
   		$array = explode("\r", fread($fp, filesize($filename)));
	}
	
	//echo sizeof($array);
	$counter = 0;
	// foreach ($array as $line){
	// 	echo $counter;
	// 	echo " : ";
	// 	echo $line;
	// 	echo '<br>';
	// 	echo '<br>';
	// 	echo '<br>';

	// 	$counter++;

	// }
	echo $name;
	echo $time;

?>