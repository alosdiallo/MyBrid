<?php 
$base_url = base_url();
?>

<html>



<style type="text/css">

	
#explaination {
   padding-left:10px;
}
</style>

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

		<script type="text/javascript">
			function is_numeric(input){
				return !isNaN(parseFloat(input));
			}

			function runMagicPlate(number){
				project = $('#folder-select').val();
				inputPNG = $('#inputPNG').val();
				nClusters = $('#nClusters').val();
				smoothRadius = $('#smoothRadius').val();
				smoothMode = $('#smoothMode').val();
				colonyMinSize = $('#colonyMinSize').val();
				colonyMaxSize = $('#colonyMaxSize').val();
				colonyNeighbors = $('#colonyNeighbors').val();
				debugMode = $('#debugMode').val();
				
				// Set up Defaults
				if( !is_numeric(nClusters) ){
					nClusters = 2;
					$('#nClusters').val("2");
				}
				if( !is_numeric(smoothRadius) ){
					smoothRadius = 8;
					$('#smoothRadius').val("8");
				}
				if( !is_numeric(colonyMinSize) ){
					colonyMinSize = 40;
					$('#colonyMinSize').val("40");
				}
				if( !is_numeric(colonyMaxSize) ){
					colonyMaxSize = 150;
					$('#colonyMaxSize').val("150");
				}
				if( !is_numeric(colonyNeighbors) ){
					colonyNeighbors = 18;
					$('#colonyNeighbors').val("18");
				}
				if( !is_numeric(debugMode) ){
					debugMode = 0;
					$('#debugMode').val("0");
				}

				
				$("#response").html($("#response").html()+"Running magic plate -- please be paitient, your response will print below once it is complete.<br>");
				if(number == 0){
					$("#response").html($("#response").html()+"Running Manual Magic Plate on project "+project+"..."+"<br>");
					$.ajax({
						url : '<?php echo $base_url?>index.php/utilities/runManualMagicPlate',
						type : 'post',
						data : {
							project: project
						},
						success : function(answer){
							if(answer == "LOGGED OUT"){window.location.href=window.location.href;}
							if(answer == "LOW PERMISSION"){alert("You don't have permission to do this");}
							//str = answer;
							errors = eval(answer);
							str = ""
							for(i in errors){
								str = str + errors[i]+"<br>";
							}
							//alert("Message\nMessage");
							$("#response").html($("#response").html()+"Completed<br>"+str+"<br>");
						}
					});
				} else if(number == 1){
					$("#response").html($("#response").html()+"Running Auto Magic Plate on project "+project+"..."+"<br>");
					$.ajax({
						url : '<?php echo $base_url?>index.php/utilities/runAutoMagicPlate',
						type : 'post',
						data : {
							project: project,
							inputPNG: inputPNG,
							nClusters: nClusters,
							smoothRadius: smoothRadius,
							smoothMode: smoothMode,
							colonyMinSize: colonyMinSize,
							colonyMaxSize: colonyMaxSize,
							colonyNeighbors: colonyNeighbors,
							debugMode: debugMode
						},
						success : function(answer){
							if(answer == "LOGGED OUT"){window.location.href=window.location.href;}
							if(answer == "LOW PERMISSION"){alert("You don't have permission to do this");}
							errors = eval(answer);
							str = ""
							for(i in errors){
								str = str + errors[i]+"<br>";
							}
							//alert("Message\nMessage");
							//if(answer == "Success"){
							$("#response").html($("#response").html()+"Completed<br>"+str+"<br>");
						}
					});
				} else if(number == 2){
					$("#response").html($("#response").html()+"Running Spot On! on project "+project+"..."+"<br>");
					$.ajax({
						url : '<?php echo $base_url?>index.php/utilities/runSpotOn',
						type : 'post',
						data : {
							project: project,
							inputPNG: inputPNG,
							nClusters: nClusters,
							smoothRadius: smoothRadius,
							smoothMode: smoothMode,
							colonyMinSize: colonyMinSize,
							colonyMaxSize: colonyMaxSize,
							colonyNeighbors: colonyNeighbors,
							debugMode: debugMode
						},
						success : function(answer){
							if(answer == "LOGGED OUT"){window.location.href=window.location.href;}
							if(answer == "LOW PERMISSION"){alert("You don't have permission to do this");}
							errors = eval(answer);
							str = "";
							for(i in errors){
								str = str + errors[i]+"<br>";
							}
							//alert("Message\nMessage");
							//if(answer == "Success"){
							$("#response").html($("#response").html()+"Completed<br>"+str+"<br>");
							//$("#response").html($("#response").html()+"Completed<br>"+errors+"<br>");
						}
					});
				} else {
					alert("?");
				}
			}

			function runProcessScript(){
				project = $('#folder-select').val();
				$("#response").html($("#response").html()+"Running Process Script to "+project+"<br>");
				$.ajax({
					url : '<?php echo $base_url?>index.php/utilities/runProcessScript',
					type : 'post',
					data : {
						project: project,
					},
					success : function(answer){
						if(answer == "LOGGED OUT"){window.location.href=window.location.href;}
						if(answer == "LOW PERMISSION"){alert("You don't have permission to do this");}
						//str = answer;
						errors = eval(answer);
						str = ""
						for(i in errors){
							str = str + errors[i]+"<br>";
						}
						//alert("Message\nMessage");
						$("#response").html($("#response").html()+str+"<br>");
					}
				});
			}
		</script>
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
		<div id="explaination">
			<p><b>Utilities Page</b></p>
			<p> This page is used to generate the intensity values for each of the plates in your project.</p>
			<p> In order to process your images please follow the steps outlined below:</p>
			<p> Select your project from the drop down menue</p>
			<p> If you would like to have the computer grid the images update the options below</p>
			<p> If you manually made the grids for your images, or if you are done with the options click "Spot On!"</p>
			<p> Once this is complete click Process Script.  You can view your results on the Quality Control page.</p>
			<p> Note: The magic plate scripts take approximately 1 minute per plate to complete. You are free to leave this page after clicking on the button. If you stay on the page, a message will come up when you are done along with any errors that may have happened.</p>
		</div>
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
			<!--<button type="button" onClick="runMagicPlate(0)">Manual MagicPlate</button>-->
			<!--<button type="button" onClick="runMagicPlate(1)">Auto MagicPlate</button>-->
			<button type="button" onClick="runMagicPlate(2)">Spot On!</button>
			<button type="button" onClick="runProcessScript()">Process Script</button>
			<br>
			<p>
				<!--Input PNG: <input type="text" id="inputPNG" name="inputPNG" value=""> 
				The PNG file name you wish you run magicPlate over.<br>-->
				nClusters: <input type="text" id="nClusters" name="nClusters" value="2"> 
				How many clusters to detect during initial segmentation.<br>
				Smooth Radius: <input type="text" id="smoothRadius" name="smoothRadius" value="8"> 
				Pixel radius by which to smooth over. A radius of 4 will smooth over 64 pixels.<br>
				Smooth Mode: <input type="text" id="smoothMode" name="smoothMode" value="med">
				(avg,med,stdev,min,max).  Which smoothing mode to use.<br>
				Min Colony Size: <input type="text" id="colonyMinSize" name="colonyMinSize" value="40">
				Minimum pixel size for a detectable colony.<br>
				Max Colony Size: <input type="text" id="colonyMaxSize" name="colonyMaxSize" value="150">
				Maximum pixel size for a detectable colony.<br>
				Colony Neighbors: <input type="text" id="colonyNeighbors" name="colonyNeighbors" value="18">
				Minimum required neighbors of the same object within a 1 radius window around each pixel.<br>
				<!--Debug Mode: <input type="text" id="debugMode" name="debugMode" value="0">
				Debug Mode prints out all steps of the pipeline for debug purposes.<br>-->
			</p>
			<div id="response"></div>
			<!--button type="button" onClick="setFolder()">View</button-->
		</div>
	</body>                                                                
</html>
