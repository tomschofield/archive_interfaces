<?php

//c01 [0] c02 (series level) = Published Poetry by Author
/*
c02 [0] = Published Poetry by Author
c02 [1] = Poetry Anthologies, Collaborations and Translations by Title
c02 [2] = Literary Criticism by Author
c02 [3] = Prose, Fiction and Photography by Author




*/




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
//will return list of auhtors in $series_num = 1, anthology titles in $series_num =2
function getSubseriesNames($series_num){
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;
	$seriesNames=array();
	$c02 = $apath[0]->xpath('//c02')[$series_num];
	foreach ( $c02 as $c03) {
		//echo "c02 ",$c02['level'],"\n";
		echo "subseries: ",$c03->did->unittitle,"\n";
		//array_push($seriesNames,(string)$c02->did->unittitle->emph[0]);
	}
	return $seriesNames;
}

//only in poetry by author
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
// function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
// {
//     // error was suppressed with the @-operator
//     if (0 === error_reporting()) {
//         return false;
//     }

//     throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
// }
// set_error_handler('handleError');

function getAnthologyItems(){
	$books=array();
	$xml=simplexml_load_file("edit.xml");

	$apath = $xml->xpath('//c01') ;

	$c02 = $apath[0]->xpath('//c02')[1];

	
	$count =0;
	foreach ( $c02 as $c03) {
		//echo $c03['level'],"\n";
		//c3 is anthology level : series
		$thisFileList = array();
		$anthTitle = "";
		$anthId = "";
		$anthDate = "";
		

		foreach ($c03->children() as $first_gen) {
			//echo "	",$first_gen['level'],"\n";
			// if($count<20){
			// 	echo $first_gen->getName(),"\n";
			// 	$count ++;
			// }
			if($first_gen->getName()=='did'){
				//echo $first_gen['level'],"\n";
				$anthTitle = (string)$first_gen->unittitle;
				$anthId = (string)$first_gen->unitid;
				$anthDate = (string)$first_gen->unitdate;
				
			}
			//mpw ;ets get the files
			//for each c04

			if($first_gen->getName()=='c04'){
				foreach ($first_gen->children() as $second_gen) {
					if($second_gen->getName()=='did'){
						$title = (string)$second_gen->unittitle;
						//$thisTitle = $title;

						$id = (string)$second_gen->unitid;
						$date = (string)$second_gen->unitdate;

						
					}
					if($second_gen->getName()=='scopecontent'){
						//echo "size of scopecontent ps ",sizeof($second_gen->p )," found \n";
						$scopecontent ="";
						if(sizeof($second_gen->p)>1 ){

						 	$scopecontent = (string)  implode ( "\n", $second_gen->p );

						}else{
							$scopecontent = $second_gen->p;
						}
						//echo ($second_gen->p);
						//
					}

				}
				$aFile = array(
				    "title" => $title ,
				    "scopecontent" => $scopecontent ,
				    "id" => $id,
				    "date"=> $date
				);
				array_push($thisFileList, $aFile);
				//echo "book title ",$thisTitle, " item title ",$title," id ",$id," date ",$date," scopecontent ",$scopecontent,"\n";
			}
			
		}
		$aBook = array(
		    "title" => $anthTitle ,
			"isAnthology"=>true,
		    "id" => $anthId,
		    "date"=> $anthDate,
		    "author"=>"none",
		    "individualFiles"=>$thisFileList
		);

		array_push($books, $aBook);
		
	}
	//print_r($books);
	return $books;

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
				$individualFiles = array();
				foreach ($first_gen->children() as $second_gen) {
					//echo $second_gen->getName(),"\n";
					if($second_gen->getName()=='did'){
						//echo $first_gen['level'],"\n";
						$fileTitle = (string)$second_gen->unittitle;
						
						$fileId = (string)$second_gen->unitid;
						$fileDate = (string)$second_gen->unitdate;
						
						
						//echo "	File title ",$title," id ",$id," date ",$date,"\n";
					}
					//and now for individual items! 
					if($second_gen->getName()=='c05'){

						foreach ($second_gen->children() as $third_gen) {
							
							if($third_gen->getName()=='did'){
								//echo $first_gen['level'],"\n";
								$ftitle = (string)$third_gen->unittitle;
								
								$fid = (string)$third_gen->unitid;
								$fdate = (string)$third_gen->unitdate;
								if($itemID==$id){
									
									$authorName = $thisAuthor;
								}
								

								
								//echo "		Item title ",$title," id ",$id," date ",$date,"\n";
							}
							if($third_gen->getName()=='scopecontent'){
								$fScopecontent="";
								foreach ($third_gen->p as $key => $value) {
									$fScopecontent .= (string)$value;
								}
							}

							$individualFile = array(
								    "title" => $ftitle ,
								    "id" => $fid,
								    "date"=> $fdate,
								    "scopecontent"=>$fScopecontent
							);
							//check exisrs
							$exists = false;
							foreach ($individualFiles as $testFile) {
								if($fid==$testFile['id']) $exists = true;
							}

							//if(!in_array($individualFile, $individualFiles)){
							if($exists==false)	array_push($individualFiles, $individualFile);
							//}
						}	
					}
					
				}
				if($collect){
					$aFile = array(
					    "title" => $fileTitle ,
					    "id" => $fileId,
					    "date"=> $fileDate,
					    "individualFiles"=>$individualFiles
					);
					//print_r($aFile);
					array_push($thisFileList, $aFile) ;
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
								$ftitle = (string)$third_gen->unittitle;
								$fid = (string)$third_gen->unitid;
								$fdate = (string)$third_gen->unitdate;

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

?>