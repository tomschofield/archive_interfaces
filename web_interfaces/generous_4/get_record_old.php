<?php
	$filename = 'BloodAxeDB_creators.txt';
	$_POST["sort"];
	class book
	{
		public $title;
		public $date;
		public $fname;
		public $creators = array();

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
		
	    function get_title()
	    {
	        return $this->title;
	    }
	    function get_date()
	    {
	        return $this->date;
	    }
	    function get_fname()
	    {
	        return $this->fname;
	    }
	    function get_creators()
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

		$abook->setCreators($creators);
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
	return strcmp($a->title, $b->title);
	}

	if($_POST["sort"]=="bydate"){
		usort($books, "compare_date");
	}
	elseif($_POST["sort"]=="bytitle"){
		usort($books, "compare_title");
	}
	

	if($_POST["id"] < sizeof($books) ){
		//echo $array [$_POST["id"]];
		//retrive the entry



		$an_entry = $books [$_POST["id"]];
		//43 = filename
		//$exploded = explode("\t", $an_entry);

		$creators = explode(">", $an_entry->get_creators());
		$author ="no data";
		foreach ($creators as $word) {
			$name = explode(" ", $word);
			if($name[0]!="Cover" && $name[0]!="Translated" && strlen($name[0])>1) $author = $word;
		}
		echo json_encode(array("title"=>$an_entry->get_title(),"date"=>$an_entry->get_date(),"fname"=>$an_entry->get_fname(),"id"=>$_POST["id"],"creators"=>$author));
	}
?>