<!DOCTYPE HTML> 
<html>
<head>
    <title>Interface Feedback</title>
    <link rel='stylesheet' type='text/css' href='stylesheet.css'/>
    <script src="jquery-1.10.2.min.js"></script>
    <script src="script.js" type="text/javascript"></script>
</head>
<body> 
<body> 
	<div id="container">
		Thank you for taking the time to fill out our survey. You should be automatically redirected to the main project page in just a second. If you aren't, please click <a href="http://bloodaxe.ncl.ac.uk/wordpress/">here</a>.
	</div>
	<?php
	//Content is the pause before invoking the meta
		echo '<META HTTP-EQUIV="Refresh" Content="4; URL=http://bloodaxe.ncl.ac.uk/wordpress/">';   
	?>
</body> 
</html>