<?php
	#this script makes a python array declaration with all the names to append to urls for the scrape
	include '../read_archive.php';
	$authorList = getAuthorListFromPoetryByAuthor();
	print_r($authorList);
	$code  = "authorList = []\n";
	foreach ($authorList as $key => $value) {
		# code...
		if(strlen(getInfoForAuthor($value)["bio"])>1){
			$exploded = explode(" ", trim($value)); 
			$nameWithPlus="";
			for ($i=0; $i <sizeof($exploded ) ; $i++) { 
				$nameWithPlus.=$exploded [$i];
				$nameWithPlus.="+";
			}
			$code.="authorList.append(\"";
			$code.=substr($nameWithPlus, 0, -1);
			$code.="\")\n";
		}
	}
	echo $code;
	
?>