<?php

// create connection
$connect = mysqli_connect("localhost", "root", "");
// check connection
if (!$connect)
{
	die('Could not connect: ' . mysql_error());
	exit();
}

// select database
mysqli_select_db($connect, 'poeticarchive');

// create tables
$sql1 = "CREATE TABLE Person(AuthorID VARCHAR(15),
							Name VARCHAR(30),
							Biography VARCHAR(500),
							Books VARCHAR(100) )";

// execute query
if (mysqli_query($connect, $sql1)) {
	echo "Table 'Person' created successfully";
} else {
	echo "Error creating table: " .mysqli_error($connect);
}

$sql2 = "CREATE TABLE Book(BookID INT,
							Title VARCHAR(30),
							Anthology BOOLEAN,
							Authors VARCHAR(100),
							RelatedMaterials VARCHAR(200),
							DatePublished VARCHAR(10),
							Editor VARCHAR(10),
							ImageCredits VARCHAR(10) )";

// execute query
if (mysqli_query($connect, $sql2)) {
	echo "Table 'Book' created successfully";
} else {
	echo "Error creating table: " .mysqli_error($connect);
}

$sql3 = "CREATE TABLE Poem(PoemID INT,
							Author VARCHAR(20),
							DatePublished VARCHAR(10),
							Title VARCHAR(30),
							Dedication VARCHAR(20) )";

// execute query
if (mysqli_query($connect, $sql3)) {
	echo "Table 'Poem' created successfully";
} else {
	echo "Error creating table: " .mysqli_error($connect);
}

// close connection
mysqli_close($connect);

?>