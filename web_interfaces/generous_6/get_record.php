<?php
$filename = 'box_log_paired.json';
$fp = @fopen($filename, 'r'); 
$array = array();

// Add each line to an array
if ($fp) {
		$array = explode("\n", fread($fp, filesize($filename)));
}


if($_POST["id"] < sizeof($array) ){
		//$jsonArray = json_decode($array[5]);
		//echo $array[6];
		$jsonArray = json_decode($array[$_POST["id"]]);
		echo json_encode(array("boxref"=>$jsonArray[0],"labels"=>$jsonArray[1],"accessed_by"=>$jsonArray[2],"summary_contents"=>$jsonArray[3],"data_pairs"=>$jsonArray[4],"dates_covered"=>$jsonArray[5],"id"=>$_POST["id"],"meat"=>$exploded[5]));
		//echo $jsonArray[4][0];
}
?>