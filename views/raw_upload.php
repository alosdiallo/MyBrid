<!--?php require_once("/project/csbio/web/UMassProject/dev/ci/phpfileuploader/select-multiple-files-upload.php") ?-->
<?php 
$base_url = base_url();
?>
<html>
	<!--script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script-->
	<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery-1.6.2.js"></script>
	<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.core.js"></script>
	<script type="text/javascript" src="<?php echo $base_url?>javascript/project_control.js"></script>
	<script type="text/javascript" src="<?php echo $base_url?>javascript/memorizeRawProject_ajax.js"></script>
	<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
	

<style type="text/css">
	#head {
		height: 40;
	}
	
#warning {
    color: #FA2A00;
}
</style>
<script>

//////
// Define base url for project control
//////
var base_url = "<?php echo $base_url?>"; 
var userMem = "<?php echo $this->session->userdata('user_mem')?>";
var projectMem = "<?php echo $this->session->userdata('project_mem')?>";

</script>
<!-- Jquery functions -->
<script language="javascript">
	var savedProject = "";
	
	function setFolder(){
		var val = $('#folder-select').attr('value');
		createUploaderAttribute("folder", val);
		savedProject = val;

		$("#message").html("You are currently uploading to "+ val+".");

		// Memorize the raw project
		memorizeRawProject_ajax(val);
	}

	// When the window loads check to see if there is a saved location.
	window.onload = function () {
		// do stuff here
		setFolder();
	}
	
window.onbeforeunload = function(e) {
    return 'You are about to close this page.';
};
	
	
	
</script>
<script>
	function addRawProject(){
		project = $('#newRawProject').val();
		$("#response").html($("#response").html()+"Adding Project..."+project+"<br>");
		if(project != ""){
			$.ajax({
				url : '<?php echo $base_url?>index.php/raw_upload/addRawProject',
				type : 'post',
				data : {
					project: project,
				},
				success : function(answer){
					if(answer == "LOGGED OUT"){window.location.href=window.location.href;}
					if(answer == "LOW PERMISSION"){alert("You don't have permission to do this");}
					if(answer == "success"){
						$('#folder-select').append('<option value="'+project+'">'+project+'</option>');
						$('#raw-message').html("You have created a new project named "+project);
					}
				}
			});
		} else {
			alert("Your project that you want to add needs a name. The name should not have spaces or special characters.");
		}
	}
	
	function runCameraScript(number){
		$("#response").html($("#response").html()+"Running Camera Script"+"<br>");
		if(savedProject != ""){
			
			var project = savedProject;
			if(number == 0){
				$("#response").html($("#response").html()+"Old Camera Script Running..."+"<br>");
				$.ajax({
					url : '<?php echo $base_url?>index.php/raw_upload/runOldCameraScript',
					type : 'post',
					data : {
						project: project,
					},
					success : function(answer){
						if(answer == "LOGGED OUT"){window.location.href=window.location.href;}
						if(answer == "LOW PERMISSION"){alert("You don't have permission to do this");}
						$("#response").html($("#response").html()+"Old Camera Script Complete..."+"<br>");
						alert(answer);
					}
				});
			} else if(number == 1){
				$("#response").html($("#response").html()+"New Camera Script Running..."+"<br>");
				$.ajax({
					url : '<?php echo $base_url?>index.php/raw_upload/runNewCameraScript',
					type : 'post',
					data : {
						project: project,
					},
					success : function(answer){
						if(answer == "LOGGED OUT"){window.location.href=window.location.href;}
						if(answer == "LOW PERMISSION"){alert("You don't have permission to do this");}
						$("#response").html($("#response").html()+"New Camera Script Complete..."+"<br>");
						alert(answer);
					}
				});
			} else {
				alert("Theres been an error on the Run Camera Function, your number is "+number+". Please report this to Alos/Justin.");
			}
		}
	}
	
</script>
		
<head>
	<title>PHP Upload - Selecting multiple files for upload</title>
	<link href="demo.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="head">
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
		<FORM METHOD="LINK" ACTION="<?php echo $base_url?>" class='alignright'>
			<INPUT TYPE="submit" VALUE="Back to Homepage">
		</FORM>
		<!--Project Control Divs-->
		<span id="user_project_controls">
			<span id="project_controls" >
				<select id="project_id" name="project_id" class="alignright"></select>
			</span>
			<span id="user_controls" >
				<select id="user_id" name="user_id" class="alignright"></select>
			</span>
		</span>
		<!--End Project Control Divs-->
	</div>
</div>
		<div id="rawUploadNavigation">
			<button type="button" onclick="window.location.href='../raw_upload/'">STEP 1: Create Project and Raw Upload</button>
			<button type="button" onclick="window.location.href='../align_plate/'">STEP 2: Align Plate</button>
			<button type="button" onclick="window.location.href='../utilities/'">STEP 3: Run Utilities</button>
			<button type="button" onclick="window.location.href='../quality_control/'">STEP 4: Quality Control</button>
			<button type="button" onclick="window.location.href='../final_upload/'">STEP 5: Send to Production</button>
		</div>
	<h2>Raw Project Upload Utility</h2>
	<p><b>Creating a new raw project.</b></p>
	<p>You can create a new raw project folder to upload raw data files into. To do this type in the name of the raw project. The name of the raw project should not have spaces or special characters in it. After you have typed in the name of the project click add</p>
	<p>Please make sure there are no spaces or special characters in your project name.</p> 
	<div id="new-folder">
		<input type="text" id="newRawProject" />
		<button type="button" onClick="addRawProject()">Add</button>
	</div>
	<div id="raw-message"></div>
	<p><b>Uploading Raw Project Images</b></p>
	<div id="warning"><strong><p>Do not close the screen while uploading images, if this is done all images will be lost.</p></strong>
<strong><p>Do not include spaces in your file names, the system will not recognize your image names if this is done.</p></div>
<p>Please include identifier info for each bait in the case of this example it's 1_A08, the plate that it 	is on i.e. 1-4,5-8,9-12, also include info on which camera was used in this example OLD is written. <br>The camera boxes are labeled old and new.
<br>Examples of good image names: 1_A08_1-4_OLD_20mM_3AT_Xgal_7d_W.JPG or 1077_A9_1-4_old_5mM_Xgal_7d.JPG</p>
	            

	<p>Select your raw project from the drop down below and then push the set upload location button. You should get a message underneath the drop down saying that you are now uploading to your projects folder. You can now use the uploader to upload image files into the raw project folder.</p>
	<p>You will also want this selected when you run the scripts for the different cameras</p>
	<div id="folder-controls">
		<select id="folder-select">
			<option value="nowhere">Please Select a Location To Upload</option>
			<?php
				$directory = '/heap/UMassProject/raw_images/';
				$raw_image_folders = array_diff(scandir($directory), array('..', '.'));
				
				foreach($raw_image_folders as $raw_project){
					echo "<option value ='$raw_project'";
					if($raw_project == $this->session->userdata('rawProject')){ echo " selected='selected'";}
					echo ">$raw_project</option>";
				}
			?>
			<!--option value="test">Test</option-->
		</select>
		<button type="button" onClick="setFolder()">Set Upload Location</button>
	</div>
	<div id="message">You are currently uploading to nowhere.</div>
		<p>
			<applet id="jumpLoaderApplet" name="jumpLoaderApplet"
				code="jmaster.jumploader.app.JumpLoaderApplet.class"
				archive="<?php echo $base_url?>jumploader_z.jar"
				width="715"
				height="450"
				mayscript>
					<param name="uc_uploadUrl" value="<?php echo $base_url?>raw_uploadHandler.php"/>
					<param name="ac_fireAppletInitialized" value="true"/>
					<param name="ac_fireUploaderFileAdded" value="true"/>
					<param name="ac_fireUploaderFileRemoved" value="true"/>
						<param name="ac_fireUploaderFileMoved" value="true"/>
					<param name="ac_fireUploaderFileStatusChanged" value="true"/>
					<param name="ac_fireUploaderFilesReset" value="true"/>
					<param name="ac_fireUploaderStatusChanged" value="true"/>
					<param name="ac_fireUploaderFilePartitionUploaded" value="true"/>
					<param name="ac_fireUploaderSelectionChanged" value="true"/>
					<param name="ac_fireUploadViewFileOpenDialogFilesSelected" value="true"/>
					<param name="ac_fireMainViewMessageShown" value="true"/>
					<param name="uc_partitionLength" value="1048576"/> 
			</applet>
		</p>
		<button type="button" onClick="runCameraScript(0)">Run Old Camera Script</button>
		<button type="button" onClick="runCameraScript(1)">Run New Camera Script</button>
		<div id="wheel_camerascript"></div>
		<div id="response"></div>
		
		<!-- callback methods -->
		<!-- debug auxiliary methods -->
		<script type="text/javascript" src="<?php echo $base_url?>javascript/rawUpload_jumploader.js"></script>			
		<form name="debugForm">
			<p>Events:<br>
			<textarea name="txtEvents" style="width:100%; font:10px monospace" rows="10" wrap="off" id="txtEvents"></textarea>
			</p>
		
			<p><input type="button" value="Dump uploader status" onClick="dumpUploaderStatus()">
			&nbsp;&nbsp;
			<input type="button" value="About..." onClick="alert( document.jumpLoaderApplet.getAppletInfo() )">
			  <p id="uploaderStatus"></p>
			 </p>
		</form>
	</body>
</html>
