<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utilities extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		if(!$this->session->userdata('is_logged_in')){redirect('');}
		if($this->session->userdata('admin') > 2){redirect('');}
		$this->load->view('utilities.php');
	} // end function index
	
	
	
	public function runManualMagicPlate(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$project = escapeshellcmd($this->input->post("project"));
		$projectFolder = "/heap/UMassProject/raw_images/".$project;
		//$execute_str = "/heap/UMassProject/scripts/Mwork.pl ".$projectFolder."/images ".$projectFolder."/alignments > ".$projectFolder."/output.txt 2>&1 &";
		$execute_str = "/heap/UMassProject/scripts/Mwork.pl ".$projectFolder."/images ".$projectFolder."/alignments > ".$projectFolder."/outputM.txt 2>&1 &";
		
		exec($execute_str, $execute_output);
		echo json_encode($execute_output);
		//echo $execute_str;
	}
	
	public function runAutoMagicPlate(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$project = escapeshellcmd($this->input->post("project"));
		
		$inputPNG = escapeshellcmd($this->input->post("inputPNG"));
		$nClusters = escapeshellcmd($this->input->post("nClusters"));
		$smoothRadius = escapeshellcmd($this->input->post("smoothRadius"));
		$smoothMode = escapeshellcmd($this->input->post("smoothMode"));
		$colonyMinSize = escapeshellcmd($this->input->post("colonyMinSize"));
		$colonyMaxSize = escapeshellcmd($this->input->post("colonyMaxSize"));
		$colonyNeighbors = escapeshellcmd($this->input->post("colonyNeighbors"));
		$debugMode = escapeshellcmd($this->input->post("debugMode"));
		
		// $inputPNG $nClusters $smoothRadius $smoothMode $colonyMinSize $colonyMaxSize $colonyNeighbors $debugMode
		
		$projectFolder = "/heap/UMassProject/raw_images/".$project;
		$execute_str = "/heap/UMassProject/scripts/Awork.pl $projectFolder/images $nClusters $smoothRadius $smoothMode $colonyMinSize $colonyMaxSize $colonyNeighbors > $projectFolder/outputA.txt 2>&1";
		exec($execute_str, $execute_output);
		echo json_encode($execute_output);
	}
	
	public function runSpotOn(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$project = escapeshellcmd($this->input->post("project"));
		
		$inputPNG = escapeshellcmd($this->input->post("inputPNG"));
		$nClusters = escapeshellcmd($this->input->post("nClusters"));
		$smoothRadius = escapeshellcmd($this->input->post("smoothRadius"));
		$smoothMode = escapeshellcmd($this->input->post("smoothMode"));
		$colonyMinSize = escapeshellcmd($this->input->post("colonyMinSize"));
		$colonyMaxSize = escapeshellcmd($this->input->post("colonyMaxSize"));
		$colonyNeighbors = escapeshellcmd($this->input->post("colonyNeighbors"));
		$debugMode = escapeshellcmd($this->input->post("debugMode"));
		
		// $inputPNG $nClusters $smoothRadius $smoothMode $colonyMinSize $colonyMaxSize $colonyNeighbors $debugMode
		
		$projectFolder = "/heap/UMassProject/raw_images/".$project;
		//$execute_str = "/heap/UMassProject/scripts/plate_controller.pl $projectFolder/images $nClusters $smoothRadius $smoothMode $colonyMinSize $colonyMaxSize $colonyNeighbors $projectFolder/alignments > $projectFolder/output.txt 2>&1";
		$execute_str = "/heap/UMassProject/scripts/plate_controller.pl $projectFolder/images $nClusters $smoothRadius $smoothMode $colonyMinSize $colonyMaxSize $colonyNeighbors $projectFolder/alignments > $projectFolder/Debug.txt 2>&1";
		exec($execute_str, $execute_output);
		print_r($execute_output);
	}

	public function runProcessScript(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$project = escapeshellcmd($this->input->post("project"));
		$projectFolder = "/heap/UMassProject/raw_images/".$project."/";
		$execute_str = "/heap/UMassProject/scripts/controler_new.pl ".$projectFolder;
		exec($execute_str, $execute_output);
		echo json_encode($execute_output);
	}
}
