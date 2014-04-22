<?php
	//$filename = 'BloodAxeDB_creators.txt';
	$filename = 'Export.3.2.14.txt';
	$_POST["sort"];
	//echo "Running programme";
	//echo "<br>";
	class book
	{
		public $title;
		public $date;
		public $fname;
		public $creators;

		public function setTitle($title) {
        $this->title = $title;
        	return $this;
    	}
    	public function setDate($date) {
        $this->date = $date;
        	return $this;
    	}
    	public function setFname($fname) {
        $this->fname = $fname;
        	return $this;
    	}
    	public function setCreators($creators) {
        $this->creators = $creators;
        	return $this;
    	}
		
	    public function get_title()
	    {
	        return $this->title;
	    }
	    public function get_date()
	    {
	        return $this->date;
	    }
	    public function get_fname()
	    {
	        return $this->fname;
	    }
	    public function get_creators()
	    {
	        return $this->creators;
	    }
	}
	
	
	// Open the file
	$fp = @fopen($filename, 'r'); 
	$array = array();
	// Add each line to an array
	if ($fp) {
   		$array = explode("\r", fread($fp, filesize($filename)));
	}
	
	$counter = 0;

	//this will be our array of book objects
	$books = array();
	
	//ignore the first line = it's the headings
	for ($i=1; $i < sizeof($array); $i++) { 
		$exploded = explode("\t", $array[$i]);
		
		$abook = new book;
		$abook->setTitle($exploded[4]);
		$abook->setDate($exploded[20]);
		$abook->setFname($exploded[43]);

		$creators = explode(">", $exploded[19]);
		

		$abook->creators =$creators;//  setCreators($creators);
		//echo "size opppppppf creators ",sizeof($abook->creators);
		if(strlen($exploded[20])>1) array_push($books, $abook);
	}

	//echo "unsorted ", $books[0]->get_date();		

	//lets sort the array
	function compare_date($a, $b)
	{
	return strcmp($a->date, $b->date);
	}
	function compare_title($a, $b)
	{
		//check for dodgy special characters
		if (ctype_alpha ($a->title[0])&&ctype_alpha ($b->title[0])) {
			return strcasecmp($a->title, $b->title);
		}
		elseif (ctype_alpha ($a->title[0])&&!ctype_alpha ($b->title[0])) {
			return strcasecmp($a->title, substr($b->title, 1, sizeof($b->title)-2));
		}
		elseif (!ctype_alpha ($a->title[0])&&ctype_alpha ($b->title[0])) {
			return strcasecmp(substr($a->title, 1, sizeof($b->title)-2), $b->title);
		}
		elseif (!ctype_alpha ($a->title[0])&&!ctype_alpha ($b->title[0])) {
			return strcasecmp(substr($a->title, 1, sizeof($b->title)-2), substr($b->title, 1, sizeof($b->title)-2));
		}
	
	}

	if($_POST["sort"]=="bydate"){
		usort($books, "compare_date");
	}
	elseif($_POST["sort"]=="bytitle"){
		usort($books, "compare_title");
	}
	elseif($_POST["sort"]=="shuffle"){
		shuffle($books);
	}
	
	//echo "size of books outside function ",sizeof($books);
	function find_most_productive_year($_books){
//		1979
		$most_productive_year_yet = 0;
		$highest_book_count = 0;
		//echo date("Y");
		for ($i=1979; $i <= @date("Y"); $i++) { 
			$book_count=0;
			//echo "checking",$i,"<br>";
			//echo "size of books inside function ",sizeof($_books);
			foreach ($_books as $aBook) {
				//echo "date ",$aBook->get_date();
				if($aBook->get_date()==$i){
					$book_count++;
				}
			}
			if ($book_count>$highest_book_count) {
				$highest_book_count=$book_count;
				$most_productive_year_yet = $i;
			}
			//echo "count for this year is ",$book_count,"<br>";
		}
		
		return array ($most_productive_year_yet, $highest_book_count); 
		//return "true";
	}

	list  ($year, $number_of_books)=find_most_productive_year($books);
	// echo "Most productive year was : ";
 //  	echo $year;
	// echo " with a total of : ";
	// echo $number_of_books;

	if($_POST["id"] < sizeof($books) ){
		//echo $array [$_POST["id"]];
		//retrive the entry



		$an_entry = $books [$_POST["id"]];
		//43 = filename
		//$exploded = explode("\t", $an_entry);

		$creators = $an_entry->creators;
		//echo "creators ",sizeof($an_entry->creators);
		//echo "creators ",$creators[0];
		$author ="";
		foreach ($creators as $word) {
			$name = explode(" ", $word);
			//echo $name[0];
			if($name[0]!="Cover" && $name[0]!="Translated" && strlen($name[0])>1) {
				$author = $word;
			}
		}
		echo json_encode(array("title"=>$an_entry->get_title(),"date"=>$an_entry->get_date(),"fname"=>$an_entry->get_fname(),"id"=>$_POST["id"],"creators"=>$author));
	}
?>