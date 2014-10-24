<?php 
$base_url = base_url();
?>

<html>
	<head>    
		<title>MyBrid</title>
		<!-- CSS FILES -->
		<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $base_url?>css/jquery.ui.all.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $base_url?>css/slideshow.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $base_url?>css/results.css" rel="stylesheet" type="text/css">

		<!-- JAVASCRIPT FILES -->
		<script>
			var base_url = "<?php echo $base_url?>";
			var userMem = "<?php echo $this->session->userdata('user_mem')?>";
			var projectMem = "<?php echo $this->session->userdata('project_mem')?>";
		</script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery-1.6.2.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.core.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.widget.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.position.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ui.autocomplete.js"></script>
		<!--script type="text/javascript" src="<?php echo $base_url?>javascript/array_unique.js"></script-->
		<script type="text/javascript" src="<?php echo $base_url?>javascript/project_control.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/wz_jsgraphics.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/overlay_highlights.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.ad-gallery.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/dimensions.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/memorizeRawProject_ajax.js"></script>
		<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery.form.js"></script> 

		<script>
			$(document).ready(function(){ 
				setupAjaxForm('#rawupload');
			})

			function setupAjaxForm(identifier){
				$(identifier).ajaxForm({
					beforeSubmit: function() {},
					success: showResponse
				});
			}

			function showResponse(answer){
				if(answer == "ERROR"){
					alert("There was an error with the file upload.");
				} else if (answer == "ERROR_FILE_MOVE"){
					alert("There was an error with the file upload. The file was unable to move.");
				} else {
					$("#response").html($("#response").html()+"File has been successfully uploaded<br>");
				}
			}		
		</script>

<script type="text/javascript">
			//Made by Alos
	function delete_project(){
		var rawProject = $("#folder-select").val();
		var x;
		var r=confirm("Are you sure you would like to delete that Project?");
		if (r==true){
			$.ajax({
				url : '<?php echo $base_url?>index.php/delete_project/DeleteRawProject',
				type : 'post',
				data : {
					rawProject: rawProject
				},
				success : function(answer){
					if(answer == "LOGGED OUT"){window.location.href=window.location.href;}
					if(answer == "LOW PERMISSION"){alert("You don't have permission to do this");}
					$("#response").html(answer);
					alert("Project Deleted.");
				}
			});	
		}
		else
		{
		  alert("The Project has not been deleted.");
		}
		
	}
</script>

		
	<body>
		<div id="head" class="medium-padding" style="height: 20px;">
			<div id="session_controls">
				<span id="user_session_controls">
					<FORM METHOD="LINK" ACTION="<?php echo $base_url?>index.php/login/logout/" class="alignright">
						<INPUT TYPE="submit" VALUE="Logout">
					</FORM>
					<FORM METHOD="LINK" ACTION="<?php echo $base_url?>" class="alignright">
						<INPUT TYPE="submit" VALUE="Back to Homepage">
					</FORM>
				</span>
			</div>
			<span id="user_project_controls">
				<span id="project_controls" >
					<select id="project_id" name="project_id"></select>
				</span>
				<span id="user_controls" >
					<select id="user_id" name="user_id"></select>
				</span>
			</span>
		</div>	
		<div id="rawUploadNavigation">
			<button type="button" onclick="window.location.href='../raw_upload/'">STEP 1: Create Project and Raw Upload</button>
			<button type="button" onclick="window.location.href='../align_plate/'">STEP 2: Align Plate</button>
			<button type="button" onclick="window.location.href='../utilities/'">STEP 3: Run Utilities</button>
			<button type="button" onclick="window.location.href='../quality_control/'">STEP 4: Quality Control</button>
			<button type="button" onclick="window.location.href='../final_upload/'">STEP 5: Send to Production</button>
			<button type="button" onclick="window.location.href='../delete_project/'">STEP 6: Delete Project</button>
		</div>
		<div id="explaination">
			<p><b>Delete Project Page</b></p>

		</div>
		<div id="folder-controls" class='large-padding'>
			
			<form id="rawupload" action="<?php echo $base_url;?>promoterUploadHandler.php" method="post" enctype="multipart/form-data">
				RAW PROJECT: <select id="folder-select" name="folder-select">
					<option value="">Please Select a Raw Image Project</option>
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
	
							<button type="button" onclick='delete_project();'>Delete Project</button>
			</form>


			<!--button type="button" onClick="setFolder()">View</button-->
		</div>
	</body>                                                                
</html>