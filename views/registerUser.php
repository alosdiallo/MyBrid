<?php 
$base_url = base_url();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Spot-On - Admin Panel</title>

<!--link rel="stylesheet" type="text/css" href="<?=$base_url?>registerForm/view.css" media="all"-->
<!--script type="text/javascript" src="<?=$base_url?>registerForm/view.js"></script-->
</head>

<body id="main_body" >
	
	<!--img id="top" src="<?=$base_url?>registerForm/top.png" alt=""-->
		<div id="registerUser">
			<p> Register a new user </p>
			<form method="post" action="<?=$base_url?>index.php/register/">	
				First name: <input type="text" id="firstname" name="firstname" /><br />
				Last name: <input type="text" id="lastname" name="lastname" /><br />
				E-mail: <input type="text" id="email" name="email" /><br />  
				Username: <input type="text" id="username" name="username" /><br />  
				Password: <input type="password" id="password" name="password" /><br />  
				Password Confirmation: <input type="password" id="passconf" name="passconf" /><br />  
				<input type="submit" value="Register" />
			</form>	
			<div class="errors">
				<?php echo validation_errors();?>
			</div>
		</div>
	</body>
</html>
