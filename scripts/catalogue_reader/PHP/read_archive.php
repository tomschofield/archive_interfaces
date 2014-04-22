<?php

//c01 [0] c02 (series level) = Published Poetry by Author

function foo(){
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
// //echo sizeof($xml->xpath('scopecontent'))
// foreach ($xml->xpath('//emph') as $c01) {
// 		echo "c01 ",$c01->attributes(),"\n";
// 	}

//echo sizeof($apath[0]);

//get the series names //eg Published Poetry by Author
function getSeriesNames(){
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;
	$seriesNames=array();
	foreach ($apath[0]->xpath('//c02') as $c02) {
		//echo "c02 ",$c02['level'],"\n";
		echo "series: ",$c02->did->unittitle->emph[0],"\n";
		array_push($seriesNames,(string)$c02->did->unittitle->emph[0]);
	}
	return $seriesNames;
}

function getSubseriesNames(){
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;
	$seriesNames=array();
	$c02 = $apath[0]->xpath('//c02')[0];
	foreach ( $c02 as $c03) {
		//echo "c02 ",$c02['level'],"\n";
		echo "subseries: ",$c03->did->unittitle,"\n";
		//array_push($seriesNames,(string)$c02->did->unittitle->emph[0]);
	}
	return $seriesNames;
}
//c03 is author level in the Published Poetry by Author 
function getFileLevelTitlesAndIds(){
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;
	
	$c02 = $apath[0]->xpath('//c02')[0];
	echo sizeof($c02),"\n";
	$count =0;
	foreach ( $c02 as $key=>$value) {
		//echo "c02 ",$c02['level'],"\n";
		//this is author level ie subseires
		echo $count," New subseries: ",$value->did->unittitle,"\n";
		$innnercount =0;
		foreach ($value->c04 as $c04) {
			echo $count," ",$innnercount, " file: ",$c04->did->unittitle," id : ",$c04->did->unitid,"\n";
			$innnercount++;
		}
		//array_push($seriesNames,(string)$c02->did->unittitle->emph[0]);
		$count++;
	}
	
}


/*
c02 is sub of editorial eg published poetry by aythor, anthologies, critisim etc
(in c02) 
	c03 is author
	c04 is file of stuff
	c05 is an item in a file (scan level)

confusingly both c03 and c04 are 'file' level
*/
function printRecords($recordID){

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
				
				$id = (string)$first_gen->unitid;
				$date = (string)$first_gen->unitdate;
				echo "Subseires (ie author ) title ",$title," id ",$id," date ",$date,"\n";
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
						echo "	File title ",$title," id ",$id," date ",$date,"\n";
					}
					//and now for individual items! 
					if($second_gen->getName()=='c05'){

						foreach ($second_gen->children() as $third_gen) {
							
							if($third_gen->getName()=='did'){
								//echo $first_gen['level'],"\n";
								$title = (string)$third_gen->unittitle;
								
								$id = (string)$third_gen->unitid;
								$date = (string)$third_gen->unitdate;
								echo "		Item title ",$title," id ",$id," date ",$date,"\n";
							}
						}	
					}
					
				}
			}
			//echo "	first _gen ",$first_gen->getName()," ",$first_gen['level'],"\n";
		}
		
	}
	
}
/*
c02 is sub of editorial eg published poetry by aythor, anthologies, critisim etc
(in c02) 
	c03 is author
	c04 is file of stuff
	c05 is an item in a file (scan level)

confusingly both c03 and c04 are 'file' level
*/
function getAuthorForItemAnywhere($itemID){

	$authorName="";
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;

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
	// 			echo "Subseires (ie author ) title ",$title," id ",$id," date ",$date,"\n";
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
						if($itemID==$id){
									
									$authorName = $thisAuthor;
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
									echo 'match';
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
	//echo getInfoForAuthor('David Constantine')["bio"],"\n";
	//printFileListForAuthor('Jackie Kay');
	//$myAuthor = getTitleForItemInCollections('BXB/1/2/TRE/5');
	$myAuthor = getAuthorForItem ('BXB/1/1/HEW/1/1');
	echo $myAuthor;

	$info =  getInfoForAuthor($myAuthor);
	foreach ($info as $key => $value) {
		echo $key , " : ",$value,"\n";
	}
	foreach ($info['fileList'] as $key => $value) {
		echo $key , " : ",$value,"\n";
		foreach ($value as $fileInfo) {
			echo $fileInfo,"\n";
		}
	}
?>