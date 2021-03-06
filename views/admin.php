<?php 
$base_url = base_url();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>MyBrid - Admin Panel</title>
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery-1.6.2.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/admin_control.js"></script>
<script type="text/javascript">
	var base_url = "<?php echo $base_url?>";
	
	
	window.onload = grabListManagementLists();
	
	function grabListManagementLists(){
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/getTranscriptionFactorLists',
			type : 'post',
			data : {},
			success : function(answer){
				lists = eval(answer);
				str = "";
				for(i in lists){
					 str = str + "<option value='"+lists[i]+"'>"+lists[i]+"</option>";
				}
				$('#listManagementLists').html(str);
			}
		});
	}
	
	
	function deleteUser(){
		var user = $("#user_id").val();
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/deleteUser',
			type : 'post',
			data : {
				username   : user
			},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
				getFullUserListUpdate();
			}
		});
	}
	
	function addProject(){
		var user = $("#user_id").val();
		var projectName = $("#newProjectName").val();
		
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/addProject',
			type : 'post',
			data : {
				username   : user,
				projectname: projectName
			},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
				getFullProjectListUpdate();
			}
		});
	}
	
	function deleteProject(){
		var project = $("#project_id").val();
		
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/deleteProject',
			type : 'post',
			data : {
				projectname   : project
			},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
				getFullProjectListUpdate();
			}
		});
	}
	
	function deleteData(){
		$("#databaseResponse").html('ARE YOU SURE (DELETE ALL)?' + 
		                            '<button type="button" id="deleteDataYes" onClick="deleteDataYes()">Yes</button>' +
		                            '<button type="button" id="deleteDataNo" onClick="deleteDataNo()">No</button>');
	}
	function deleteDataYes(){
		var user = $("#user_id").val();
		var projectName = $("#project_id").val();
		
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/deleteData',
			type : 'post',
			data : {
				username   : user,
				projectname: projectName
			},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
				getFullProjectListUpdate();
			}
		});
	}
	
	function deleteDataNo(){
		$("#databaseResponse").html('');
	}
	
	function deletePromoterData(){
		$("#databaseResponse").html('ARE YOU SURE (DELETE Promoter)?' + 
		                            '<button type="button" id="deleteDataYes" onClick="deletePromoterDataYes()">Yes</button>' +
		                            '<button type="button" id="deleteDataNo" onClick="deletePromoterDataNo()">No</button>');
	}
	function deletePromoterDataYes(){
		var user = $("#user_id").val();
		var projectName = $("#project_id").val();
		
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/deletePromoterData',
			type : 'post',
			data : {
				username   : user,
				projectname: projectName
			},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
				getFullProjectListUpdate();
			}
		});
	}
	
	function deletePromoterDataNo(){
		$("#databaseResponse").html('');
	}
	
	function deleteInteractionData(){
		$("#databaseResponse").html('ARE YOU SURE (DELETE Interaction)?' + 
		                            '<button type="button" id="deleteDataYes" onClick="deleteInteractionDataYes()">Yes</button>' +
		                            '<button type="button" id="deleteDataNo" onClick="deleteInteractionDataNo()">No</button>');
	}
	function deleteInteractionDataYes(){
		var user = $("#user_id").val();
		var projectName = $("#project_id").val();
		
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/deleteInteractionData',
			type : 'post',
			data : {
				username   : user,
				projectname: projectName
			},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
				getFullProjectListUpdate();
			}
		});
	}
	
	function deleteInteractionDataNo(){
		$("#databaseResponse").html('');
	}
	
	function addPublication(){
		var projectName = $("#project_id").val();
		var titleName = $("#titleName").val();
		var authorName = $("#authorName").val();
		var abstractName = $("#abstractName").val();
		var paperName = $("#paperName").val();
		var dataName = $("#dataName").val();
		var yearName = $("#yearName").val();
		
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/addPublication',
			type : 'post',
			data : {
				projectName: projectName,
				titleName: titleName,
				authorName: authorName,
				abstractName: abstractName,
				paperName: paperName,
				dataName: dataName,
				yearName: yearName
			},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
		
		
	function addImage(){
		var project = $("#project_id").val();
		var user = $("#user_id").val();
		var imageName = $("#newImageName").val();

		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/addImage',
			type : 'post',
			data : {
				projectname: project,
				username: user,
				imagename: imageName
			},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
	
	function backupTables(){
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/backupTables',
			type : 'post',
			data : {},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
	
	function addInteractionToTable(){
		var project = $("#project_id").val();
		var user = $("#user_id").val();
		var interaction = $("#interactionName").val();
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/addInteractionToTable',
			type : 'post',
			data : {
				projectname: project,
				username: user,
				interactionname: interaction
				},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}

	function addPromoterToTable(){
		var project = $("#project_id").val();
		var user = $("#user_id").val();
		var promoter = $("#promoterName").val();
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/addPromoterToTable',
			type : 'post',
			data : {
				projectname: project,
				username: user,
				promotername: promoter
				},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
	
	function addTranscriptionToTable(){
		var transcriptionFactorName = $("#transcriptionName").val();
		var transcriptionFactorList = $("#transcriptionList").val();
		
		if(transcriptionFactorList == ""){
			alert("Pick a List!");
		} else {
			$.ajax({
				url : '<?php echo $base_url?>index.php/admin/addTranscriptionToTable',
				type : 'post',
				data : {
					transcriptionFactorName: transcriptionFactorName,
					transcriptionFactorList: transcriptionFactorList
					},
				success : function(answer){
					response = answer;
					$("#databaseResponse").html(response);
				}
			});
		}
	}
	
	function renameProject(){
		var user = $("#user_id").val();
		var oldProjectName = $("#project_id").val();
		var newProjectName = $("#renamedProjectName").val();
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/renameProject',
			type : 'post',
			data : {
				oldprojectname: oldProjectName,
				newprojectname: newProjectName,
				username:           user
				},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
	
	function changeUserPermission(){
		var user = $("#user_id").val();
		var newValue = $("#newUserPermission").val();
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/changeUserPermission',
			type : 'post',
			data : {
				value: newValue,
				username: user
				},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
	
	function changeProjectPermission(){
		var user = $("#user_id").val();
		var project = $("#project_id").val();
		var newValue = $("#newProjectPermission").val();
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/changeProjectPermission',
			type : 'post',
			data : {
				value: newValue,
				projectname: project,
				username: user
				},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
	
	function changeMetaProjects(){
		var user = $("#user_id").val();
		var project = $("#project_id").val();
		var metausers = $("#metausers").val();
		var metaprojects = $("#metaprojects").val();
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/changeMetaProjects',
			type : 'post',
			data : {
				user: user,
				project: project,
				metausers: metausers,
				metaprojects: metaprojects
				},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
	
		
		
	function deleteImageTable(){
		var user = $("#user_id").val();
		var project = $("#project_id").val();
		var baitImageName = $("#baitImageName").val();
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/deleteImageTable',
			type : 'post',
			data : {
				user: user,
				project: project,
				baitImageName: baitImageName
				},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
	
	
	
	function deleteSingleBait(){
		var user = $("#user_id").val();
		var project = $("#project_id").val();
		var bait = $("#baitSingleDelete").val();
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/deleteSingleBait',
			type : 'post',
			data : {
				user: user,
				project: project,
				bait: bait
				},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
	
	function attachListToProject(){
		var project = $("#project_id").val();
		var user = $("#user_id").val();
		var list = $("#listManagementLists").val();
		$.ajax({
			url : '<?php echo $base_url?>index.php/admin/attachListToProject',
			type : 'post',
			data : {
				user: user,
				project: project,
				list: list
				},
			success : function(answer){
				response = answer;
				$("#databaseResponse").html(response);
			}
		});
	}
</script>
</head>
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
	<body id="main_body" >
	<hr>
	<!--Project Control Divs-->
	<span id="user_project_controls">
		User:
		<span id="user_controls" >
			<select id="user_id" name="user_id"></select>
		</span>
		Project:
		<span id="project_controls" >
			<select id="project_id" name="project_id"></select>
		</span>
	</span>
	<hr>
		<p>To register a new user, click on the button below and fill in the form.</p>
		<p><a href="<?php echo $base_url?>index.php/register/"><button  value="Register New User"> Register New User </button></a></p>
	<hr>
		<p>To delete a user, you must first delete all of that users data and all of that users projects. After you have deleted that users data and projects, select the user in the box above and click on the delete user button.</p>
		<p><button type="button" id="deleteUser" onClick="deleteUser()">Delete User</button></p>
	<hr>
		<p>To add a project, add in the project name in the textbox. Then select the user that the project will belong to in the box above and click on the Add Project button.</p>
		<p><button type="button" id="addProject" onClick="addProject()">Add Project</button><input type="text" id="newProjectName"/></p>
	<hr>
		<p>To delete a project, you must first delete all of that projects data. After you have deleted that projects data, select the project in the box above and click on the delete project button.</p>
		<p><button type="button" id="deleteProject" onClick="deleteProject()">Delete Project</button></p>
	<hr>
		<p>Both the username and the project must be correct for ANY data to be deleted and a confirmation will come up.</p>
		<p><button type="button" id="deletePromoterData" onClick="deletePromoterData()">Delete Data in Promoter Table</button></p>
		<p><button type="button" id="deleteInteractionData" onClick="deleteInterationData()">Delete Data in interaction Table</button></p>
		<p><button type="button" id="deleteData" onClick="deleteData()">Delete Data</button></p>
	<hr>
		<p>To attach publication data to a project, first select the correct project for the data to be attached to and then fill in any fields that you wish to update. The paper and data fields should be the name of files for the system. And will need to be uploaded separately.</p>
		
		Title <br><textarea cols="80" rows="2" id="titleName"/></textarea><br><br>
		Authors <br><textarea cols="80" rows="2" id="authorName"/></textarea><br><br>
		Abstract <br><textarea cols="80" rows="6" id="abstractName"/></textarea><br><br>
		Paper <br><input type="text" id="paperName"/><br><br>
		Data <br><input type="text" id="dataName"/><br><br>
		Year <br><input type="text" id="yearName"/><br><br>

		<p><button type="button" id="addPublication" onClick="addPublication()">Add Publication</button>
	<hr>
		<p> This is the section about adding data into the mysql tables. Please follow directions closely </p>
		
		<p> To backup the mysql tables, click on the button below. It will delete all current backup tables. </p>
		
		<p><button type="button" id="backup" onClick="backupTables()">Backup Mysql Tables</button>
		
		<p> MAKE SURE YOU HAVE BOTH A USER AND A PROJECT SELECTED! </p>
		
		<p> After you have the user and project selected, enter the name of the interaction file into the area below and then click Add to Table </p>
		
		<p>Interaction File <br><input type="text" id="interactionName"/></p>

		<p><button type="button" id="addInteractionToTable" onClick="addInteractionToTable()">Add to Interaction Table</button>
		
		<p>Promoter File <br><input type="text" id="promoterName"/></p>

		<p><button type="button" id="addPromoterToTable" onClick="addPromoterToTable()">Add to Promoter Table</button>
		
		<p>Transcription Factor File <br><input type="text" id="transcriptionName"/></p>
		<p>Transcription Factor List Name <br><input type="text" id="transcriptionList"/></p>

		<p><button type="button" id="addTranscriptionToTable" onClick="addTranscriptionToTable()">Add to Transcription Factor Table</button>
	<hr>
		<p>This is where you can modify the name of a project. Select the project you want to rename AND the correct username, input the new name below then hit rename project</p>
		
		<p>New Project Name <br><input type="text" id="renamedProjectName"/></p>
		
		<p><button type="button" onClick="renameProject()">Rename Project</button>
		
	<!--End Project Control Divs-->

	<hr>
		<p>Manual Add to images table</p>
		<p>This is a temporary fix for any plates that do not show up in the system right now. You can add the plate to the images table</p>
		<p>Make sure the user and the project are correctly set before doing this</p>
		<p><button type="button" id="addImage" onClick="addImage()">Add Image</button><input type="text" id="newImageName" size=40/></p>
	<hr>
		<p><b>Permission Level Tweaks</b></p>
		<p>Change the permission of a user or a project by typing in the permission number. Users can view all projects with a higher permission than them (ie, user permission level 1 can view all projects; user permission level 3 can view all projects with permission level 3 or greater)</p>
		<p>Changing user permissions requires you to only select the user, changing project permission requires the user and project</p>
		<p>User Permission <button type="button" onClick="changeUserPermission()">Change</button><input type="text" id="newUserPermission" /></p>
		<p>Project Permission <button type="button" onClick="changeProjectPermission()">Change</button><input type="text" id="newProjectPermission" /></p>
	<hr>
		<p><b>MetaProjects</b></p>
		<p>Select a user and project and then make a list of users and projects that belong to it. Each user/project should be separated by a comma, no space (ie: jnelson,alos,marian). The ordering for the users should match up with the ordering for the projects</p>
		<p>Users <input type="text" id="metausers" /></p>
		<p>Projects <input type="text" id="metaprojects" /></p>
		<p><button type="button" onClick="changeMetaProjects()">Change</button></p>
	<hr>
		<p><b>Delete from Image Table</b></p>
		<p>This will delete entries from the image table, input the bait_id you want deleted below. Make sure to select user_id and project_id.</p>
		<p>Bait <input type="text" id="baitImageName" /></p>
		<p><button type="button" onClick="deleteImageTable()">Delete</button></p>
	<hr>
		<p><b>Delete Single Bait Entry</b></p>
		<p>Select a single Bait Id to delete from the Interaction table. Make sure to select user_id and project_id</p>
		<p>Bait <input type="text" id="baitSingleDelete" /></p>
		<p><button type="button" onClick="deleteSingleBait()">Delete</button></p>
	<hr>
		<p><b>List Management</b></p>
		<p>Attach a list to a project</p>
		<p>Select a project above and select the name of the list.</p>
		<select id='listManagementLists'></select> 
		<p><button type="button" onClick="attachListToProject()">Attach to Project</button></p>
	<hr>
	<p><b>Response</b></p>
	<div id="databaseResponse"><p>This is where the response for your actions ends up</p></div>
	
	</body>
</html>
