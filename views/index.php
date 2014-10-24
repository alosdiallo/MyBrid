<?php 
$this->load->helper("url"); 
$base_url = base_url();
if($this->session->userdata('is_logged_in') == false){
	header("Location: ".$base_url."index.php/login/");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>MyBrid</title>
<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
<script>
	base_url = "<?php echo $base_url?>";
</script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery-1.6.2.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/locateFavoriteGene.js"></script>

<style type="text/css">
body {
 background-color: #fff;
 margin: 40px;
 font-size: 14px;
 color: #4F5155;
}

a {
 color: #003399;
 background-color: transparent;
 font-weight: normal;
}

h1 {
 color: #444;
 background-color: transparent;
 border-bottom: 1px solid #D0D0D0;
 font-size: 16px;
 font-weight: bold;
 margin: 24px 0 2px 0;
 padding: 5px 0 6px 0;
}

code {
 font-size: 12px;
 background-color: #f9f9f9;
 border: 1px solid #D0D0D0;
 color: #002166;
 display: block;
 margin: 14px 0 14px 0;
 padding: 12px 10px 12px 10px;
}
.alignleft {
float: left;
}
.alignright {
float: right;
}

</style>

<script>
	
</script>
</head>
<body>

<div >
	<h1>
		<p class='alignleft'>Welcome to MyBrid.</p><br><br>
		<p> Please click on Browse Interactions to view particular experiments belonging to your genes of interest (you can also view an entire dataset), if you don't know what project</p>
		<p> contains your gene of interest you could enter it into the "FAVORITE GENE SEARCH" below on this page.</p>

			<div class="user_session_controls">
			<p class='alignright'>
				<?php
					if($this->session->userdata('is_logged_in') == false){
						echo '<FORM METHOD="LINK" ACTION="'.$base_url.'index.php/login/" class="alignright"><INPUT TYPE="submit" VALUE="Login"></FORM>';
					}
					else{
						echo '<FORM METHOD="LINK" ACTION="'.$base_url.'index.php/login/logout/" class="alignright"><INPUT TYPE="submit" VALUE="Logout"></FORM>';
					}
				?>
			</p>
		</div>
		<div style="clear:both;"></div>
	</h1>

</div>
<p>
	<ul><p><h1> <a href="<?php echo $base_url?>index.php/data/"> Browse Interactions </a></h1></p></ul>
	<ul><p><h1> <a href="<?php echo $base_url?>index.php/downloadPositiveData/"> Download Datasets </a></h1></p></ul>
	<ul><p><h1> <a href="<?php echo $base_url?>index.php/network_view/"> View Interactions as a Network </a></h1></p></ul>
	<ul><p><h1> <a href="<?php echo $base_url?>index.php/publication/"> Access Lab Publications + Published Datasets </a></h1></p></ul>
	<ul><p><h1> <a href="<?php echo $base_url?>index.php/send_mail/"> Contact Us </a></h1></p></ul>
	<?php
		//Permission level 2 required to view plate_view
		if($this->session->userdata('admin') <= 2){
			echo '<ul><p><h1> <a href="'.$base_url.'index.php/plate_view/"> Check Screen Quality using Treeview </a></h1></p></ul>';
		}
	?>
	<?php
		//Permission level 2 required to view plate_view
		if($this->session->userdata('admin') <= 2){
			echo '<ul><p><h1> <a href="'.$base_url.'index.php/upload/"> Upload Resources </a></h1></p></ul>';
		}
	?>
	<?php
		//Permission level 1 required to view plate_view
		if($this->session->userdata('admin') == 1){
			echo '<ul><p><h1> <a href="'.$base_url.'index.php/raw_upload/"> Raw Upload </a></h1></p></ul>';
		}
	?>
	<?php
		//Permission level 1 required to view plate_view
		if($this->session->userdata('admin') == 1){
		//	echo '<ul><p><h1> <a href="'.$base_url.'index.php/align_plate/"> Plate Align </a></h1></p></ul>';
		}
	?>
	<?php
		//Permission level 1 required to view plate_view
		if($this->session->userdata('admin') == 1){
		//	echo '<ul><p><h1> <a href="'.$base_url.'index.php/quality_control/"> Quality Control </a></h1></p></ul>';
		}
	?>
	<?php
		//Permission level 1 required to view plate_view
		if($this->session->userdata('admin') == 1){
		//	echo '<ul><p><h1> <a href="'.$base_url.'index.php/utilities/"> Utilities </a></h1></p></ul>';
		}
	?>
	<?php
		//Permission level 1 required to view plate_view
		if($this->session->userdata('admin') == 1){
			echo '<ul><p><h1> <a href="'.$base_url.'index.php/admin/"> Admin Panel </a></h1></p></ul>';
		}
	?>
	<ul><p><h1> <a href="<?php echo $base_url?>man/"> Instructions </a></h1></p></ul>
	<ul><p><hl>FAVORITE GENE SEARCH
	<p>Please enter YOUR FAVORITE GENE to get a table explaining whether YOUR FAVORITE GENE is a BAIT or a PREY and which projects it belongs to.</p>
	<p><input type="text" id="favoriteGene" /></input><button type="button" onClick="locateFavoriteGene()">Locate!</button></p>
	<p><div id='favoriteGeneTable'></div></p>
	</h1></p></ul>
	
</p>

</body>
</html>
