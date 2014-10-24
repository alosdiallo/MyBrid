<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Delete_Project extends CI_Controller {
	
	private $forbidCharacters = array("/", "'", '"');
	
	/*
	 * default contructor, loads the modules necessaries 
	 * in this class.
	 * @args: Register_Model is the Model of Register, which acess the database
	 * @args: helper url - used from the framework to sanitize the 
	 * 			arguments passed by the user
	 * @form_validation: actually checks whether all the forms were valid
	 */
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}
	public function index(){
		if(!$this->session->userdata('is_logged_in')){redirect('');}
		if($this->session->userdata('admin') > 2){redirect('');}
		$this->load->view('delete_project.php');
	}
	

		
	//Made by Alos 
	public function DeleteRawProject(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$rawProject = addslashes($this->input->post("rawProject"));

		$directory = '/heap/UMassProject/raw_images/'.$rawProject.'/';

		if(file_exists($directory)){
		
				echo "File Exists Deleting File!!!!!"."<br>";
				$exec_str = "perl /heap/UMassProject/scripts/delete_project.pl ".escapeshellarg($directory)." 2>$1";
				exec($exec_str, $execOutput);
				print_r($execOutput);
				echo "<br>".$exec_str."<br>";
				echo "COMPLETE"."<br>";
	
		}
		
		else {
			echo "The project does not exist.";
		}
	}
	


}
