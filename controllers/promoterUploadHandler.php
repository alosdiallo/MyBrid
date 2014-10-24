<?php
	ini_set('display_errors', 1);

	//echo "Success!\n";
	if ($_FILES["fileToBeUploaded"]["error"] > 0){
		echo "ERROR";
	} else {
		$temporaryFileName = $_FILES["fileToBeUploaded"]["tmp_name"];
		$rawProject = $_POST["folder-select"];
		
		if(move_uploaded_file($temporaryFileName, '/heap/UMassProject/raw_images/'.$rawProject.'dataFiles/promoter.txt')){
			//Move file was successful
			//chmod($GLOBALS['directory']."users/".$user."/projects/".$project."/".$filenameOnServer, 0777);
			echo "SUCCESS";
		} else {
			//Move file was unsuccessful
			echo "ERROR_FILE_MOVE";
		}
  }
?>
