<?php

// increase timeout for large data transfer
ini_set('max_execution_time', 300); //300 seconds = 5 minutes


// ========== CONNECT TO DATABASE ========== //


$connect = mysqli_connect("localhost", "root", "");
// check connection
if (!$connect)
{
	die('Could not connect: ' . mysql_error());
	exit();
}

// select database
mysqli_select_db($connect, 'poeticarchive');

// save XML document as a variable reference
$xml = simplexml_load_file("edit.xml");


// ========== COLLECT DATA FROM XML ========== //


// initialise variables
$aID="";
$name="";
$bio="";
$book="";
$personArray=array();

// set path for 'Editorial'
$apath = $xml->xpath('//c01') ;
// set path for 'Published Books by Poet'
$c02 = $apath[0]->xpath('//c02');
//echo sizeof($c02),"\n";
$c01 =  $xml->xpath('//ead/archdesc/dsc/c01'); ///

$c01[0]->xpath('/c02');
//echo "tags :",sizeof($c02);

$authors = $xml->xpath('//ead/archdesc/dsc/c01/c02[1]/c03');
//echo sizeof($authors);
foreach ($authors as $author) {
	//echo sizeof($author),"\n";//->xpath('/did/unitid//@identifier');
	//echo $author->xpath('.//unittitle')[0];

	//echo $author->xpath('.//unitid//@identifier')[0];
	foreach ($author->children() as $child){
	//echo $child->getName(),"<br>";

		$bioTogether  ="";
  		if($child->getName() =='bioghist'){
  			
  			//print_r($child);

  			foreach ($child as $bit) {
  				$bioTogether.=$bit;
  			}
  			echo $bioTogether,"<br>";
  		}
  	}
	//echo  $author['bioghist'];//->xpath('.//bioghist'),"<br>";
	//$consolidatedBio = implode("\n", $biogs);
	//echo $author[2];// $consolidatedBio, "\n";


	$name = $author->xpath('.//unittitle')[0];
	$unitID = $author->xpath('.//unitid//@identifier')[0];

	//	echo sizeof($author->xpath('//did/unitid//@identifier')),"\n";//->xpath('/did/unitid//@identifier');
	$person = array("AuthorID"=>$aID,
				"Name"=>$name,
				"Biography"=>$bio,
				"Books"=>$book);
		array_push($personArray, $person);
	# code...
}
/*foreach ($c03 as $c04) {
		// id value
		//$aID = $c03->xpath('//ead/archdesc/dsc/c01/c02[1]/c03/did/unitid//@identifier');
		// full name
		//$name = $c03->xpath('//ead/archdesc/dsc/c01/c02[1]/c03/did/unittitle[1]');
		//echo $c04,"\n";

		//echo sizeof($name),"\n";
		// biography value
		//$bio = $c03->xpath('//ead/archdesc/dsc/c01/c02[1]/c03/bioghist//p');
		//echo sizeof($bio),"\n";
		// books by author value
		//$book = $c03->xpath("//ead/archdesc/dsc/c01//c02[1]/c03/c04/did//unittitle");
		// store the values into an array
		$person = array("AuthorID"=>$aID,
				"Name"=>$name,
				"Biography"=>$bio,
				"Books"=>$book);
		array_push($personArray, $person);
}*/

//print_r($personArray);
for ($i=0; $i <sizeof($personArray) ; $i++) { 
	# code...
//	echo $personArray[$i]['Name'];
}
/* foreach ($person as $key=>$values) {
	foreach ($values as $value) {
		echo strtoupper($key) . "<br>" . $value . "<br>";
	}
} /*

// ========== EXPORT DATA INTO DATABASE ========== //

// inserts recorded values into database
/* while(list( , $node)= each($aID)) {
	mysqli_query($connect, "INSERT INTO person(AuthorID) VALUES ('$node')");
} */

/* perhaps instead use e.g. UPDATE person SET AuthorID='$node' */

/* while(list( , $node)= each($name)) {
	mysqli_query($connect, "INSERT INTO person(Name) VALUES ('$node')");
}
while(list( , $node)= each($bio)) {
	mysqli_query($connect, "INSERT INTO person(Biography) VALUES ('$node')");
}
while(list( , $node)= each($book)) {
	mysqli_query($connect, "INSERT INTO person(Books) VALUES ('$node')");
} */
/* perhaps instead use e.g. iterate table via id, UPDATE person SET Books='$node' WHERE AuthorID=(current id)
	i.e. for EACH of the 250 author ids, set 'books' to all the unittitles under that id */

// close connection
mysqli_close($connect);

?>