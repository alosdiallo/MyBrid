<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Network_View extends CI_Controller{

	private $forbidCharacters = array("/", "'", '"',";");
	private $file;
	private $time;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('Network_Model');
		$this->load->model('Data_Model');
		$this->load->model('Download_Model');
		$this->time = date('Y-m-d_H:i:s');
		$this->file = '/heap/UMassProject/downloads/bait' . $this->time . '.csv';
	}
	
	private function createFile($data){
		//$header = array('Bait Gene Name', 'Prey Gene Name', 'Average Z-Prime', 'User', 'Project', 'List Name');
		$header = array('Bait ID', 'Bait Gene Name', 'Bait Alternate Name', 'Bait Family', 'Prey Array Coordinate', 'Prey Gene Name', 'Prey Alternate Name', 'Prey Family', 'Number of Positive Colonies', 'Average Raw Normalized Intensity', 'Average Row Column Normalized Intensity', 'Average Bait to Bait Normalized Intensity', 'Average Z-score', 'Average Z-Prime', 'User', 'Project', 'Prey Array Version');
		
		
		$fp = fopen($this->file, 'w');
		//print_r($data);
		fputcsv($fp, $header);		
		//writes the intermatrix to the file in .csv format
		//fputcsv($fp, $data, chr(44));
		//var_dump($data);
		
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
		$this->load->model('Config_Model');
		$this->Config_Model->checkLogin();
		
		$this->load->view('network_view');
		
		
	}
	
	public function getNodes(){
		/// Grab from Post
		$promoters = $this->input->post('promoters');
		$tfs = $this->input->post('tfs');
		$edges = $this->input->post('edges');
		
		//////////////////////////////////////////////////////////////////////////
		// Get metaproject info		
		$project_info = $this->Data_Model->getMetaProjects($this->input->post('user'), $this->input->post('project'));
		
		if($project_info['users'] && $project_info['projects']){
			$users = $project_info['users'];
			$projects = $project_info['projects'];
		} else {
			$users = array(0 => "NULL");
			$projects = array(0 => "NULL");
		}
		//////////////////////////////////////////////////////////////////////////

		// Grab the list of nodes that I must find
		$promoterArray = array();
		$tfArray = array();

		foreach($edges as $edge){
			$promoterArray[] = $edge['source'];
			$tfArray[]       = $edge['target'];
		}
		
		// Make sure it is unique.
		$promoterArrayUniq = array_unique($promoterArray);
		$tfArrayUniq       = array_unique($tfArray);
		
		// Create the list strings for use in functions
		// Query the nodes again to get the list for interactions
		$promoterString = "";
		$tfString       = "";
		
		foreach($promoterArrayUniq as $value){
			$promoterString .= "'$value',";
		}
		$promoterString .= "'catch'";
		
		foreach($tfArrayUniq as $value){
			$tfString .= "'$value',";
		}
		$tfString .= "'catch'";

		// We now have a list of nodes that data is required for.
		
		
		
		
		/// Set up the nodes array this will store an id and a name for all of the nodes
		/// for promoters the id will be the bait_id and for tf it will be the array coord.
		$nodes = array();
		
		/// Query up the promoter nodes
		$query = $this->Network_Model->queryPromoterNodes($users[0], $projects[0], $promoterString);
		foreach($query->result() as $value){
			if($value->bait_name  != null){ $name = $value->bait_name ; } else { $name = $value->bait_id   ;}
			
			$nodes[] = array("id" => $value->bait_id, "label" => $name, "shape" => "DIAMOND", "color" => "ORANGE");
		}
		
		/// Query up the transcriptor nodes
		$query = $this->Network_Model->queryTranscriptionNodes($users[0], $projects[0], $tfString);
		foreach($query->result() as $value){
			if($value->common_name != null){ $name = $value->common_name ; } else { $name = $value->coordinate ;}
			$nodes[] = array("id" => $value->coordinate, "label" => $name, "shape" => "ELLIPSE", "color" => "BLUE");
		}
		
		// echo it out
		echo json_encode($nodes);$this->load->model('Network_Model');
		
		//queryTranscriptionNodes($userName, $projectName)
	}
	
	public function getEdges(){
		/// Grab from Post

		$promoters = $this->input->post('promoters');
		$tfs = $this->input->post('tfs');
		
		// Get metaproject info		
		$project_info = $this->Data_Model->getMetaProjects($this->input->post('user'), $this->input->post('project'));
		
		if($project_info['users'] && $project_info['projects']){
			$users = $project_info['users'];
			$projects = $project_info['projects'];
		} else {
			$users = array(0 => "NULL");
			$projects = array(0 => "NULL");
		}




		// Query the nodes again to get the list for interactions
		// This query is to get nodes related to user input and only grab those edges
		$promoterString = "";
		$tfString = "";
		
		if($promoters != null){
			/// Query up the promoter nodes
			$query = $this->Network_Model->queryPromoterNodes($users[0], $projects[0], $promoters);
			foreach($query->result() as $value){
				$promoterString .= "'$value->bait_id',";
			}
			$promoterString .= "'catch'";
		}

		if($tfs != null){
			/// Query up the transcriptor nodes
			$query = $this->Network_Model->queryTranscriptionNodes($users[0], $projects[0], $tfs);
			foreach($query->result() as $value){
				$tfString .= "'$value->coordinate',";
			}
			$tfString .= "'catch'";
		}
		
		
		
		/// Set up the edges array
		$edges = array();
		
		$query = $this->Network_Model->queryEdges($users[0], $projects[0], $promoterString, $tfString);
		foreach($query->result() as $value){
			$edges[] = array("id" => $value->plate_name . "to" . $value->array_coord, "target" => $value->array_coord, "source" => $value->plate_name);
		}
		// echo it out
		echo json_encode($edges);
		
		//queryTranscriptionNodes($userName, $projectName)
	}
	
	public function downloadNetwork(){
		$user = $this->input->post('user');
		$project = $this->input->post('project');
		$promoters = $this->input->post('promoters');
		$tfs = $this->input->post('tfs');
		
		// Get metaproject info		
		$project_info = $this->Data_Model->getMetaProjects($this->input->post('user'), $this->input->post('project'));
		
		if($project_info['users'] && $project_info['projects']){
			$users = $project_info['users'];
			$projects = $project_info['projects'];
		} else {
			$users = array(0 => "NULL");
			$projects = array(0 => "NULL");
		}
		$data = array();
		$data = $this->Network_Model->getNetwork($users[0], $projects[0], $promoters, $tfs);
		
		//print_r($data);
		if($data){
			$this->createFile($data);
			
			if(file_exists($this->file)){
				$url = 'http://franklin-umh.cs.umn.edu/UMassProject/downloads/bait' . $this->time . '.csv';
				//echo "Reached";
				echo json_encode($url);
			} else{
				echo json_encode("error");
			}
		} else {
			echo json_encode("data");
		}
		
	}
}
