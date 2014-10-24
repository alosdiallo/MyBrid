<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed\n');

/*
 * class written by Philippe Ribeiro
 * September 4th, 2011
 * 
 * class Autocomplete invokes the database and gets the results associated with the 
 * input passed by ajax request
 * and returns an array back to the page, which is going to be used to show possible data
 * to the user
 * 
 */
class Autocomplete extends CI_Controller{

	/*
	 * default constructor
	 * loads the Autocomplete_Model class to access the database
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model('Autocomplete_Model');
		$this->load->model('Data_Model');
	}
	
	public function getPromoterData(){
		/*
		$userId = $this->input->post('user');
		$projectId = $this->input->post('project');
		*/
		$project_info = $this->Data_Model->getMetaProjects($this->input->post('user'), $this->input->post('project'));
		
		if($project_info['users'] && $project_info['projects']){
			$users = $project_info['users'];
			$projects = $project_info['projects'];
		} else {
			$users = array(0 => "NULL");
			$projects = array(0 => "NULL");
		}
		
		echo json_encode($this->Autocomplete_Model->getPromoters($users, $projects));
	}
	
	public function getTranscriptionData(){
		$userId = $this->input->post('user');
		$projectId = $this->input->post('project');
		
		echo json_encode($this->Autocomplete_Model->getTranscriptionFactors($userId, $projectId));
	}
}

?>
