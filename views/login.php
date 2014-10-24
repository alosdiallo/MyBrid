<?php 
	$this->load->helper("url"); 
	$base_url = base_url();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>MyBrid - Login</title>
		<!--link rel="stylesheet" type="text/css" href="<?php echo $base_url?>loginForm/view.css" media="all"-->
		<script type="text/javascript" src="<?php echo $base_url?>loginForm/view.js"></script>
		<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
		<style>
			label {display : block;} .errors {color : red;} 
			
			.absolute {
				position: absolute;
			}
			
			.left {
				width: 300px;
			}
			
			.border {
				border-style:solid;
				border-width:5px;
			}
		</style>
	</head>
	
	<body id="main_body" >
		<div class='absolute left' style="width: 700px; left: 10px;">
			<p style="margin-left: 5px; margin-right: 5px;"><b><span style="font-size:24pt; color:#2790B0">Welcome to MyBrid</span><br><span style="font-size:18pt;"><dd>Visualizing and managing enhanced yeast hybrid screens.</span></b><br><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A major challenge in systems biology is to understand the gene regulatory networks that drive development, physiology and pathology. The first level of gene control involves sequence-specific transcription factors (TFs) that function through various types of physical interactions. These include interactions with regulatory regions of the genome (like promoters and enhancers), and also interactions with regulatory proteins (like co-factors and other TFs).
			<br><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;We detect TF-DNA interactions using yeast one-hybrid (Y1H) assays and TF-protein interactions using yeast two-hybrid (Y2H) assays. To delineate genome-scale regulatory networks, large sets of interactions need to be interrogated at high throughput and high coverage. To achieve this, we developed enhanced Y1H and Y2H assays that use a robotic mating platform with a set of improved reagents and automated readout quantification software called SpotOn. The pipeline for enhanced Y1H assays is presented in the figure at the right.
			<br><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MyBrid was developed for several tasks linked to these enhanced yeast hybrid assays: 1) to upload and view assay images that have been processed by SpotOn; 2) to correct (rare) miscalls by SpotOn; and 3) to download interaction datasets. Mybrid is also an access point for all of the <a href="<?php echo $base_url?>index.php/publication/">Walhout lab papers</a>.
			<br><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mybrid is designed to work using Firefox, Chrome and Safari web browsers. In order to use MyBrid you need to log in. If you do not have a user ID and password please register to set up an account.
			<br><br></p>
			
			<div class='border' border='1px' >
				<span style="margin-left: 15px; font-size:18pt;"><b>Login</b></span><br>	
				<ul>
					<?php echo form_open('login'); ?>
					
						<?php
						echo form_label('Username', '', 'username');
						echo form_input('username', set_value('username'), 'id="username"');
						?>
						<?php
						echo form_label('Password', '', 'password');
						echo form_password('password', '', 'id="password"');
						
						?>
						<?php echo form_submit('submit', 'Login'); ?>
						<?php form_close(); ?>
				</ul>
				<div class="errors">
					<?php echo validation_errors();?>
				</div>
			</div>
			<button type="button" id="Register New User" onClick="window.location.href='<?php echo $base_url?>index.php/register/'">Register New User</button>
		</div>
		
		<div class='absolute' style="margin-left: 750px; top: 10px;">
			<img src="http://franklin-umh.cs.umn.edu/UMassProject/images/nmeth.1748-F1.jpg" width=500/> 
		</div>		
		<!--img id="top" src="<?php echo $base_url?>loginForm/top.png" alt=""-->
		<div id="form_container" align='center'>	
			
		</div>
		<!--img id="bottom" src="<?php echo $base_url?>loginForm/bottom.png" alt=""-->
	</body>
</html>
