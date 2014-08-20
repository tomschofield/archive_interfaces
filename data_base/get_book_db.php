<?php
	$filename = 'BloodAxe_22.5.14.txt';
	
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
	
	function replaceASCII20($inString, $replaceString){
		$outString;
		for ( $j=0; $j<strlen($inString);$j++){
			if(ord($inString[$j])==29){
				$outString.=$replaceString;
			}
			else{
				$outString.=$inString[$j];
			}
		}

		return $outString;
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
		//echo gettype($exploded[43]);
		$abook->setFname($exploded[43]);
		//echo $exploded[19];
		$stripped = replaceASCII20($exploded[19], ">");
		$creators = explode(">", $stripped);
		//echo "creators ",sizeof($creators);		

		$abook->creators =$creators;//  setCreators($creators);
		//echo "size opppppppf creators ",sizeof($abook->creators);
		if(strlen($exploded[20])>1) array_push($books, $abook);
	}

	file_put_contents("portfolio.json", json_encode($books)) ;
	
?>