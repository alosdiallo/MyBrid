<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * main controller of the website
 * defines the main Class, UMassProject
 * 
 * class written by Philippe Ribeiro
 * June 24th, 2011
 */
class DownloadPositiveData extends CI_Controller {

	private $time;
	private $file;
	private $header = array('Bait ID', 'Bait Gene Name', 'Bait Alternate Name', 'Bait Family', 'Prey Array Coordinate', 'Prey Gene Name', 'Prey Alternate Name', 'Prey Family', 'Number of Positive Colonies', 'Average Raw Normalized Intensity', 'Average Row Column Normalized Intensity', 'Average Bait to Bait Normalized Intensity', 'Average Z-score', 'Average Z-Prime', 'User', 'Project', 'Prey Array Version');
	
					
	public function __construct(){
		parent::__construct();
		$this->load->model('Download_Model');
		$this->load->model('Data_Model');
		$this->time = date('Y-m-d_H:i:s');
		$this->file = '/heap/UMassProject/downloads/avg_data' . $this->time . '.csv';
		

	}
	
	private function createFile($data){
		
		$fp = fopen($this->file, 'w');
		//print_r($data);
		fputcsv($fp, $this->header);		
		//writes the intermatrix to the file in .csv format
		//fputcsv($fp, $data, chr(44));
		
		foreach($data as $line){
				if($line){
					//fputcsv($fp, (array)$line[0], chr(44));
					fputcsv($fp, (array)$line);
				}
		}
		
		chmod($this->file, 0777);
		fclose($fp);
	}
	
	public function index(){
		if(!$this->session->userdata('is_logged_in')) {redirect('login');}
		$this->load->view('downloadPositiveData.php');
	}
	
	public function downloadAll(){
		$project_info = $this->Data_Model->getMetaProjects($this->input->post('user_id'), $this->input->post('project_id'));
		// If duplicate = 1, duplicate_call must = positive;
		$duplicate = $this->input->post('duplicate');
		
		
		if($project_info['users'] && $project_info['projects']){
			$users = $project_info['users'];
			$projects = $project_info['projects'];
		} else {
			$users = array(0 => "NULL");
			$projects = array(0 => "NULL");
		}

		$user_count = 0;
		$data = array();
		// Get the Data
		foreach($users as $user){
			$data = array_merge($data, $this->Download_Model->getAllPositive($users[$user_count], $projects[$user_count], $duplicate));
			$user_count++;
		}
		
		$this->createFile($data);
		
		if(file_exists($this->file)){
			$url = 'http://franklin-umh.cs.umn.edu/UMassProject/downloads/avg_data' . $this->time . '.csv';
			echo json_encode($url);
		}
		else{
			echo json_encode(false);
		}
	}
}

?>
