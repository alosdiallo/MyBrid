<?php 
$base_url = base_url();
?>
<html>
<head>
<title> Download all the Positive Data </title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/project_control.js"></script>
<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">

<style type="text/css">
.alignleft {
	float: left;
}
.alignright {
	float: right;
}

#head {
	height: 40;
}
</style>
<script type="text/javascript">
	//////
	// Define base url for project control
	//////
	var base_url = "<?php echo $base_url?>"; 
	var userMem = "<?php echo $this->session->userdata('user_mem')?>";
	var projectMem = "<?php echo $this->session->userdata('project_mem')?>";

	function downloadFile(duplicate){
		var user = $('#user_id').val();
		var project = $('#project_id').val();

		$("#loader").html('<img src="http://franklin-umh.cs.umn.edu/UMassProject/images/loader.gif"/>');
		 
		$.ajax({
			url : '<?php echo $base_url?>index.php/downloadPositiveData/downloadAll',
			type : 'post',
			data : {
				user_id    : user,
				project_id : project,
				duplicate: duplicate
			},
			success : function(answer){	
				if(answer == "error"){
					alert("file does not exist");
				} else{
					var url = eval(answer);
					//alert(url);
					window.location.href = url;
				}
				$("#loader").empty();
			}
		});
	}
	
	function downloadArray(){
		$("#loader").html('<img src="http://franklin-umh.cs.umn.edu/UMassProject/images/loader.gif"/>');
		window.location.href = 'http://franklin-umh.cs.umn.edu/UMassProject/publication/'+$('#arrayFiles').val();
		$("#loader").empty();
	}
	
	function downloadPromoter(){
		$("#loader").html('<img src="http://franklin-umh.cs.umn.edu/UMassProject/images/loader.gif"/>');
		window.location.href = 'http://franklin-umh.cs.umn.edu/UMassProject/publication/'+$('#promoterFiles').val();
		$("#loader").empty();
	}
</script>
</head>

<body>
<div id="head">
	<div class="user_session_controls">
		<?php
			if($this->session->userdata('is_logged_in') == false){
				echo '<FORM METHOD="LINK" ACTION="'.$base_url.'index.php/login/" class="alignright">
					<INPUT TYPE="submit" VALUE="Login">
				</FORM>';
			} else{
				echo '<FORM METHOD="LINK" ACTION="'.$base_url.'index.php/login/logout/" class="alignright">
					<INPUT TYPE="submit" VALUE="Logout">
				</FORM>';
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

<div id='download' class = 'medium-padding'>
	<div id='content'>
		<p>
			Click here to download the data file in .csv format. This may take a while depending on how much data is present.
		</p><p>
			Make sure that you have the correct project selected in the upper right hand corner.
		</p>
	</div>
	<input id='btnLoad' type='button' value='Download Interactions' onclick='downloadFile(0);'></input><br>
	<input id='btnLoad' type='button' value='Download Interactions with Redundancies Removed' onclick='downloadFile(1);'></input><br>
	<p>Click here to download a file listing which proteins are present in an array + where they occur in the array.</p>
	<input id='btnLoad' type='button' value='Download Transcription Factor Array' onclick='downloadArray();'></input>
	<select id='arrayFiles'>
		<option value='C_elegans_Y1H_2010_prey_array.xlsx'>C_elegans_Y1H_2010_prey_array</option>
		<option value='C_elegans_Y2H_2011_prey_array.xlsx'>C_elegans_Y2H_2011_prey_array</option>
	</select><br>
	<p>Click here to download a file listing which baits were screened for a project.</p>
	<input type='button' value='Download Promoter Array' onclick='downloadPromoter();'></input>
	<select id='promoterFiles'>
		<option value='C_elegans_Promoter_TF_Y1H_Bait_list.xlsx'>C_elegans_Promoter_TF_Y1H_Bait_list</option>
		<option value='C_elegans_TF_TF_Y2H_Bait_list.xlsx'>C_elegans_TF_TF_Y2H_Bait_list</option>
		<option value='C_elegans_Cofactor_TF_Y2H_Bait_list.xlsx'>C_elegans_Cofactor_TF_Y2H_Bait_list</option>
	</select><br>
	<!--<input type=button onClick="location.href='<?php echo $base_url?>index.php/tableview/'" value='Advanced Search'><br>-->
	<div id='loader'></div>
	
	<!--
	<button href="<?php echo $base_url?>index.php/tableview/">Advanced Search</button><br>

	<ul><p><h1> <a href="<?php echo $base_url?>index.php/tableview/"> Advanced Search </a></h1></p></ul>
	-->
</div>

</body>
</html>
