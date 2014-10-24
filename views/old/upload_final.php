<?php require_once "/project/csbio/web/UMassProject/dev/ci/phpfileuploader/phpuploader/include_phpuploader.php" ?>
<html>
<body>
	<?php
		$fileguid = @$_POST['myuploader'];
		if($fileguid){
			
			$mvcfile = $uploader->GetUploadedFile($fileguid);
			
			if($mvcfile){
				
				//gets the file name
				echo($mvcfile->FileName);
				//gets the temp file path
				echo($mvcfile->FilePath);
				//gets the size of the file
				echo($mvcfile->FileSize);
				
				//copys the file uploaded file to a new location
				$mvcfile->CopyTo('/project/csbio/web/UMassProject/dev/ci/uploads/');
				//moves the file uploaded to the new location
				$mvcfile->MoveTo('/project/csbio/web/UMassProject/dev/ci/uploads/');
				//deletes this instance
				$mvcfile->Delete();
			}
		}
	?>
</body>
</html>
