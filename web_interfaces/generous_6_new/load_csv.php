<?php



$filename = 'output.csv';
$data = file_get_contents($filename);
$Data = str_getcsv($data, "\n"); //parse the rows 

$csvData = array();
foreach($Data as &$Row) {
	$Row = str_getcsv($Row, ","); 
	array_push($csvData, $Row );
}//parse the items in rows


for ($i=0; $i <sizeof($csvData) ; $i++) { 
		echo sizeof($csvData[$i]);
	}

?>