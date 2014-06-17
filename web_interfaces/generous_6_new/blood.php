<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<link rel='stylesheet' type='text/css' href='stylesheet.css'/>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title>Box Log Connections</title>
	<style type="text/css">
	@import "jquery.svg.css";
		
	</style>

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="jquery.svg.js"></script>
	<script type="text/javascript" src="jquery.svgdom.js"></script>
	<script type="text/javascript" src = "script.js"></script>
</head>

<body>
	<div id="buffer"></div>
	
	<div id ="container">
		<div id="intro">
			
			<h2>Box Log Connections</h2>
				<p>
				The first part of the archive came to Newcastle University in 62 boxes. A further 142 were added later. With this visualisation tool you can trace particular authors across the archive to support your research. <br><br>The most popular box<?php include 'get_most_popular_boxes.php'; ?>
				</p>

		</div>
		
		<div id="svgbasics"></div>
		<div id="invisible"></div>

		
	</div>
</body>
</html>
