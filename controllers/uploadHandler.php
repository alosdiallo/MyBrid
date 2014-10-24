<?php
	//----------------------------------------------
	//	upload file handler script
	//----------------------------------------------

	//
	//	specify file parameter name
	$file_param_name = 'file';

	//
	//	retrieve uploaded file name
	$file_name = $_FILES[ $file_param_name ][ 'name' ];

	//
	//	retrieve uploaded file path (temporary stored by php engine)
	$source_file_path = $_FILES[ $file_param_name ][ 'tmp_name' ];

	//
	//	construct target file path (desired location of uploaded file) -
	//	here we put to the web server document root (i.e. '/home/wwwroot')
	//	using user supplied file name
	$target_file_path = $_SERVER[ 'DOCUMENT_ROOT' ] . "/UMassProject/dev/ci4/uploaded" . $file_name;

	//
	//	move uploaded file
	echo "Moving file " . $source_file_path . " > " . $target_file_path . ": ";
	if( move_uploaded_file( $source_file_path, $target_file_path ) ) {
		echo "success";
	} else{
		echo "failure";
	}

	//
	//	below is trace of variables
?>
<html>
<body>
	<h1>GET content</h1>
	<pre><?print_r( $_GET );?></pre>
	<h1>POST content</h1>
	<pre><?print_r( $_POST );?></pre>
	<h1>FILES content</h1>
	<pre><?print_r( $_FILES );?></pre>
</body>
