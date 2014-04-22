<?php
	$fnames=array();
	foreach (glob("..".$_POST["imagedir"]."*.jpg") as $filename) {

    	array_push($fnames, substr($filename, 3));
	}
	echo json_encode(array("fnames"=>$fnames));

?>