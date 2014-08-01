<?php

// ========== CONNECT TO DATABASE ========== //


// create variable to store connection details
$connect = mysqli_connect("localhost", "root", "");
// check connection; quit if fail with error
if (!$connect)
{
	die('Could not connect: ' . mysql_error());
	exit();
}

// select database to work with using connection variable
mysqli_select_db($connect, 'poeticarchive');


// ========== COLLECT DATA FROM XML ========== //


// use xml file as a stored variable
$xml = simplexml_load_file("edit.xml");

// series 'Editorial'
$apath = $xml->xpath('//c01');
// series 'Published Poetry by Author'
$c02 = $apath[0]->xpath('//c02')[0];

//variables to store data for Person table
$aID = "";
$name = "";
$bio = "";
$books = "";
$personArray = array();

foreach ($c02 as $c03) {
	// finds 1st children of c02 node (under c01/c02[0], poetry by author) stored as variable $first_gen
	foreach ($c03->children() as $first_gen) {
		// unique identifier is found under xml tag <unitid> under the atttribute "identifier"
		$aID == $first_gen->xpath('//ead/archdesc/dsc/c01//c02[1]/c03/did/unitid//@identifier');
		$name == $first_gen->xpath('//ead/archdesc/dsc/c01/c02[1]/c03/did/unittitle[1]');
		$bio == $first_gen->xpath('//ead/archdesc/dsc/c01/c02[1]/c03/bioghist//p');
		$books == $first_gen->xpath('//ead/archdesc/dsc/c01//c02[1]/c03/c04/did//unittitle');

		// for this iteration map all the values recorded into a temporary array variable
		$aperson = array("AuthorID"=>$aID,
						 "Name"=>$name,
						 "Biography"=>$bio,
						 "Books"=>$books, );

		// pass the data from this iteration into the array variable 'personArray'
		array_push($personArray, $aperson) ;
	}
}

print_r($personArray);

// ========== IMPORT DATA INTO DATABASE ========== //

$personTable = implode("','",$personArray[1]);

mysql_query("INSERT INTO Person (AuthorID, Name, Biography, Books) VALUES ('$personTable')");


// need to pass data stored in 'personArray' into the database 'poeticarchive' into table 'Person'
/* $sql = "INSERT INTO Person (AuthorID, FirstName, LastName, Age, Biography, DateOfBirth) VALUES";

// create a new iterator to iterate personArray
$itr = new ArrayIterator($personArray);

// create new CachingIterator to provide access to hasNext() to tell iterator when to terminate
$citr = new CachingIterator($itr);

// loop over the array
foreach ($citr as $val) {
	// add to the query
	$sql .= "('".$citr->key()."','" .$citr->current()."')";
	// if there's another entry, add a comma
	if( $citr->hasNext() )
    {
        $sql .= ",";
    }
} */

/* mysqli_query($connect, $sql);

foreach ($personArray as $val) {
	$sql = ($connect, "INSERT INTO Person (AuthorID, FirstName, LastName, Age, Biography, DateOfBirth)
						VALUES ('$aID', '$firstName', '$lastname', '$age', '$bio', '$dob')");
	if (!mysqli_query($connect, $sql)) {
  		die('Error: ' . mysqli_error($con));
	}
} */