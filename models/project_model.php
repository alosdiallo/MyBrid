<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Project_Model extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->model('Data_Model');
		$this->load->model('Config_Model');
	}
	
	////// getUserList
	// gets a list of the users for the system
	//////
	public function getUserList($limitMetaProjects){
		$permission = $this->session->userdata('admin');
		$str = "SELECT DISTINCT user_id FROM Projects WHERE permission >= $permission";
		// If metaprojects is limited disable any metaproject that has multiple projects under it from showing up
		if($limitMetaProjects){
			$str .= " AND metausers NOT LIKE '%,%'"; 
		}
		return $this->makeQuery($str);
	}
	
	////// getProjectList
	// gets a list of projects for the system
	//////
	public function getProjectList($user_id, $limitMetaProjects){
		$permission = $this->session->userdata('admin');
		$str = "SELECT * FROM Projects WHERE user_id = '$user_id' AND permission >= $permission";
		// If metaprojects is limited disable any metaproject that has multiple projects under it from showing up
		if($limitMetaProjects){
			$str .= " AND metausers NOT LIKE '%,%'"; 
		}
		$projectList = $this->makeQuery($str);
		if(!$projectList) {
			// Create a Default Project
			$defaultProject = new stdClass();
			$defaultProject->project_id = "No Projects Found";
			$defaultProject->user_id    = "Default Project User";
			// And return it
			return array($defaultProject);
		}
		return $projectList;
	}
	
	////// Getfull project list
	// Gets the list of all projects in the database
	//////
	public function getFullProjectList(){
		$str = "SELECT * FROM Projects ORDER BY year DESC";

		$query = $this->db->query($str);
		
		if(!$query){
			return array();
		}
		else{
			if($query->num_rows() > 0){
			//print_r($query->result());
				foreach($query->result() as $row){
					$projectList[] = $row;
				}
			} else {return array();}
		}
		
		if(!$projectList) {
			// Create a Default Project
			$defaultProject = new stdClass();
			$defaultProject->project_id = "No Projects Found";
			$defaultProject->user_id    = "Default Project User";
			// And return it
			return array($defaultProject);
		}
		return $projectList;
	}
	
	//// getfulluserList
	// Gets the full list of users from the user table instead of the projects table
	/////
	public function getFullUserList(){
		$str = "SELECT DISTINCT username AS user_id FROM users";
		
		return $this->makeQuery($str);
	}
	
	/*
	 * Callback to make this function better.
	 */
	public function makeQuery($string, $transcriptionData = null){
		$data = array();
		$j=0;
		/*
		 * Make sure the user is logged in to access the database
		 */
		if( !$this->Config_Model->checkLogin() ) {return array();}
		$query = $this->db->query($string);
		
		if(!$query){
			return array();
		}
		else{
			if($query->num_rows() > 0){
			//print_r($query->result());
				foreach($query->result() as $row){
					$data[] = $row;
				}
			} else {return array();}
		}
		return $data;
	}
}
