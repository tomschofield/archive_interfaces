<?php
$sizes = [];
//$_POST["id"] 
$filename='burn_image_viewer_4.pde';
$lines = file($filename, FILE_IGNORE_NEW_LINES);



$new_sketch ="";
//add the preload statement
$new_sketch.="/* @pjs preload= ";
$new_sketch.=$_POST["imagedir"];
$new_sketch.=$_POST["filename"];
$new_sketch.="; */";

$new_sketch.="\n";
for ($i=1; $i <sizeof($lines) ; $i++) { 
	$new_sketch.= $lines[$i];
	$new_sketch.="\n";
	//echo ';
}
$file='burn_image_viewer_4.pde';
file_put_contents($file, $new_sketch);
//$path = $_POST["filename"];
$sizes = getimagesize( "..".$_POST["imagedir"].$_POST["filename"] );
//$sizes = getimagesize( $_POST["filename"] ));//
echo json_encode(array("mywidth"=>$sizes[0], "myheight"=>$sizes[1]) );

?>