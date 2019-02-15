<meta charset="utf-8">
<head>
	<title>Geography</title>
	<!--<META NAME="Content-Type" CONTENT="text/html;Charset=Windows-1251">-->
	<!--<script language="JavaScript" src="audio/audio-player.js"></script>
	<script type="text/javascript" src="scrolling.js"></script>-->
</head>
<body>
	<?php 
	$type = $_GET["type"];
	if ($type != 14313)
		include("infromation.php");
	else
		include("change.php");
	?>
</body>