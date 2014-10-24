<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Publication extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->load->model('Project_Model');
		
		$loadData['project'] = $this->Project_Model->getFullProjectList();

		$this->load->view('publication_view', $loadData);
	}
}
