<!--?php require_once("/project/csbio/web/UMassProject/dev/ci/phpfileuploader/select-multiple-files-upload.php") ?-->
<?php 
$base_url = base_url();
?>
<html>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/project_control.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>javascript/jquery-1.6.2.js"></script>
<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">

<style type="text/css">
	#head {
		height: 40;
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
<head>
	<title>PHP Upload - Selecting multiple files for upload</title>
	<link href="<?php echo $base_url?>css/universal.css" rel="stylesheet" type="text/css">
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

	<h2>Selecting multiple files for upload</h2>
	<p><b>NOTE: BEFORE YOU UPLOAD ANY FILES MAKE SURE THAT THE CORRECT USER AND PROJECT IS SELECTED!!!</b></p>
	<p> Select multiple files in the file browser dialog then upload them at once [Images: png, gif, jpeg] [Other: all].</p>
	<p>
                    <applet id="jumpLoaderApplet" name="jumpLoaderApplet"
							code="jmaster.jumploader.app.JumpLoaderApplet.class"
							archive="<?php echo $base_url?>jumploader_z.jar"
							width="715"
							height="450"
							mayscript>
								<param name="uc_uploadUrl" value="<?php echo $base_url?>uploadHandler.php"/>
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
						</applet>
						                  </p>
						<!-- callback methods -->
						<script language="javascript">
							/**
							 * applet initialized notification
							 */
							function appletInitialized( applet ) {
								traceEvent( "appletInitialized, " + applet.getAppletInfo() );
							}
							/**
							 * files reset notification
							 */
							function uploaderFilesReset( uploader ) {
								traceEvent( "uploaderFilesReset, fileCount=" + uploader.getFileCount() );
							}
							/**
							 * file added notification
							 */
							function uploaderFileAdded( uploader, file ) {
								traceEvent( "uploaderFileAdded, index=" + file.getIndex() );
							}
							/**
							 * file removed notification
							 */
							function uploaderFileRemoved( uploader, file ) {
								traceEvent( "uploaderFileRemoved, path=" + file.getPath() );
							}
							/**
							 * file moved notification
							 */
							function uploaderFileMoved( uploader, file, oldIndex ) {
								traceEvent( "uploaderFileMoved, path=" + file.getPath() + ", oldIndex=" + oldIndex );
							}
							/**
							 * file status changed notification
							 */
							function uploaderFileStatusChanged( uploader, file ) {
								traceEvent( "uploaderFileStatusChanged, index=" + file.getIndex() + ", status=" + file.getStatus() + ", content=" + file.getResponseContent() );
							}
							/**
							 * file partition uploaded notification
							 */
							function uploaderFilePartitionUploaded( uploader, file ) {
								traceEvent( "uploaderFilePartitionUploaded, index=" + file.getIndex() + ", partition=" + file.getUploadedPartitionCount() + ", response=" + file.getResponseContent() );
								/// If we didn't fail to upload
								if(file.getResponseContent() != "failure"){
								
									/// grab some values
									var project = $("#project_id").val();
									var user = $("#user_id").val();
									var imageName = file.getResponseContent();
									
									var imageSplit = imageName.split(".");
									var imageEXT = imageSplit[imageSplit.length - 1].toUpperCase();
									/// If the file uploaded was an image.
									if(imageEXT == "JPG" || imageEXT == "JPEG" || imageEXT == "PNG" || imageEXT == "GIF"){
										$.ajax({
											url : '<?php echo $base_url?>index.php/admin/addImageByUser',
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
								}
								
							}
							/**
							 * uploader status changed notification
							 */
							function uploaderStatusChanged( uploader ) {
								traceEvent( "uploaderStatusChanged, status=" + uploader.getStatus() );
							}
							/**
							 * uploader selection changed notification
							 */
							function uploaderSelectionChanged( uploader ) {
								traceEvent( "uploaderSelectionChanged" );
							}
							/**
							 * upload view open dialog files selected notification
							 */
							function uploadViewOpenDialogFilesSelected(uploadView, paths ) {
								traceEvent( "uploadViewOpenDialogFilesSelected, paths=" + paths.length );
								for(i = 0; i < paths.length; i++) {
									traceEvent("" + i + ". " + paths[i]);
								}
							}
							/**
							 * main view message shown notification
							 */
							function mainViewMessageShown(mainView, severity, message) {
								traceEvent( "mainViewMessageShown, severity=" + severity + ", message=" + message );
							}
						</script>
						
						<!-- debug auxiliary methods -->
						<script language="javascript">
							/**
							 * trace event to events textarea
							 */
							function traceEvent( message ) {
								document.debugForm.txtEvents.value += message + "\r\n";
							}
							/**
							 * dump status of uploader into html
							 */
							 function dumpUploaderStatus() {
							 	var uploader = document.jumpLoaderApplet.getUploader();
							 	//
							 	//	dump uploader
							 	var uploaderDump = "<strong>Uploader</strong><br>" +
									"Status: " + uploader.getStatus() + "<br>" +
									"Files total: " + uploader.getFileCount() + "<br>" +
									"Ready files: " + uploader.getFileCountByStatus( 0 ) + "<br>" +
									"Uploading files: " + uploader.getFileCountByStatus( 1 ) + "<br>" +
									"Finished files: " + uploader.getFileCountByStatus( 2 ) + "<br>" +
									"Failed files: " + uploader.getFileCountByStatus( 3 ) + "<br>" +
									"Total files length: " + uploader.getFilesLength() + " bytes<br>" +
									"";
							 	//
							 	//	dump files
							 	var filesDump = "<strong>Files</strong><br>";
							 	for( i = 0; i < uploader.getFileCount(); i++ ) {
							 		var file = uploader.getFile( i );
							 		filesDump += "" + ( i + 1 ) + ". path=" + file.getPath +
							 			", length=" + file.getLength() +
							 			", status=" + file.getStatus() +
							 			"<br>";
							 	}
								//
								//	set text
								document.getElementById( "uploaderStatus" ).innerHTML = uploaderDump + "<br>" + filesDump;
							 }
						</script>
						<form name="debugForm">
							<p>Events:<br>
							<textarea name="txtEvents" style="width:100%; font:10px monospace" rows="3" wrap="off" id="txtEvents"></textarea>
							</p>
						</form>
</body>
</html>