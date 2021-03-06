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
		
		<style>
		
		</style>

		<script type="text/javascript"> 
			// Get the X or Y pixel position
			function getY( oElement ){
				var iReturnValue = 0;
				while( oElement != null ) {
					iReturnValue += oElement.offsetTop;
					oElement = oElement.offsetParent;
				}
				return iReturnValue;
			}
			function getX( oElement ){
				var iReturnValue = 0;
				while( oElement != null ) {
					iReturnValue += oElement.offsetLeft;
					oElement = oElement.offsetParent;
				}
				return iReturnValue;
			}
		</script>
		<script>
			window.onload = function () {
				// do stuff here
				setFolder();
			}
		</script>
		<script>
			var currentProject = "";
			function setFolder(){
				project = $('#folder-select').val();
				//alert(project);
				if(project == ""){
					alert("You must select a project to view.");
				} else {
					$.ajax({
						url : '<?php echo $base_url?>index.php/quality_control/getRawImagesAsArray',
						type : 'post',
						data : {
							project: project,
						},
						success : function(answer){
							if(answer == "LOGGED OUT"){window.location.href=window.location.href;}
							if(answer == "LOW PERMISSION"){alert("You don't have permission to do this");}
							images = eval(answer);
							setupRawImageSelect(images);
							currentProject = project;
							gotoNextImage();
							setImage();
							
						}
					});
					memorizeRawProject_ajax(project);
				}
			}
			
			function setImage(){
				image = $('#image-select').val();
				if(image == ""){
					str = '<p>This is the raw image tool.</p>'
				} else {
					str = '<img id="curimg" src="http://franklin-umh.cs.umn.edu/UMassProject/raw_images/'+currentProject+'/quality_control/'+image+'" />';
				}
				
				$('#picture').html(""+str);
				currentImage = image;
				showControlsForAlignment();
			}
			
			function setupRawImageSelect(images){
				htmlstr = '<select id="image-select"><option value="">Please Select a Raw Image</option>';
				for(i in images){
					htmlstr = htmlstr + '<option value="'+images[i]+'">'+images[i]+'</option>';
				}
				htmlstr = htmlstr + '</select><button type="button" onClick="setImage()">View</button>';
				$('#image-controls').html(""+htmlstr);
				$('#picture').html("");
				hideControlsForAlignment();
			}
			
			function hideControlsForAlignment(){
				$('#controls').hide();
			}
			function showControlsForAlignment(){
				$('#controls').show();
			}
			
			function gotoNextImage(){
				$('#image-select > option:selected').removeAttr('selected').next('option').attr('selected', 'selected');
				setImage();
			}
			function gotoPreviousImage(){
				if($('#image-select > option:selected').prev('option').length){
					$('#image-select > option:selected').removeAttr('selected').prev('option').attr('selected', 'selected');
				} else {
					$('#image-select > option:selected').removeAttr('selected');
					$("#image-select > option:last").attr('selected', 'selected');
				}
				
				setImage();
			}

		</script>
	</head>
	<body>
		<div id="head" class="medium-padding" style="height: 20px;">
			<div id="session_controls" class="alignright">
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
					<select id="project_id" name="project_id" class="alignright"></select>
				</span>
				<span id="user_controls" >
					<select id="user_id" name="user_id" class="alignright"></select>
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
		<div id="image_drop_div">
		<div id="folder-controls" class='large-padding'>
			<select id="folder-select">
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
			
			<button type="button" onClick="setFolder()">View</button>

			</div>

		</div>
		<div id="image-controls" class="large-padding"></div>
		<div id='controls' class='large-padding' style="display: none;">
			<button type="button" onclick='gotoPreviousImage();'>Previous Image</button>
			<button type="button" onclick='gotoNextImage();'>Next Image</button>
		</div>
		
		<div id='picture' class='large-padding'>
			<!--img id='curimg' src='http://franklin-umh.cs.umn.edu/UMassProject/images/1075_B02_1-4_5mM_3AT_Xgal_7d_W.cropped.resized.grey.png' /-->
		</div>
		
		
	</body>                                                                
</html>
