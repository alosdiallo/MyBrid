<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed\n');

class Autocomplete_Model extends CI_Model{

	private $promoterTable      = "";
	private $transcriptionTable = "";
	private $interactionTable   = "";
	private $imageTable         = "";
	public function __construct(){
		parent::__construct();
		$this->load->model('Config_Model');
		$this->promoterTable      = $this->Config_Model->getPromoterTable();
		$this->transcriptionTable = $this->Config_Model->getTranscriptionTable();
		$this->interactionTable   = $this->Config_Model->getInteractionTable();
		$this->imageTable         = $this->Config_Model->getImageTable();
	}
	
	public function getPromoters($users, $projects){
		$i = 0;
		$tags = array();
		foreach($users as $user){
			$str = "SELECT DISTINCT bait_id FROM $this->promoterTable WHERE user_id = '$user' AND project_id = '".$projects[$i]."'";
			$tags = array_merge($tags, $this->makeAutocompleteQuery($str));
			$str = "SELECT DISTINCT bait_name FROM $this->promoterTable WHERE user_id = '$user' AND project_id = '".$projects[$i]."'";
			$tags = array_merge($tags, $this->makeAutocompleteQuery($str));
			$str = "SELECT DISTINCT bait_name2 FROM $this->promoterTable WHERE user_id = '$user' AND project_id = '".$projects[$i]."'";
			$tags = array_merge($tags, $this->makeAutocompleteQuery($str));
			$str = "SELECT DISTINCT bait_name3 FROM $this->promoterTable WHERE user_id = '$user' AND project_id = '".$projects[$i]."'";
			$tags = array_merge($tags, $this->makeAutocompleteQuery($str));
			$i++;
		}
		return $tags;
	}

	/*$str = "SELECT DISTINCT bait_id, bait_name, bait_name2, bait_name3 FROM $this->promoterTable WHERE user_id = '$user' AND project_id = '$project'";*/
	
	public function getTranscriptionFactors($user, $project){
		$this->load->model('Data_Model');
		$list = $this->Data_Model->getTranscriptionFactorList($user, $project);	
		
		$tags = array();
		$str = "SELECT DISTINCT orf_name FROM $this->transcriptionTable WHERE list = '$list'";
		$tags = array_merge($tags, $this->makeAutocompleteQuery($str));
		$str = "SELECT DISTINCT orf_name2 FROM $this->transcriptionTable WHERE list = '$list'";
		$tags = array_merge($tags, $this->makeAutocompleteQuery($str));
		$str = "SELECT DISTINCT wb_gene FROM $this->transcriptionTable WHERE list = '$list'";
		$tags = array_merge($tags, $this->makeAutocompleteQuery($str));
		$str = "SELECT DISTINCT common_name FROM $this->transcriptionTable WHERE list = '$list'";
		$tags = array_merge($tags, $this->makeAutocompleteQuery($str));
		$str = "SELECT DISTINCT info FROM $this->transcriptionTable WHERE list = '$list'";
		$tags = array_merge($tags, $this->makeAutocompleteQuery($str));

		return $tags;
	}
	
	private function makeAutocompleteQuery($str){
		if( !$this->Config_Model->checkLogin() ) {return array();}
		
		$data = array();
		$query = $this->db->query($str);
		
		if(!$query || $query->num_rows() == 0){
			return false;
		}
		
		foreach($query->result_array() as $row){
			foreach($row as $element){
				if($element != "" && strtoupper($element) != "BLANK"){
					$data[] = $element;
				}
			}
		}
		
		return $data;
	}
}

?>
