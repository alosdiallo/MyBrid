<?php 
$base_url = base_url();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Spot-On - Register</title>
<link rel="stylesheet" type="text/css" href="<?php echo $base_url?>registerForm/view.css" media="all">
<script type="text/javascript" src="<?php echo $base_url?>registerForm/view.js"></script>
<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
<style>
label {display : block;} .errors {color : red;} 
</style>
</head>
<body id="main_body" >
	
	<!--img id="top" src="<?php echo $base_url?>registerForm/top.png" alt=""-->
	<div id="form_container">
	
		<h1><a> Register a new user </a></h1>
		<form id="form_183019" class="appnitro"  method="post" action="<?php echo $base_url?>index.php/admin/addNewUser">
					<div class="form_description">
			<h2>Register</h2>
			<p>Simply fill out this fields to register yourself as a user of this website.</p>
		</div>						
			<ul >
		<?php echo form_open('login'); ?>
					<li id="li_1" >
		<?php
			echo "<span>" . form_label('First Name', '', 'firstname');
			echo form_input('firstname', set_value('firstname'), 'id="firstname"') . "</span>";
			
		?>
		<?php
			echo "<span>" . form_label('Last Name', '', 'lastname');
			echo form_input('lastname', set_value('lastname'), 'id="lastname"') . "</span>";
		?>
		</li>		<li id="li_2" >
		<?php
			echo form_label('Email', '', 'email');
			echo form_input('email', set_value('email'), 'id="email"');
		
		?>
		</li>		<li id="li_3" >
		<?php
			echo form_label('Username', '', 'username');
			echo form_input('username', set_value('username'), 'id="username"');
		
		?>
		</li>		<li id="li_4" >
		<?php
			echo form_label('Password', '', 'password');
			echo form_password('password','', 'id="password"');
		
		?>
		</li>		<li id="li_5" >
		<?php
			echo form_label('Password Confirmation', '', 'passconf');
			echo form_password('passconf', '', 'id="passconf"');
		
		?>
		</li>
			
					<li class="buttons">
		<?php echo form_submit('submit', 'Register'); ?>
		<?php form_close(); ?>
		</li>
			</ul>
		</form>	
		<div class="errors">
		<?php echo validation_errors();?>
		</div>
		<div id="footer">
		</div>
	</div>
	<!--img id="bottom" src="<?php echo $base_url?>registerForm/bottom.png" alt=""-->
	</body>
</html>
