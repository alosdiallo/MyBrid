<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	
class Table_Model extends CI_Model{

	private $promoterTable = "";
	private $transcriptionTable = "";
	private $interactionTable = "";
	private $imageTable = "";
	public function __construct(){
		parent::__construct();
		$this->load->model('Data_Model');
		
		$this->load->model('Config_Model');
		$this->promoterTable      = $this->Config_Model->getPromoterTable();
		$this->transcriptionTable = $this->Config_Model->getTranscriptionTable();
		$this->interactionTable   = $this->Config_Model->getInteractionTable();
		$this->imageTable         = $this->Config_Model->getImageTable();
	}
	
	/*GET PROMOTER DATA
	 * 
	 * If you want to use a transcriptionFactor for the search you must do a getTranscriptionFactor search first and 
	 * pipe the orf_name into this function as $loadData['transcriptionFactor']
	 * 
	 */
	public function getPromoterData($loadData, $transcriptionData = array(0)){
		// If these are unset they must be set, default is false.
		if( !isset($loadData['promoterSearch']    ) ) { $loadData['promoterSearch']    = false; }
		if( !isset($loadData['interactionSearch'] ) ) { $loadData['interactionSearch'] = false; }
		
		$str = "SELECT "; 
		//What are we searching? Promoters or Interactions?
		if($loadData['promoterSearch']   ){ 
			$str .= "$this->promoterTable.*, ";
		}
		if($loadData['interactionSearch']){ 
			$str .= "$this->interactionTable.* ";
		} else { 
			$str .= "$this->interactionTable.plate_number ";
		}

		if( isset($loadData['zScore_ave']) ){
			$str .=  ", MAX($this->interactionTable.z_score) AS z_score ";
		}
		if( isset($loadData['zPrime_ave']) ){
			$str .=  ", MAX($this->interactionTable.z_prime) AS z_prime ";
		}
		$str .= "FROM ";
		// WHAT AND WHERE
		$needComma = false;
		
		$str .= "$this->promoterTable, $this->interactionTable ";
		/*
		if($loadData['interactionSearch']){
			if($needComma) {$str .= ", ";}
			$str .= "$this->interactionTable ";
			$needComma = true;
		}
		if($loadData['promoterSearch']){
			if($needComma) {$str .= ", ";}
			$str .= "$this->promoterTable ";
			$needComma = true;
		}
		*/
		// Set the need for " AND " to false, NEED A WHERE OTHERWISE!
		$needAnd = false;
		// TABLE JOIN [if needed]
		
		$str .= "WHERE $this->interactionTable.plate_name = $this->promoterTable.bait_id ";
		$needAnd = true;
		/*
		if($loadData['interactionSearch'] && $loadData['promoterSearch']){
			$str .= "WHERE $this->interactionTable.plate_name = $this->promoterTable.bait_id";
			$needAnd = true;		// need " AND "'s now
		}
		*/
		foreach($loadData as $key => $value){
			if($value != ''){
				switch($key){
					case 'plateNumber':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.plate_number = '$value' ";
							$needAnd = true;
						break;
					case 'transcriptionFactor':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.array_coord IN ($value)";
							$needAnd = true;
						break;
					case 'promoter':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "( $this->promoterTable.bait_id IN ($value) OR 
									   $this->promoterTable.bait_name IN ($value) OR 
									   $this->promoterTable.bait_name2 IN ($value) OR 
									   $this->promoterTable.bait_name3 IN ($value) ) ";		// END OR STATEMENT
							$needAnd = true;
						break;
					case 'positive':
							
							if($value == "true" || $value == "True" || $value == "TRUE"){
								if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
								$str .= "$this->interactionTable.modified_call = 'Positive' ";
								$needAnd = true;
							}
						break;
					case 'userId':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->promoterTable.user_id = '$value' ";
							$needAnd = true;
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.user_id = '$value' ";
							
						break;
					case 'projectId':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->promoterTable.project_id = '$value' ";
							$needAnd = true;
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.project_id = '$value' ";
							
						break;
					case 'zScore_min':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.z_score > $value";
							$needAnd = true;
						break;
					case 'zScore_max':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.z_score < $value";
							$needAnd = true;
						break;
					case 'zPrime_min':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.z_prime > $value";
							$needAnd = true;
						break;
					case 'zPrime_max':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.z_prime < $value";
							$needAnd = true;
						break;
					case 'origIntensity_min':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.orig_intensity_value > $value";
							$needAnd = true;
						break;
					case 'origIntensity_max':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.orig_intensity_value < $value";
							$needAnd = true;
						break;
					case 'rcIntensity_min':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.rc_intensity_value > $value";
							$needAnd = true;
						break;
					case 'rcIntensity_max':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.rc_intensity_value < $value";
							$needAnd = true;
						break;
					case 'ptpIntensity_min':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.ptp_intensity_value > $value";
							$needAnd = true;
						break;
					case 'ptpIntensity_max':
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							$str .= "$this->interactionTable.ptp_intensity_value < $value";
							$needAnd = true;
						break;
					case 'bleedover':
						if($value == "true" || $value == "True" || $value == "TRUE"){
							if($needAnd) {$str .= " AND ";} else {$str .= " WHERE ";}
							if($value == TRUE){$str .= "bleed_over = 'BO'";}
							$needAnd = true;
						}
						break;
				}
			}
		}
		$useAnd = false;
		if(isset($loadData['zScore_ave']) || isset($loadData['zPrime_ave'])){
			$str .= "GROUP BY $this->promoterTable.bait_id, $this->interactionTable.plate_number ";
			$str .= "HAVING ";
			if(isset($loadData['zScore_ave'])){
				$str .= "z_score > " . $loadData['zScore_ave'];
				$useAnd = true;
			}
			if(isset($loadData['zPrime_ave'])){
				if($useAnd){$str .= "AND ";}
				$str .= "z_prime > ".$loadData['zPrime_ave'];
				$useAnd = true;
			}
		}

		/*
		if(isset($loadData['zScore_ave'])){
			$str .= "GROUP BY $this->promoterTable.bait_id, $this->interactionTable.plate_number ";
			$str .= "HAVING z_score > ".$loadData['zScore_ave'];
		}
		*/
		$data = array();
		if( !$this->Config_Model->checkLogin() ) {return array();}
		$results = $this->db->query($str);
		if(!$this->session->userdata('is_logged_in')) {return $data;}
		if(!$results || $results->num_rows() == 0){
			return false;
		}
		$j = 0;
		foreach($results->result() as $row){
				$data[$j][0] = $row->plate_name;
				$data[$j][1] = $row->transcriptor_factor;
				$data[$j][2] = $row->orf_name;
				$data[$j][3] = $row->y_coord;
				$data[$j][4] = $row->x_coord;
				$data[$j][5] = $row->plate_median;
				$data[$j][6] = $row->call_type;
				$data[$j][7] = $row->bleed_over;
				$data[$j][8] = $row->human_call;
				$data[$j][9] = $row->modified_call;
				$data[$j][10] = $row->z_score;
				$data[$j][11] = $row->z_prime;
				$data[$j][12] = $row->array_coord;
				$data[$j][13] = $row->orig_intensity_value;
				$data[$j][14] = $row->rc_intensity_value;
				$data[$j][15] = $row->ptp_intensity_value;
				$j++;
		}
		
		return $data;
		
		
		//return $this->makeQuery($str, $transcriptionData);
	}
	
	/*
	 * 
	 */
	public function getTranscriptionFactorData($transcriptionFactors, $user, $project){
		// GRAB THE APPROPRIATE TF LIST
		$query = $this->db->query("SELECT tf_list FROM Projects WHERE user_id = '$user' AND project_id = '$project'");
		if($query){ // IS VALID QUERY
			if($query->num_rows() > 0){ // QUERY HAS RESULTS
				foreach($query->result() as $row){ // ROWS = 1
					if(isset($row->tf_list)){ // TF LIST IS SET
						$list = $row->tf_list;
					}
				}
			} else {return array();}
		} else {return array();}
		// END GRAB LIST
		
		$str = "SELECT coordinate FROM $this->transcriptionTable WHERE list = '$list' AND orf_name IN ($transcriptionFactors) OR orf_name2 IN ($transcriptionFactors) OR wb_gene IN ($transcriptionFactors) OR common_name IN ($transcriptionFactors) OR coordinate  IN ($transcriptionFactors) OR coordinate2 IN ($transcriptionFactors) OR info IN ($transcriptionFactors)";
		return $this->makeQuery($str);
		//return $transcriptionData;
	}
	
	
	private function makeQuery($query){
		
		$data = array();
		if( !$this->Config_Model->checkLogin() ) {return array();}
		$results = $this->db->query($query);
		if(!$this->session->userdata('is_logged_in')) {return $data;}
		if(!$results || $results->num_rows() == 0){
			return false;
		}
		
		foreach($results->result() as $row){
				$data[] = $row;
		}
		
		return $data;
	}
}
