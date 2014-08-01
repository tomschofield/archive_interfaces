<?php

header('Content-Type: text/plain');
$csv = file_get_contents('OCRdata.csv');
echo $csv;

?>