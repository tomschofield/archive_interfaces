<?php
	//reads the standard catalogue
 	include 'read_archive.php';
 	//ini_set('memory_limit', '-1');
 	// //reads the portfolio data dump
 	// include 'get_book_db.php';
 	// //reads the box log
 	// include 'clean_box_log.php';


 	//print_r(getSeriesNames());
 	//print_r(getAuthorListFromPoetryByAuthor());
 	function makePeopleTable(){
 		$people = array();

	 	$peopleInPoetryByAuthor = getAuthorListFromPoetryByAuthor();
	 	

	 	foreach($peopleInPoetryByAuthor as $aPerson){
	 		//echo $aPerson, "\n";
	 		//array_push($people, getInfoForAuthor($aPerson));

	 		//index by auhtor name as array key
	 		$info = getInfoForAuthor($aPerson);
	 		//print_r($info);
	 		$people[$info['id']] = $info;
	 	}
	 	file_put_contents("people.json", json_encode($people));
	 	return $people;
 	}
 	function makeBooksTable(){

 		$books = array();
 		$people = json_decode(file_get_contents("people.json"),true);
 		$portfolioBooks = json_decode(file_get_contents("portfolio.json"),true);
 		//print_r($portfolioBooks);
 		
 		$bookList  =array();

 		//add the books from poetry by author
 		foreach ($people as $person) {
 			$fileList = $person['fileList'];
 			//print_r($person);
 			foreach ($fileList as $book){
 				//echo "NEW BOOK\n";
 				
 				//should contain list of files for each book
 				$thisFileList = array();
				$aBook = array(
				    "title" => $book['title'] ,
					"isAnthology"=>false,
				    "id" => $book['id'],
				    "date"=> $book['date'],
				    "author"=>$person['author'],
		    		"individualFiles"=>$book['individualFiles']
				);
 				//array_push($bookList,$aBook );
 				$bookList[$aBook['id']] = $aBook;
 			}
 			
 		}
 		//add the books from poetry collections
 		$anthBooks = getAnthologyItems();

 		$allBooks = array_merge($anthBooks, $bookList);
 		//now go through the poetry by author and look up the title in portfolio db
 		$newBooks = array();

 		//this doesn't actually write over the array so I cheat and make a new one
 		foreach ($allBooks as $book) {
 			$book['hasCover'] = false;
 			foreach ($portfolioBooks as $pBook) {

 				//Selected Poems
 				//Complete Poems
 				//Collected Poems
 				$bookTitle =trim(strtolower($book['title']));
 				$pBookTitle =trim(strtolower($pBook['title']));

 				$sbookTitle = preg_replace('#[^a-zA-Z0-9]#', '', $bookTitle);
 				$spBookTitle = preg_replace('#[^a-zA-Z0-9]#', '', $pBookTitle);
 				if($bookTitle==$pBookTitle ){//||$sbookTitle == $spBookTitle){

 					if($book['title']=='Selected Poems' ||$book['title']=='Complete Poems'||$book['title']=='Collected Poems'  ||$book['title']=='New and Collected Poems' ||$book['title']=='New and Selected Poems'){
 						//print_r( );
 						$creators = $pBook['creators'];
 						foreach($creators as $creator){
 							if($creator == $book['author']){
 								// print_r($book['author']);
 								// echo "/////////////////////////////////////\n";
 								$book['cover_info'] = $pBook;
 								$book['hasCover'] = true;
 								//print_r($book);
 							}

 						}
 						$pFname = $pBook['fname'];
	 					$exploded = explode("-", $pFname);
	 					$portfolioCode = substr($exploded[1], 0,3);

	 					$catalogueID = $book['id'];
	 					$exploded = explode("/", $catalogueID);
	 					$catalogueCode = substr($exploded[3], 0,3);
	 					
	 					if($catalogueCode == $portfolioCode){
	 						$book['cover_info'] = $pBook;
 							$book['hasCover'] = true;
	 					}

 					// echo $book['title'], " ", $portfolioCode," ", $catalogueCode, "\n";
 					}
 					else{
 						$book['cover_info'] = $pBook;
 						$book['hasCover'] = true;
 						// print_r($book['title']);
 						// echo "\n";
 					}
 				//	$book['cover_info'] = $pBook;
 				//	$book['hasCover'] = true;
 					//echo $pBook['title']," is a match\n";
 				}
 				// else if($sbookTitle == $spBookTitle){
 				// 	echo $bookTitle," ",$pBookTitle,"\n";
 				// }

 			}

 			// if($book['hasCover']){
 			// 	if (strlen($book['cover_info']['fname'])!=20){
 			// 		echo $book['cover_info']['fname'], " caught exception\n" ;
 			// 	}
 				
 			// }
 			//array_push($newBooks, $book);
 			$newBooks[$book['id']] = $book;
 			//$newBooks[$book['title']] = $book;
 		}
 		//print_r($bookList);
 		file_put_contents("books.json", json_encode($newBooks));
 		return $newBooks;

 	}
 	function makeDataBase(){
 		$people = json_decode(file_get_contents("people.json"),true);
 		$books = json_decode(file_get_contents("books_and_paths.json"),true);
 		$dataBase = array();
 		$dataBase['people'] = $people;
 		$dataBase['books']= $books;
 		file_put_contents("bloodaxe_db.json", json_encode($dataBase));
 	}

 	function addPDFFilePaths(){
 		//first make a list of all the pdfs we have
 		$books = json_decode(file_get_contents("books.json"),true);

 		$dir = '/Users/cmdadmin/Documents/PDF';///Volumes/SpecCollStorage/BloodAxe/BloodAxeArchive/OCR PDF/';
 		//echo $dir;
 		//$dir = '/Volumes/SpecCollStorage/BloodAxe/BloodAxeArchive/OCR PDF/';

		$files1 = scandir($dir);

		$dir2 = '/Users/cmdadmin/Documents/PDF/jpegs/';
		$files2 = scandir($dir2);

		$newBooks = array();
		
		foreach ($books as $book) {
			
			$hasImages = false;
			$newIndividualFiles = array();

			//for each item related to each book
			foreach($book['individualFiles'] as $file){
				//print_r($files1);
				$fpaths = array();
				$jpgPaths = array();

				//files1 is the list of pdfs
				foreach ($files2 as $fname) {
				//	print_r($file);
					//this is a stupid way of buildiing a unique array
					//if this is included in our
					if(getItemRefFromFileName($fname)==$file['id'] && !in_array($fname, $fpaths)){
						// echo "file exists for ",$file['title']," ",$fname," ",$file['id']," \n";
						// echo $fname, " ";
						
						$fullPath = "/Users/cmdadmin/Documents/PDF/jpegs/";
						$fullPath.=$fname;
						//array_push($fpaths, $fname);
						array_push($jpgPaths, $fullPath );
					}
				}
				//now look at our resulting list of paths
				// if(sizeof($fpaths)>0){
				// 	// echo $file['title']," ",$book['title']," ",$book['author'],"\n";
				// 	// print_r($fpaths);
					
				// 	//now look for jpeg versions
				// 	foreach ($fpaths as $pdfName) {
				// 		foreach ($files2 as $jpgName) {

				// 			$trimmedJPGName = substr($jpgName, 0,15);//strlen($jpgName)-6); 
				// 			$trimmedPDFName = substr($pdfName, 0,15);// strlen($pdfName)-4); 
				// 		//	echo $trimmedJPGName,"\n";
				// 			if($trimmedJPGName==$trimmedPDFName){
				// 				//echo "Match\n";

				// 				// $fullPath = "/sandpit/jpegs/";
				// 				$fullPath = "/Users/cmdadmin/Documents/PDF/jpegs/";
				// 				// $fullPath.=$dir2;
				// 				$fullPath.=$jpgName;
				// 				//echo $fullPath,"\n";
				// 				array_push($jpgPaths, $fullPath );
								
				// 			}

				// 		}
				// 		# code...
				// 	}
				if(sizeof($jpgPaths)>0){

					$file['imagePaths'] = array_unique($jpgPaths);//$fpaths;
					$hasImages = true;
					
					array_push($newIndividualFiles, $file);
				}
				else{
					$file['imagePaths'] = [];
					$hasImages = false;
					array_push($newIndividualFiles, $file);
				}
			}

			$book['individualFiles'] = $newIndividualFiles;
			$book['hasImages'] = $hasImages;
			// array_push($newBooks , $book);
			if(strlen($book['id']) >3){
				$newBooks[$book['id']] = $book;
			}
			

		}
		//print_r($newBooks);
		foreach ($newBooks as $book) {
			if($book['hasImages']==1){
			//	print_r($book);
			}
		}
		file_put_contents("books_and_paths.json", json_encode($newBooks));
 	}

 	function getBookRecord($title){
 		$bloodaxe_db = json_decode(file_get_contents("bloodaxe_db.json"),true);
 	  
 	  $people = $bloodaxe_db['people'];
 	  $books = $bloodaxe_db['books'];

 	  foreach ($books as $book) {
 	  	# code...
 	  	if($book['title']==$title){
 	  		$result = $book;
 	  	}
 	  }
 	  return $result;
 	}

 	//main function to remake the db
	function createAll(){
		echo "making people table\n";
		makePeopleTable();
		echo "making books table\n";
		makeBooksTable();
		echo "adding file paths\n";
		addPDFFilePaths();
		echo "making database\n";
		makeDataBase();
	}


 	  //addPDFFilePaths();
 	  createAll();

 	//addPDFFilePaths($bloodaxe_db['books']);


?>