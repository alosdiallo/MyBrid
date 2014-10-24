<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Align_Plate extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		//$this->Config_Model->checkLogin();
		if(!$this->session->userdata('is_logged_in')){redirect('');}
		if($this->session->userdata('admin') > 2){redirect('');}
		$this->load->view('align_plate.php');
	} // end function index
	
	public function getRawImagesAsArray(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$project = addslashes($this->input->post("project"));
		$directory = '/heap/UMassProject/raw_images/'.$project.'/images/';
		$raw_images = array_values(array_diff(scandir($directory), array('..', '.')));
		$final_images = array();
		foreach($raw_images as $raw_image){
			$split_raw_image = explode(".", $raw_image);
			// Grab the last element in the split raw project, uppercase and see if it is png
			if(strtoupper(end($split_raw_image)) == "PNG"){
				$final_images[] = $raw_image;
			}
		}
		//var_dump($raw_images);
		echo json_encode($final_images);
	}
	
	//Made by Alos 
	public function DeleteRawImage(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$project = addslashes($this->input->post("project"));
		$image = addslashes($this->input->post("image"));
		$directory = '/heap/UMassProject/raw_images/'.$project.'/images/';
		$full_path = $directory.$image;
		if(file_exists($directory)){
			if(file_exists($full_path)){
				echo "File Exists Deleting File!!!!!"."<br>";
				$exec_str = "perl /heap/UMassProject/scripts/delete_file.pl ".escapeshellarg($image)." ".escapeshellarg($directory)." ".escapeshellarg($full_path)." 2>$1";
				exec($exec_str, $execOutput);
				print_r($execOutput);
				echo "<br>".$exec_str."<br>";
				echo "COMPLETE"."<br>";
			}
			else {
				echo "The image does not exist.";
			}
		}
		
		else {
			echo "The project does not exist.";
		}
	}
	public function oldImage(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$project = addslashes($this->input->post("project"));
		$image = addslashes($this->input->post("image"));
		$directory = '/heap/UMassProject/raw_images/'.$project.'/images/';
		$full_path = $directory.$image;
		if(file_exists($directory)){
			if(file_exists($full_path)){
				echo "File Exists cropping File!!!!!"."<br>";
				$exec_str = "perl /heap/UMassProject/scripts/Single_convertO.pl ".escapeshellarg($image)." ".escapeshellarg($directory)." ".escapeshellarg($full_path)." 2>$1";
				exec($exec_str, $execOutput);
				print_r($execOutput);
				echo "<br>".$exec_str."<br>";
				echo "COMPLETE"."<br>";
			}
			else {
				echo "The image does not exist.";
			}
		}
		
		else {
			echo "The project does not exist.";
		}
	}
	public function newImage(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$project = addslashes($this->input->post("project"));
		$image = addslashes($this->input->post("image"));
		$directory = '/heap/UMassProject/raw_images/'.$project.'/images/';
		$full_path = $directory.$image;
		if(file_exists($directory)){
			if(file_exists($full_path)){
				echo "File Exists cropping File!!!!!"."<br>";
				$exec_str = "perl /heap/UMassProject/scripts/Single_convertN.pl ".escapeshellarg($image)." ".escapeshellarg($directory)." ".escapeshellarg($full_path)." 2>$1";
				exec($exec_str, $execOutput);
				print_r($execOutput);
				echo "<br>".$exec_str."<br>";
				echo "COMPLETE"."<br>";
			}
			else {
				echo "The image does not exist.";
			}
		}
		
		else {
			echo "The project does not exist.";
		}
	}
	
	
	
	
	
	
	
	/*$COLONIES_X = 48;
	$COLONIES_Y = 32;*/
	public function saveAlignment(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$COLONIES_X = 48;
		$COLONIES_Y = 32;
		$project = addslashes($this->input->post("project"));
		$image = addslashes($this->input->post("image"));
		$align_1_X = addslashes($this->input->post("align_1_X"));
		$align_1_Y = addslashes($this->input->post("align_1_Y"));
		$align_2_X = addslashes($this->input->post("align_2_X"));
		$align_2_Y = addslashes($this->input->post("align_2_Y"));
		
		$directory = '/heap/UMassProject/raw_images/';
		$alignmentFolder = '/images/';
		
		$pixelsBetweenColonies_X = ($align_2_X - $align_1_X) / ($COLONIES_X - 1);
		
		$image = rtrim($image, "abcdefghijklmnopqrstuvwxyz");
		$image = rtrim($image, ".");
		
		$X_alignmentFileHandler = fopen($directory.$project.$alignmentFolder.$image.'._x_coords.txt', 'w') or die("can't open file");
		$Y_alignmentFileHandler = fopen($directory.$project.$alignmentFolder.$image.'._y_coords.txt', 'w') or die("can't open file");
		
		for($i = 0; $i < $COLONIES_X + 1; $i++){
			$x_value = round($pixelsBetweenColonies_X * $i + $align_1_X - $pixelsBetweenColonies_X/2);
			fwrite($X_alignmentFileHandler, $x_value . "\n");
		}
		
		for($j = 0; $j < $COLONIES_Y + 1; $j++){
			$y_value = round($pixelsBetweenColonies_X * $j + ($align_1_Y + $align_2_Y)/2 - $pixelsBetweenColonies_X/2);
			fwrite($Y_alignmentFileHandler, $y_value . "\n");
		}
		fclose($X_alignmentFileHandler);
		fclose($Y_alignmentFileHandler);
		
		chmod($directory.$project.$alignmentFolder.$image.'._x_coords.txt', 0777);
		chmod($directory.$project.$alignmentFolder.$image.'._y_coords.txt', 0777);
		
		echo "Success";
	}
	
	/*
	COLONIES_X = 48; 
			COLONIES_Y = 32;
			ALIGN_DOT_SIZE = 4;
			function drawFullAlignment(){
				if(align_1_X != -1 && align_1_Y != -1 && align_2_X != -1 && align_2_Y != -1){
					fullAlignGraphics.clear();
					colony_X_align = (align_2_X - align_1_X) / 47;
					colony_Y_align = (align_2_Y - align_1_Y) / 47;
					for(var x = 0; x < COLONIES_X; x++){
						for(var y = 0; y < COLONIES_Y; y++){
							xPos = x * colony_X_align - y * colony_Y_align + align_1_X;
							yPos = y * colony_X_align + x * colony_Y_align + align_1_Y; 
							//xPos = x * colony_X_align;
							//yPos = y * colony_X_align; 
							fullAlignGraphics.drawEllipse(getX(document.getElementById("curimg"))+xPos-(ALIGN_DOT_SIZE/2), getY(document.getElementById("curimg"))+yPos-(ALIGN_DOT_SIZE/2), ALIGN_DOT_SIZE, ALIGN_DOT_SIZE);
						}
					}
				
				
					fullAlignGraphics.paint();
				}
			}
	*/
}
