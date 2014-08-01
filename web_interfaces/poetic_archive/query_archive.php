<?php
/*

This file is used to extract data from 'edit.xml' which has been created by Becky when physically cataloguing the data.
The XML file is a digital REFERENCE to the archive.

"SERIES" level name and return data type:
c02 [0] = Published Poetry BY AUTHOR
c02 [1] = Poetry Anthologies, Collaborations & Translations BY TITLE
c02 [2] = Literary Criticism BY AUTHOR
c02 [3] = Prose, Fiction & Photography BY AUTHOR
*/

function foo() {
// iterate through every c01/2/3/4 tag in the xml doc and return them as an array with name 'c0*' //
	foreach ($xml->xpath('//c01') as $c01) {
		echo "c01 ",$c01['level'],"\n";
	}

	foreach ($xml->xpath('//c02') as $c02) {
		echo "c02 ",$c02['level'],"\n";
	}

	foreach ($xml->xpath('//c03') as $c03) {
		echo "c03 ",$c03['level'],"\n";
	}

	foreach ($xml->xpath('//c04') as $c04) {
		echo "c04 ",$c04['level'],"\n";
	}

	foreach ($xml->xpath('//c04') as $c04) {
		echo "c04 ",$c04['level'],"\n";
	}
}
/* TEST: echo sizeof($xml->xpath('scopecontent'))
		 foreach ($xml->xpath('//emph') as $c01) {
 		 echo "c01 ",$c01->attributes(),"\n";
 		 }
*/

// TEST: echo sizeof($apath[0]);

//get the series names (c02) e.g. Published Poetry by Author //
function getSeriesNames() {
	// DUPLICATED THROUGHOUT VARIOUS FUNCTIONS - load the relevant 'edit.xml' file that we want to extract data from //
	$xml=simplexml_load_file("edit.xml");
	// DUPLICATED THROUGHOUT VARIOUS FUNCTIONS - create a variable called 'apath' which stores all the 'c01' tagged data //
	$apath = $xml->xpath('//c01') ;
	// DUPLICATED THROUGHOUT VARIOUS FUNCTIONS - create new array called 'seriesNames' //
	// should establish variable names that are expected in the array here (title, name etc.) to avoid spurious attacks //
	// e.g. $expected = array('name', 'title', 'bioghist') //
	$seriesNames=array();
	// before adding items to the array check that the variable exists in 'expected' before inserting //
	// starting at first index [0] for 'c01', Editorial branch, iterate through all data tagged under 'c02' //
	foreach ($apath[0]->xpath('//c02') as $c02) {
		// TEST: echo "c02 ",$c02['level'],"\n";
		// retrieves by hierarchical tag location, for first 'emph' tag (string) //
		echo "series: ",$c02->did->unittitle->emph[0],"\n";
		// add the value to the array as a string //
		array_push($seriesNames,(string)$c02->did->unittitle->emph[0]);
	}
	return $seriesNames;
}

// get subseries names (c03, <subseries>) e.g. Robert Adamson (author name) //
function getSubseriesNames() {
	// load the relevant 'edit.xml' file that we want to extract data from //
	$xml=simplexml_load_file("edit.xml");
	// create a variable called 'apath' which stores all the 'c01' tagged data //
	$apath = $xml->xpath('//c01') ;
	// create new array called 'seriesNames' //
	$seriesNames=array();
	// variable 'c02' is first instance of c02 <series> that is contained in first instance of c01 //
	$c02 = $apath[0]->xpath('//c02')[0];

	foreach ( $c02 as $c03) {
		// TEST: echo "c02 ",$c02['level'],"\n";
		echo "subseries: ",$c03->did->unittitle,"\n";
		// WHY COMMENTED OUT? array_push($seriesNames,(string)$c02->did->unittitle->emph[0]);
	}
	return $seriesNames;
}
// c03 <subseries> is level below c02 <series> , e.g. author name in the Published Poetry by Author, title name in the Poetry Anthologies... by Title //
function getFileLevelTitlesAndIds() {
	// DUPLICATE
	$xml=simplexml_load_file("edit.xml");
	// DUPLICATE
	$apath = $xml->xpath('//c01') ;
	// DUPLICATE: variable 'c02' is first instance of c02 that is contained in first instance of c01 //
	$c02 = $apath[0]->xpath('//c02')[0];
	// why are we returning the number of c02 data elements? //
	echo sizeof($c02),"\n";
	$count =0;
	// iterate through c02 <series> elements as 'key', which represents each tag under c02 (e.g. <unittitle>) //
	// mapped to its value (data, e.g. 'Published Poetry by Author') //
	foreach ( $c02 as $key=>$value) {
		// TEST: echo "c02 ",$c02['level'],"\n"; //
		// this is c03? i.e. <subseries> //
		echo $count,"New subseries: ",$value->did->unittitle,"\n";
		$innnercount =0;
		// for each of the values found in previous foreach, map to elements of type c04 //
		foreach ($value->c04 as $c04) {
			// i.e. 00, 01, 02, 03 until no more c04 elements, leave foreach and go to next c03, 10
			echo $count," ",$innnercount, " file: ",$c04->did->unittitle," id : ",$c04->did->unitid,"\n";
			$innnercount++;
		}
		// array hasn't been initialised, why is it commented out here? //
		// array_push($seriesNames,(string)$c02->did->unittitle->emph[0]);
		$count++;
	}
	
}


/*
	c02 <series> is sub of c01 <subfonds> 'editorial' e.g. published poetry by aythor, anthologies, critisim etc.
	c03 <level> is author names
	c04 <file> is file of stuff
	c05 <item> is an item in a file (scanned level, portfolio)
*/
// print out records according to a given parameter, recordID //
function printRecords($recordID){
	// create variable to hold author name as empty string //
	$authorName="";
	// DUPLICATE //
	$xml=simplexml_load_file("edit.xml");
	// DUPLICATE //
	$apath = $xml->xpath('//c01') ;
	// DUPLICATE //
	$c02 = $apath[0]->xpath('//c02')[0];

	// iterate through c02 > c03 > c04 > c05 sequentially retrieving & displaying all records //
	$count =0;
	foreach ( $c02 as $c03) {
		// TEST: echo $c03['level'],"\n"; //
		// c03 is author name <series> //
		// children() SimpleXMLElement, finds all children of a given node, then stores as 'first_gen' //
		foreach ($c03->children() as $first_gen) {
			// this level will give us each authors metadata
			// echo "	",$first_gen['level'],"\n";
			// getName() SimpleXMLElement, returns name of XML element //
			// checks which XML element child node represents and then displays data accordingly //
			if($first_gen->getName()=='did') {
				// TEST: echo $first_gen['level'],"\n";
				// title of book //
				$title = (string)$first_gen->unittitle;
				// id value of book, e.g. 'BXB/1/1/KFT/1' //
				$id = (string)$first_gen->unitid;
				// published year //
				$date = (string)$first_gen->unitdate;
				echo "Subseries (i.e. author) title: ",$title," ID: ",$id," date: ",$date,"\n";
			}
			if($first_gen->getName()=='bioghist'){
				// why commented out? //
				//echo "bioghist p  ",$first_gen->p,"\n";
			}
			if($first_gen->getName()=='scopecontent'){
				// why commented out? //
				//echo "scopecontent p  ",$first_gen->p,"\n";
			}

			//and now for individual files, c04 <file> - that doesn't mean individual items! (c05, <item>) //
			if($first_gen->getName()=='c04') {
				//go through each file for each author //
				foreach ($first_gen->children() as $second_gen) {
					//echo $second_gen->getName(),"\n"; //
					if($second_gen->getName()=='did') {
						//echo $first_gen['level'],"\n"; //
						$title = (string)$second_gen->unittitle;
						$id = (string)$second_gen->unitid;
						$date = (string)$second_gen->unitdate;
						echo "File title: ",$title," ID: ",$id," date: ",$date,"\n";
					}
					//and now for individual items! c05 <item> // 
					if($second_gen->getName()=='c05') {

						foreach ($second_gen->children() as $third_gen) {
							
							if($third_gen->getName()=='did'){
								// TEST: echo $first_gen['level'],"\n";
								$title = (string)$third_gen->unittitle;
								$id = (string)$third_gen->unitid;
								$date = (string)$third_gen->unitdate;
								echo "Item title: ",$title," ID: ",$id," date: ",$date,"\n";
							}
						}	
					}
					
				}
			}
			//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
		}
		
	}
	
}

// given an itemID this function returns the name of the author associated with that item //
function getAuthorForItemAnywhere($itemID) {

	$authorName="";
	// DUPLICATE //
	$xml=simplexml_load_file("edit.xml");
	// DUPLICATE //
	$apath = $xml->xpath('//c01') ;
	// 2 variables from 1st instance of c01 <subfond>, c02 from 1st instance of c02 <series> & c02s from any instance of c02 <series> //
	$c02 = $apath[0]->xpath('//c02')[0];
	$c02s = $apath[0]->xpath('//c02');
	// foreach ( $c02s as $c03) {
	// 	foreach ( $c02s as $c03) {
	// 	//echo $c03['level'],"\n";
	// 	//c3 is author level : series
	// 	foreach ($c03->children() as $first_gen) {
	// 		if($first_gen->getName()=='did'){
	// 			//echo $first_gen['level'],"\n";
	// 			$title = (string)$first_gen->unittitle;
	// 			$thisAuthor = $title;
	// 			$id = (string)$first_gen->unitid;
	// 			$date = (string)$first_gen->unitdate;
	// 			echo "Subseries (ie author ) title ",$title," id ",$id," date ",$date,"\n";
	// 		}
	// 	}
	// }
	// }
	$count =0;
	foreach ( $c02s as $c02) {
		foreach ( $c02 as $c03) {
			//echo $c03['level'],"\n";
			//c3 is author level : series
			foreach ($c03->children() as $first_gen) {
				//this level will give us each authors stuff
				//echo "	",$first_gen['level'],"\n";
				if($first_gen->getName()=='did') {
					//echo $first_gen['level'],"\n";
					$title = (string)$first_gen->unittitle;
					$thisAuthor = $title;
					$id = (string)$first_gen->unitid;
					$date = (string)$first_gen->unitdate;
					//echo "Subseries (ie author ) title ",$title," id ",$id," date ",$date,"\n";
				}

				if($first_gen->getName()=='bioghist') {
				
					//echo "bioghist p  ",$first_gen->p,"\n";
				}

				if($first_gen->getName()=='scopecontent') {
				
				//echo "scopecontent p  ",$first_gen->p,"\n";
				}

				//and now for individual files - that doesn\t mean individual items!
				if($first_gen->getName()=='c04') {
					//go through each file for each author
					foreach ($first_gen->children() as $second_gen) {
						//echo $second_gen->getName(),"\n";
						if($second_gen->getName()=='did') {
							//echo $first_gen['level'],"\n";
							$title = (string)$second_gen->unittitle;
						
							$id = (string)$second_gen->unitid;
							$date = (string)$second_gen->unitdate;
							//echo "	File title ",$title," id ",$id," date ",$date,"\n";
						}
						//and now for individual items! 
						if($second_gen->getName()=='c05') {

							foreach ($second_gen->children() as $third_gen) {
							
								if($third_gen->getName()=='did') {
									//echo $first_gen['level'],"\n";
									$title = (string)$third_gen->unittitle;
								
									$id = (string)$third_gen->unitid;
									if($itemID==$id) {
									
										$authorName = $thisAuthor;
									}
									$date = (string)$third_gen->unitdate;
									//echo "		Item title ",$title," id ",$id," date ",$date,"\n";
								}
							}	
						}
					
					}
				}
				//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
			}
		
		}
	}
	return $authorName;
}

function getAuthorForItem($itemID){

	$authorName="";
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;

	$c02 = $apath[0]->xpath('//c02')[0];

	
	$count =0;
	foreach ( $c02 as $c03) {
		//echo $c03['level'],"\n";
		//c3 is author level : series
		foreach ($c03->children() as $first_gen) {
			//this level will give us each authors stuff
			//echo "	",$first_gen['level'],"\n";
			if($first_gen->getName()=='did'){
				//echo $first_gen['level'],"\n";
				$title = (string)$first_gen->unittitle;
				$thisAuthor = $title;
				$id = (string)$first_gen->unitid;
				$date = (string)$first_gen->unitdate;
				//echo "Subseires (ie author ) title ",$title," id ",$id," date ",$date,"\n";
			}
			if($first_gen->getName()=='bioghist'){
				
				//echo "bioghist p  ",$first_gen->p,"\n";
			}
			if($first_gen->getName()=='scopecontent'){
				
				//echo "scopecontent p  ",$first_gen->p,"\n";
			}

			//and now for individual files - that doesn\t mean individual items!
			if($first_gen->getName()=='c04'){
				//go through each file for each author
				foreach ($first_gen->children() as $second_gen) {
					//echo $second_gen->getName(),"\n";
					if($second_gen->getName()=='did'){
						//echo $first_gen['level'],"\n";
						$title = (string)$second_gen->unittitle;
						
						$id = (string)$second_gen->unitid;
						$date = (string)$second_gen->unitdate;
						//echo "	File title ",$title," id ",$id," date ",$date,"\n";
					}
					//and now for individual items! 
					if($second_gen->getName()=='c05'){

						foreach ($second_gen->children() as $third_gen) {
							
							if($third_gen->getName()=='did'){
								//echo $first_gen['level'],"\n";
								$title = (string)$third_gen->unittitle;
								
								$id = (string)$third_gen->unitid;
								if($itemID==$id){
									
									$authorName = $thisAuthor;
								}
								$date = (string)$third_gen->unitdate;
								//echo "		Item title ",$title," id ",$id," date ",$date,"\n";
							}
						}	
					}
					
				}
			}
			//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
		}
		
	}
	return $authorName;
}
function getAuthorAndTitleForItem($itemID){

	$authorName="";
	$titleName = "";
	$itemTitle = "";
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;

	$c02 = $apath[0]->xpath('//c02')[0];

	
	$count =0;
	foreach ( $c02 as $c03) {
		//echo $c03['level'],"\n";
		//c3 is author level : series
		foreach ($c03->children() as $first_gen) {
			//this level will give us each authors stuff
			//echo "	",$first_gen['level'],"\n";
			if($first_gen->getName()=='did'){
				//echo $first_gen['level'],"\n";
				$title = (string)$first_gen->unittitle;
				$thisAuthor = $title;
				$id = (string)$first_gen->unitid;
				$date = (string)$first_gen->unitdate;
				//echo "Subseires (ie author ) title ",$title," id ",$id," date ",$date,"\n";
			}
			if($first_gen->getName()=='bioghist'){
				
				//echo "bioghist p  ",$first_gen->p,"\n";
			}
			if($first_gen->getName()=='scopecontent'){
				
				//echo "scopecontent p  ",$first_gen->p,"\n";
			}

			//and now for individual files - that doesn\t mean individual items!
			if($first_gen->getName()=='c04'){
				//go through each file for each author
				foreach ($first_gen->children() as $second_gen) {
					//echo $second_gen->getName(),"\n";
					if($second_gen->getName()=='did'){
						//echo $first_gen['level'],"\n";
						$title = (string)$second_gen->unittitle;
						$itemTitle = $title;
						$id = (string)$second_gen->unitid;
						$date = (string)$second_gen->unitdate;
						if($itemID==$id){
									//$titleName = (string)$third_gen->unittitle;
									$authorName = $thisAuthor;
									$finalItemTitle = $itemTitle;
						}
						//echo "	File title ",$title," id ",$id," date ",$date,"\n";
					}
					//and now for individual items! 
					if($second_gen->getName()=='c05'){

						foreach ($second_gen->children() as $third_gen) {
							
							if($third_gen->getName()=='did'){
								//echo $first_gen['level'],"\n";
								$title = (string)$third_gen->unittitle;
								
								$id = (string)$third_gen->unitid;
								
								$date = (string)$third_gen->unitdate;
								//echo "		Item title ",$title," id ",$id," date ",$date,"\n";
							}
						}	
					}
					
				}
			}
			//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
		}
		
	}
	$authorAndTitle = array();
	array_push($authorAndTitle, $authorName);
	array_push($authorAndTitle, $finalItemTitle);
	array_push($authorAndTitle, $titleName);

	return $authorAndTitle;
}
function getAuthorAndTitleForItemFileLevelItem($itemID){

	$authorName="";
	$titleName = "";
	$itemTitle = "";
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;

	$c02 = $apath[0]->xpath('//c02')[0];

	
	$count =0;
	foreach ( $c02 as $c03) {
		//echo $c03['level'],"\n";
		//c3 is author level : series
		foreach ($c03->children() as $first_gen) {
			//this level will give us each authors stuff
			//echo "	",$first_gen['level'],"\n";
			if($first_gen->getName()=='did'){
				//echo $first_gen['level'],"\n";
				$title = (string)$first_gen->unittitle;
				$thisAuthor = $title;
				$id = (string)$first_gen->unitid;
				$date = (string)$first_gen->unitdate;
				//echo "Subseires (ie author ) title ",$title," id ",$id," date ",$date,"\n";
			}
			if($first_gen->getName()=='bioghist'){
				
				//echo "bioghist p  ",$first_gen->p,"\n";
			}
			if($first_gen->getName()=='scopecontent'){
				
				//echo "scopecontent p  ",$first_gen->p,"\n";
			}

			//and now for individual files - that doesn\t mean individual items!
			if($first_gen->getName()=='c04'){
				//go through each file for each author
				foreach ($first_gen->children() as $second_gen) {
					//echo $second_gen->getName(),"\n";
					if($second_gen->getName()=='did'){
						//echo $first_gen['level'],"\n";
						$title = (string)$second_gen->unittitle;
						$itemTitle = $title;
						$id = (string)$second_gen->unitid;
						$date = (string)$second_gen->unitdate;
						//echo "	File title ",$title," id ",$id," date ",$date,"\n";
					}
					//and now for individual items! 
					if($second_gen->getName()=='c05'){

						foreach ($second_gen->children() as $third_gen) {
							
							if($third_gen->getName()=='did'){
								//echo $first_gen['level'],"\n";
								$title = (string)$third_gen->unittitle;
								
								$id = (string)$third_gen->unitid;
								if($itemID==$id){
									$titleName = (string)$third_gen->unittitle;
									$authorName = $thisAuthor;
									$finalItemTitle = $itemTitle;
								}
								$date = (string)$third_gen->unitdate;
								//echo "		Item title ",$title," id ",$id," date ",$date,"\n";
							}
						}	
					}
					
				}
			}
			//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
		}
		
	}
	$authorAndTitle = array();
	array_push($authorAndTitle, $authorName);
	array_push($authorAndTitle, $finalItemTitle);
	array_push($authorAndTitle, $titleName);

	return $authorAndTitle;
}
function getItemListFromPoetryByAuthor(){

	$authorName="";
	$itemList = array();
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;

	$c02 = $apath[0]->xpath('//c02')[0];

	
	$count =0;
	foreach ( $c02 as $c03) {
		//echo $c03['level'],"\n";
		//c3 is author level : series
		foreach ($c03->children() as $first_gen) {
			//this level will give us each authors stuff
			//echo "	",$first_gen['level'],"\n";
			if($first_gen->getName()=='did'){
				//echo $first_gen['level'],"\n";
				$title = (string)$first_gen->unittitle;
				$thisAuthor = $title;
				$id = (string)$first_gen->unitid;
				$date = (string)$first_gen->unitdate;
				//echo "Subseires (ie author ) title ",$title," id ",$id," date ",$date,"\n";
			}
			if($first_gen->getName()=='bioghist'){
				
				//echo "bioghist p  ",$first_gen->p,"\n";
			}
			if($first_gen->getName()=='scopecontent'){
				
				//echo "scopecontent p  ",$first_gen->p,"\n";
			}

			//and now for individual files - that doesn\t mean individual items!
			if($first_gen->getName()=='c04'){
				//go through each file for each author
				foreach ($first_gen->children() as $second_gen) {
					//echo $second_gen->getName(),"\n";
					if($second_gen->getName()=='did'){
						//echo $first_gen['level'],"\n";
						$title = (string)$second_gen->unittitle;
						
						$id = (string)$second_gen->unitid;
						$date = (string)$second_gen->unitdate;
						//echo "	File title ",$title," id ",$id," date ",$date,"\n";
					}
					//and now for individual items! 
					if($second_gen->getName()=='c05'){

						foreach ($second_gen->children() as $third_gen) {
							
							if($third_gen->getName()=='did'){
								//echo $first_gen['level'],"\n";
								$title = (string)$third_gen->unittitle;
								
								$id = (string)$third_gen->unitid;
								array_push($itemList, $id);
								
								$date = (string)$third_gen->unitdate;
								//echo "		Item title ",$title," id ",$id," date ",$date,"\n";
							}
						}	
					}
					
				}
			}
			//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
		}
		
	}
	return $itemList;
}
function getAuthorListFromPoetryByAuthor(){

	$authorNames=array();
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;

	$c02 = $apath[0]->xpath('//c02')[0];

	
	$count =0;
	foreach ( $c02 as $c03) {
		//echo $c03['level'],"\n";
		//c3 is author level : series
		foreach ($c03->children() as $first_gen) {
			//this level will give us each authors stuff
			//echo "	",$first_gen['level'],"\n";
			if($first_gen->getName()=='did'){
				//echo $first_gen['level'],"\n";
				$title = (string)$first_gen->unittitle;
				$thisAuthor = $title;
				array_push($authorNames, $thisAuthor);
				$id = (string)$first_gen->unitid;
				$date = (string)$first_gen->unitdate;
				//echo "Subseires (ie author ) title ",$title," id ",$id," date ",$date,"\n";
			}

			//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
		}
		
	}
	return $authorNames;
}
function getTitleForItemInCollections($itemID){

	$authorName="";
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;

	$c02 = $apath[0]->xpath('//c02')[1];

	
	$count =0;
	foreach ( $c02 as $c03) {
		//echo $c03['level'],"\n";
		//c3 is author level : series
		foreach ($c03->children() as $first_gen) {
			//this level will give us each authors stuff
			//echo "	",$first_gen['level'],"\n";
			if($first_gen->getName()=='did'){
				//echo $first_gen['level'],"\n";
				$title = (string)$first_gen->unittitle;
				$thisAuthor = $title;
				$id = (string)$first_gen->unitid;
				$date = (string)$first_gen->unitdate;
				//echo "Subseires (ie title of collected word ) title ",$title," id ",$id," date ",$date,"\n";
			}
			if($first_gen->getName()=='bioghist'){
				
				//echo "bioghist p  ",$first_gen->p,"\n";
			}
			if($first_gen->getName()=='scopecontent'){
				
				//echo "scopecontent p  ",$first_gen->p,"\n";
			}

			//and now for individual files - that doesn\t mean individual items!
			if($first_gen->getName()=='c04'){
				//go through each file for each author
				foreach ($first_gen->children() as $second_gen) {
					//echo $second_gen->getName(),"\n";
					if($second_gen->getName()=='did'){
						//echo $first_gen['level'],"\n";
						$title = (string)$second_gen->unittitle;
						
						$id = (string)$second_gen->unitid;
						//echo $itemID,' ',$id,"\n";
						$date = (string)$second_gen->unitdate;
						if($itemID==$id){
									//echo 'found';
									$authorName = $thisAuthor;
						}
						//echo "	File title ",$title," id ",$id," date ",$date,"\n";
					}
					//and now for individual items! - OOPS there are none in the collections
					// if($second_gen->getName()=='c05'){

					// 	foreach ($second_gen->children() as $third_gen) {
							
					// 		if($third_gen->getName()=='did'){
					// 			//echo $first_gen['level'],"\n";
					// 			$title = (string)$third_gen->unittitle;
								
					// 			$id = (string)$third_gen->unitid;
					// 			echo $id,"\n";
					// 			$date = (string)$third_gen->unitdate;
					// 			if($itemID==$id){
					// 				echo 'found';
					// 			}
					// 			//echo "		Item title ",$title," id ",$id," date ",$date,"\n";
					// 		}
					// 	}	
					// }
					
				}
			}
			//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
		}
		
	}
	return $authorName;
}
/*
c02 is sub of editorial eg published poetry by aythor, anthologies, critisim etc
(in c02) 
	c03 is author
	c04 is file of stuff
	c05 is an item in a file (scan level)

confusingly both c03 and c04 are 'file' level
*/
function getInfoForAuthor($authorName){

	$itemID="";
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;

	$c02 = $apath[0]->xpath('//c02')[0];
	
	$thisAuthor ="";
	$thisBio ="";
	$thisId = "";
	$thisDate = "";
	$thisScopecontent = "";
	$thisFileList=array();
	
	$count =0;
	//for each author in s03
	foreach ( $c02 as $c03) {
	
		
		//echo $c03['level'],"\n";
		//c3 is author level : series
		foreach ($c03->children() as $first_gen) {
			//this level will give us each authors stuff
			//echo "	",$first_gen['level'],"\n";
			if($first_gen->getName()=='did'){
				//echo $first_gen['level'],"\n";
				$title = (string)$first_gen->unittitle;
				if($title==$authorName){
					$collect = true;
					$thisAuthor = $title;
				}
				else{
					$collect = false;
				}
				$id = (string)$first_gen->unitid;

				$date = (string)$first_gen->unitdate;
				if($collect){
					$thisId = $id;
					$thisDate = $date;
				}


				//echo "Subseires (ie author ) title ",$title," id ",$id," date ",$date,"\n";
			}
			
			if($first_gen->getName()=='bioghist'){
				if($collect){
					foreach ($first_gen->p as $key => $value) {
						$thisBio .= (string)$value;
					}
					
				}
					//echo "bioghist p  ",$first_gen->p,"\n";
			}
			if($first_gen->getName()=='scopecontent'){
				
				//echo "scopecontent p  ",$first_gen->p,"\n";
				if($collect){
					foreach ($first_gen->p as $key => $value) {
						$thisScopecontent .= (string)$value;
					}
					
				}
			}
			
			//and now for individual files - that doesn\t mean individual items!
			if($first_gen->getName()=='c04'){
				//go through each file for each author
				foreach ($first_gen->children() as $second_gen) {
					//echo $second_gen->getName(),"\n";
					if($second_gen->getName()=='did'){
						//echo $first_gen['level'],"\n";
						$title = (string)$second_gen->unittitle;
						
						$id = (string)$second_gen->unitid;
						$date = (string)$second_gen->unitdate;
						
						if($collect){
							$aFile = array(
							    "title" => $title ,
							    "id" => $id,
							    "date"=> $date
							);
							array_push($thisFileList, $aFile) ;
						}
						//echo "	File title ",$title," id ",$id," date ",$date,"\n";
					}
					//and now for individual items! 
					if($second_gen->getName()=='c05'){

						foreach ($second_gen->children() as $third_gen) {
							
							if($third_gen->getName()=='did'){
								//echo $first_gen['level'],"\n";
								$title = (string)$third_gen->unittitle;
								
								$id = (string)$third_gen->unitid;
								if($itemID==$id){
									
									$authorName = $thisAuthor;
								}
								$date = (string)$third_gen->unitdate;
								//echo "		Item title ",$title," id ",$id," date ",$date,"\n";
							}
						}	
					}
					
				}
			}
			
		//end 
			//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
		}
		
		
	}
	//echo $thisAuthor," ",$thisId," ",$date;
	$aRecord= array(
	    "author" => $thisAuthor,
	    "bio" => $thisBio,
	    "id" => $thisId,
	    "date"=> $date,
	    "scopecontent" => $thisScopecontent,
	    "fileList" => $thisFileList
	);
	return $aRecord;
}

function getInfoForItem($itemID){

	
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;

	$c02 = $apath[0]->xpath('//c02')[0];
	
	$thisAuthor ="";
	$thisBio ="";
	$thisId = "";
	$thisDate = "";
	$thisScopecontent = "";
	$thisFileList=array();
	
	$count =0;
	//for each author in s03
	foreach ( $c02 as $c03) {
		$thisAuthor ="";
		$thisBio ="";
		$thisId = "";
		$thisDate = "";
		$thisScopecontent = "";
		
		//echo $c03['level'],"\n";
		//c3 is author level : series
		foreach ($c03->children() as $first_gen) {
			//this level will give us each authors stuff
			//echo "	",$first_gen['level'],"\n";
			if($first_gen->getName()=='did'){
				//echo $first_gen['level'],"\n";
				$thisAuthor = (string)$first_gen->unittitle;
				
				$id = (string)$first_gen->unitid;

				$date = (string)$first_gen->unitdate;
				
				$thisId = $id;
				$thisDate = $date;
				


				//echo "Subseires (ie author ) title ",$title," id ",$id," date ",$date,"\n";
			}
			
			if($first_gen->getName()=='bioghist'){
				foreach ($first_gen->p as $key => $value) {
					$thisBio .= (string)$value;
				}
			}
			if($first_gen->getName()=='scopecontent'){
				
				//echo "scopecontent p  ",$first_gen->p,"\n";
				if($collect){
					foreach ($first_gen->p as $key => $value) {
						$thisScopecontent .= (string)$value;
					}
					
				}
			}
			
			//and now for individual files - that doesn\t mean individual items!
			if($first_gen->getName()=='c04'){
				//go through each file for each author
				foreach ($first_gen->children() as $second_gen) {
					//echo $second_gen->getName(),"\n";
					if($second_gen->getName()=='did'){
						//echo $first_gen['level'],"\n";
						$title = (string)$second_gen->unittitle;
						
						$id = (string)$second_gen->unitid;
						$date = (string)$second_gen->unitdate;
						
						
							$aFile = array(
							    "title" => $title ,
							    "id" => $id,
							    "date"=> $date
							);
							array_push($thisFileList, $aFile) ;
						
						//echo "	File title ",$title," id ",$id," date ",$date,"\n";
					}
					//and now for individual items! 
					if($second_gen->getName()=='c05'){

						foreach ($second_gen->children() as $third_gen) {
							
							if($third_gen->getName()=='did'){
								//echo $first_gen['level'],"\n";
								$title = (string)$third_gen->unittitle;
								
								$id = (string)$third_gen->unitid;
								if($itemID==$id){
									
									$authorName = $thisAuthor;
									$aRecord= array(
									    "author" => $thisAuthor,
									    "bio" => $thisBio,
									    "id" => $thisId,
									    "date"=> $date,
									    "scopecontent" => $thisScopecontent,
									    "fileList" => $thisFileList
									);
									return $aRecord;
									//echo 'match';
								}
								$date = (string)$third_gen->unitdate;
								//echo "		Item title ",$title," id ",$id," date ",$date,"\n";
							}
						}	
					}
					
				}
			}
			
		//end 
			//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
		}
		
		
	}
	//echo $thisAuthor," ",$thisId," ",$date;
	return 'no match';
}

function printFileListForAuthor($author){
	$myar = getInfoForAuthor($author)["fileList"];
	foreach ($myar as $key => $value) {
		
		foreach ($value as $a) {
			echo $a," ";
		}
		echo "\n";
	}
}
function getFondFromRef($ref){
	$exploded = explode('/', $ref);
		//echo $exploded,"\n";

	if(sizeof($exploded)>0){
		return $exploded[2];
	}
	else{
		return 0;
	}
}
function getItemRefFromFileName($filename){
	$exploded = explode('.', $filename);
	$exploded1 = explode(' ', $exploded[0]);
	$replaced = str_replace ( '-' , '/' ,$exploded1[0] );
	return $replaced;
}
function getAuthorAbbrFromRef($ref){
			//eg BXB/1/1/ZEP/2/3
		$exploded = explode('/', $ref);
		return $exploded[3];
}
function generateAuthorRefMappingList(){
	$authors = getAuthorListFromPoetryByAuthor();
	$data = array();
		foreach ($authors as $key => $value) {
		$theseItems = getInfoForAuthor($value)["fileList"];
		$aRef = getAuthorAbbrFromRef($theseItems[0]["id"]);
		//echo $value," : ",$aRef,", ";
		$data[$aRef]= $value;
		
	}
	//echo "\n";
	return $data;
}
function getAuthorNameFromAbr($abr, $lookup){
	return $lookup[$abr];
}
//print_r(getAuthorListFromPoetryByAuthor());
//$mapList = generateAuthorRefMappingList();
//echo getAuthorNameFromAbr("MUR", $mapList);
//BXB-1-2-TEN-1.pdf
	//echo getInfoForAuthor('David Constantine')["bio"],"\n";

	//echo  getTitleForItemInCollections($ref);
$authors = getAuthorListFromPoetryByAuthor();
foreach ($authors as $author) {
	$info = getInfoForAuthor($author);
	echo $info['thisBio'],"\n";

/*	$aRecord= array(
	    "author" => $thisAuthor,
	    "bio" => $thisBio,
	    "id" => $thisId,
	    "date"=> $date,
	    "scopecontent" => $thisScopecontent,
	    "fileList" => $thisFileList
	);*/
}

?>