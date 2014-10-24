<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Controller of the class Upload
 * handles the upload operation, uses codeigniter 
 * helper form to set up most of the forms
 * 
 * class written by Philippe Ribeiro
 * June 24th, 2010
 */
class Raw_Upload extends CI_Controller {

	/*
	 * constructor calls the CI_Controller parent
	 * loads the helper form and url
	 * starts a session and if the user is not
	 * logged in, redirect to the login page
	 */
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		
		if(!$this->session->userdata('is_logged_in')){redirect('');}
		if($this->session->userdata('admin') > 2){redirect('');}
	}

	/*
	 * function index, loads the upload page
	 * sets error to ''
	 * @access: public
	 * @arguments: void
	 * @return: void
	 */
	public function index(){
		if(!$this->session->userdata('is_logged_in')){redirect('');}
		if($this->session->userdata('admin') > 2){redirect('');}
		$this->load->view('raw_upload', array('error' => ' ' ));
	}

	public function addRawProject(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}

		
		$project = $this->input->post('project');
		if($project){
			$structure = '/heap/UMassProject/raw_images/'.$project;
		}
		
		if(mkdir($structure, 0777)){
			if(!mkdir($structure."/alignments", 0777)){
				echo "failure";
				return;
			}
			if(!mkdir($structure."/images", 0777)){
				echo "failure";
				return;
			}
			if(!mkdir($structure."/quality_control", 0777)){
				echo "failure";
				return;
			}
			if(!mkdir($structure."/final_data", 0777)){
				echo "failure";
				return;
			}
			if(!mkdir($structure."/final_debug", 0777)){
				echo "failure";
				return;
			}
			if(!mkdir($structure."/1to4", 0777)){
				echo "failure";
				return;
			}
			if(!mkdir($structure."/5to8", 0777)){
				echo "failure";
				return;
			}
			if(!mkdir($structure."/9to12", 0777)){
				echo "failure";
				return;
			}
			if(!mkdir($structure."/dataFiles", 0777)){
				echo "failure";
				return;
			}
			/*if(!mkdir($structure."/arrayFiles", 0777)){
				echo "failure";
				return;
			}
			if(!mkdir($structure."/goldFile", 0777)){
				echo "failure";
				return;
			}*/
			chmod($structure, 0777);
			chmod($structure."/alignments", 0777);
			chmod($structure."/images", 0777);
			chmod($structure."/quality_control", 0777);
			chmod($structure."/final_data", 0777);
			chmod($structure."/final_debug", 0777);
			chmod($structure."/1to4", 0777);
			chmod($structure."/5to8", 0777);
			chmod($structure."/9to12", 0777);
			chmod($structure."/dataFiles", 0777);
			/*chmod($structure."/arrayFiles", 0777);
			chmod($structure."/goldFile", 0777);*/
		} else {
			echo "failure";
		}
		echo "success";
	}
	
	public function runOldCameraScript(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}

		$this->runCameraScript('Aconvert_old.pl', $this->input->post('project'));
		echo "Success";
	}
	
	public function runNewCameraScript(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$this->runCameraScript('Aconvert_new.pl', $this->input->post('project'));
		echo "Success";
	}
	
	private function runCameraScript($script, $project){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$source = escapeshellcmd("/heap/UMassProject/scripts/".$script);

		if($project){
			// $project is user supplied, this could be a dangerous command
			$destination = escapeshellcmd('/heap/UMassProject/raw_images/'.$project.'/images/');
		}
		if(file_exists($source) && is_dir($destination)){	
			$str = 'perl '.$source.' '.$destination;
			$execResponse = exec($str, $execOutput);
			echo $str."\n";
			print_r($execOutput);
		}
	}
	
	public function memorizeRawProject(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		$session_data = array('rawProject' => $this->input->post('rawProject'));
		$this->session->set_userdata($session_data);
		echo "MEMORIZED";
	}
	
}
?>
