<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel='stylesheet' type='text/css' href='stylesheet.css'/>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>Box Log Connections</title>
<style type="text/css">
@import "jquery.svg.css";

#svgbasics { width: 1400px; height: 800px; border: 1px solid #484; }
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="jquery.svg.js"></script>
<script type="text/javascript" src = "script.js"></script>
</head>
<body>

<h1>Bloodaxe Archive</h1>
<h2>Box Contents</h2>
<div id="svgbasics"></div>
<div id="invisible"></div>
<div id="intro">
	<p>
		The archive came to Newcastle University in 63 boxes. With this visualisation tool you can trace particular authors and titles across the archive to support your research. <br><br>The most popular box<?php include 'get_most_popular_boxes.php'; ?>
	</p>

</div>


</body>
</html>
