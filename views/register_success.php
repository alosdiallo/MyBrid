<?php 
$base_url = base_url();
?>

<html>
<head>
<META HTTP-EQUIV="refresh" CONTENT="3;URL=<?php echo $base_url?>">
	<title>Spot-On</title>
</head>
<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
<body>

<p>Your profile has been successfully created!</p><br><br>
<p>You are going to be forwarded to the main page in 3 seconds</p><br><br>
<p><?php echo anchor($base_url, 'back to the main page'); ?></p>

</body>
</html>
