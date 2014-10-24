<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed\n');

/*
 * class written by Justin Nelson
 * October 4th, 2011
 * 
 * Class Project Control handles the ajax request for changing the project
 * The input will be a new user_id and it will output a list of projects associated
 * with that user.
 * 
 * 
 */
class Project_Control extends CI_Controller{

	/*
	 * default constructor
	 * loads the Autocomplete_Model class to access the database
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model('Project_Model');
	}
	
	public function getProjectList(){
		$user_id = $this->input->post('user_id');
		$limitMetaProjects = $this->input->post('limitMetaProjects');

		echo json_encode($this->Project_Model->getProjectList($user_id, $limitMetaProjects));
	}
	
	public function getUserList(){
		$limitMetaProjects = $this->input->post('limitMetaProjects');

		echo json_encode($this->Project_Model->getUserList($limitMetaProjects));
	}
	
	public function getFullUserList(){
		echo json_encode($this->Project_Model->getFullUserList());
	}
	
	public function getFullProjectList(){
		echo json_encode($this->Project_Model->GetFullProjectList());
	}
	
	public function updateSessionMem(){
		$session_data = array('user_mem' => $this->input->post('user_id'), 'project_mem' => $this->input->post('project_id'));
		$this->session->set_userdata($session_data);
		echo "user " . $this->session->userdata('user_mem') . " project " . $this->session->userdata('project_mem');
	}

}

?>
