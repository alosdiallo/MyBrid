<!--?php require_once("/project/csbio/web/UMassProject/dev/ci/phpfileuploader/select-multiple-files-upload.php") ?-->
<?php 
$base_url = base_url();
?>
<html>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript" src="<?=$base_url?>javascript/project_control.js"></script>

<style type="text/css">
	.alignleft {
		float: left;
	}
	.alignright {
		float: right;
	}
</style>
<script>

//////
// Define base url for project control
//////
var base_url = "<?=$base_url?>"; 

</script>
<head>
	<title>PHP Upload - Selecting multiple files for upload</title>
	<link href="demo.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div class="user_session_controls">
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
		<FORM METHOD="LINK" ACTION="<?=$base_url?>index.php/register/" class='alignright'>
			<INPUT TYPE="submit" VALUE="Register">
		</FORM>
		<FORM METHOD="LINK" ACTION="<?=$base_url?>" class='alignright'>
			<INPUT TYPE="submit" VALUE="Back to Homepage">
		</FORM>
		<!--Project Control Divs-->
		<span id="user_project_controls">
			<span id="user_controls" >
				<select id="user_id" name="user_id" class="alignright"></select>
			</span>
			<span id="project_controls" >
				<select id="project_id" name="project_id" class="alignright"></select>
			</span>
		</span>
		<!--End Project Control Divs-->
	</div>
	<div class="demo">
        <h2>Selecting multiple files for upload</h2>
        <p> Select multiple files in the file browser dialog then upload them at once (Allowed file types: <span style="color:red">png</span>).
		</ul>
	</div>
	<!--form name="upload" action="<?=$base_url?>index.php/upload/do_upload" method="POST" ENCTYPE="multipart/form-data">
		Filename: <input type="file" name="userfile">
		<input type="submit" name="upload" value="Upload">
	</form-->
	
	<!--form action="<?=$base_url?>index.php/upload/do_upload" method="post" enctype="multipart/form-data">
	  Send these files:<br />
	  <input name="userfile[]" type="file" size=75 /><br />
	  <input name="userfile[]" type="file" size=75 /><br />
	  <input name="userfile[]" type="file" size=75 /><br />
	  <input name="userfile[]" type="file" size=75 /><br />
	  <input name="userfile[]" type="file" size=75 /><br />
	  <input type="submit" value="Send files" />
	</form-->
	
	 <APPLET
                    CODE="wjhk.jupload2.JUploadApplet"
                    NAME="JUpload"
                    ARCHIVE="wjhk.jupload.jar"
                    WIDTH="640"
                    HEIGHT="300"
                    MAYSCRIPT="true"
                    ALT="The java pugin must be installed.">
            <param name="postURL" value="yourServerScriptURL.html" />
            <!-- Optionnal, see code comments -->
            <param name="showLogWindow" value="false" />

            Java 1.5 or higher plugin required. 

        </APPLET>

	
	
	
</body>
</html>
