<?php 
$base_url = base_url();
?>
<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery-1.6.2.js"></script>
<style type="text/css">
	#head {
		height: 40;
	}
</style>
<script>
	function sendMail(){
		$.ajax({
			url : '<?php echo $base_url?>index.php/send_mail/sendMail',
			type : 'post',
			data : {
				fname: $("#first_name").val(),
				lname: $("#last_name").val(),
				email: $("#email").val(),
				comment: $("#comments").val()
			},
			success : function(answer){
				alert("Success");
			}
		});
	}
</script>
<body>
	<div id="head">
		<div id="session_controls" class="alignright">
			<span id="user_session_controls">
				<?php
					if($this->session->userdata('is_logged_in') == false){
							echo '
							<FORM METHOD="LINK" ACTION="'.$base_url.'index.php/login/" class="alignright">
								<INPUT TYPE="submit" VALUE="Login">
							</FORM>
							';
					  }
					  else{
							echo '
							<FORM METHOD="LINK" ACTION="'.$base_url.'index.php/login/logout/" class="alignright">
								<INPUT TYPE="submit" VALUE="Logout">
							</FORM>
							';
					  }
				?>
				<FORM METHOD="LINK" ACTION="<?php echo $base_url?>" class="alignright">
					<INPUT TYPE="submit" VALUE="Back to Homepage">
				</FORM>
			</span>
			
		</div>
	</div>
	<table width="450px">
		<tr>
			<td valign="top">
				<label for="first_name">First Name</label>
			</td>
			<td valign="top">
				<input type="text" id="first_name" maxlength="50" size="30">
			</td>
		</tr>
		<tr>
			<td valign="top"">
				<label for="last_name">Last Name</label>
			</td>
			<td valign="top">
				<input type="text" id="last_name" maxlength="50" size="30">
			</td>
		</tr>
		<tr>
			<td valign="top">
				<label for="email">Email Address</label>
			</td>
			<td valign="top">
				<input type="text" id="email" maxlength="80" size="30">
			</td>
		</tr>
		<tr>
			<td valign="top">
				<label for="comments">Comments</label>
			</td>
			<td valign="top">
				<textarea id="comments" maxlength="1000" cols="25" rows="6"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				<input type="button" value="Submit" onClick="sendMail()">   <!--a href="http://www.freecontactform.com/email_form.php">Email Form</a-->
			</td>
		</tr>
	</table>
</body>

