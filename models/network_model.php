<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Network_Model extends CI_Model{

	private $promoterTable = "";
	private $transcriptionTable = "";
	private $interactionTable = "";
	private $imageTable = "";
	public function __construct(){
		parent::__construct();
		$this->load->model('Config_Model');
		$this->promoterTable      = $this->Config_Model->getPromoterTable();
		$this->transcriptionTable = $this->Config_Model->getTranscriptionTable();
		$this->interactionTable   = $this->Config_Model->getInteractionTable();
		$this->imageTable         = $this->Config_Model->getImageTable();
	}
	
	public function queryPromoterNodes($user, $project, $promoters){
		if($promoters != null){
			return $this->db->query("SELECT * FROM $this->promoterTable WHERE user_id = '$user' AND project_id = '$project' AND ( bait_id IN ( $promoters ) OR bait_name IN ( $promoters ) OR bait_name2 IN ( $promoters ) OR bait_name3 IN  ( $promoters ) )");
		} else {
			return $this->db->get_where($this->promoterTable, array('user_id' => $userId, 'project_id' => $projectId));
		}
	}
	
	public function queryTranscriptionNodes($user, $project, $tfs){
		// GRAB THE APPROPRIATE TF LIST
		$query = $this->db->query("SELECT tf_list FROM Projects WHERE user_id = '$user' AND project_id = '$project'");
		if($query){ // IS VALID QUERY
		//print_r( $query->result() );
			if($query->num_rows() > 0){ // QUERY HAS RESULTS
				foreach($query->result() as $row){ // ROWS = 1
					if(isset($row->tf_list)){ // TF LIST IS SET
						$list = $row->tf_list;
					}
				}
			} else {return array();}
		} else {return array();}
		// END GRAB LIST
		
		if($tfs != null){
			return $this->db->query("SELECT * FROM $this->transcriptionTable WHERE list = '$list' AND ( coordinate IN ( $tfs ) OR orf_name IN ( $tfs ) OR orf_name2 IN ( $tfs ) OR wb_gene IN ( $tfs ) OR common_name IN ( $tfs ) OR coordinate2 IN ( $tfs ) OR info IN ($tfs) )");
		} else {
			return $this->db->query("SELECT * FROM $this->transcriptionTable WHERE list = '$list'");
		}
	}
	
	public function queryEdges($userId, $projectId, $promoterString, $tfString){
		$str = "SELECT DISTINCT plate_name, plate_number, array_coord FROM $this->interactionTable WHERE user_id = '$userId' AND project_id = '$projectId' AND modified_call = 'Positive' ";
		if($promoterString != ""){
			$str .= "AND plate_name IN ( $promoterString ) ";
		}
		if($tfString != ""){
			$str .= "AND array_coord IN ( $tfString ) ";
		}
	
		return $this->db->query($str);
	}
	
	public function getNetwork($user, $project, $promoters, $tfs){
			if( !$this->Config_Model->checkLogin() ) {return array();}
			
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
			
			$data = array();
			
			$str = "SELECT DISTINCT Interactions.plate_name, Interactions.array_coord, TranscriptorFactor.orf_name, TranscriptorFactor.orf_name2, TranscriptorFactor.wb_gene, TranscriptorFactor.common_name, TranscriptorFactor.alt_name,  TranscriptorFactor.info, TranscriptorFactor.info2, TranscriptorFactor.note, TranscriptorFactor.list, Interactions.plate_median, COUNT(*) as count, AVG(orig_intensity_value) as ave_orig_intensity, AVG(ptp_intensity_value) as ave_ptp_intensity, AVG(rc_intensity_value) as ave_rc_intensity, AVG(z_score) as ave_z_score, AVG(z_prime) as ave_z_prime, Interactions.user_id, Interactions.project_id FROM Interactions, TranscriptorFactor WHERE Interactions.array_coord = TranscriptorFactor.coordinate AND TranscriptorFactor.list = '$list' AND Interactions.user_id = '$user' AND Interactions.project_id = '$project' AND Interactions.modified_call = 'Positive' ";
			
			//$str = "SELECT DISTINCT Interactions.plate_name, TranscriptorFactor.common_name, Interactions.array_coord, TranscriptorFactor.list, AVG(z_prime) as ave_z_prime, COUNT(*) as count, Interactions.user_id, Interactions.project_id FROM Interactions, TranscriptorFactor WHERE Interactions.array_coord = TranscriptorFactor.coordinate AND TranscriptorFactor.list = '$list' AND Interactions.user_id = '$user' AND Interactions.project_id = '$project' AND Interactions.modified_call = 'Positive' ";
			if($tfs != ''){
				$str .= "AND (TranscriptorFactor.orf_name IN ($tfs) OR TranscriptorFactor.orf_name2 IN ($tfs) OR TranscriptorFactor.wb_gene IN ($tfs) OR TranscriptorFactor.common_name IN ($tfs) OR TranscriptorFactor.info IN ($tfs) OR Interactions.array_coord IN ($tfs)) ";
			}
			$str .= "GROUP BY Interactions.plate_name, Interactions.array_coord";
			//echo $str;
			
			$query = $this->db->query($str);
			
			$str2 = "SELECT DISTINCT * FROM Promoter WHERE user_id = '$user' AND project_id = '$project' ";
			if($promoters != ''){
				$str2 .= "AND (bait_id IN ($promoters) OR bait_name IN ($promoters) OR bait_name2 IN ($promoters) OR bait_name3 IN ($promoters)) ";
			}
			$query2 = $this->db->query($str2);
			//echo $str2;
			
			if(!$query || $query->num_rows() == 0){return false;}
			if(!$query2 || $query2->num_rows() == 0){return false;}
			
			$i = 0;
			foreach($query->result() as $row){
				if($row->count > 1){
					foreach($query2->result() as $row2){
						if($row2->bait_id == $row->plate_name){
							$data[$i][0] = $row->plate_name;
							$data[$i][1] = $row2->bait_name;
							$data[$i][2] = $row2->bait_name2;
							$data[$i][3] = $row2->bait_name3;
							$data[$i][4] = $row->array_coord;
							$data[$i][5] = $row->common_name;
							$data[$i][6] = $row->orf_name;
							//$data[$i][6] = $row->orf_name2;
							//$data[$i][7] = $row->wb_gene;
							
							//$data[$i][9] = $row->alt_name;
							$data[$i][7] = $row->info;
							//$data[$i][11] = $row->info2;
							//$data[$i][12] = $row->note;
							//$data[$i][13] = $row->plate_median;
							$data[$i][8] = $row->count;
							$data[$i][9] = $row->ave_orig_intensity;
							$data[$i][10] = $row->ave_rc_intensity;
							$data[$i][11] = $row->ave_ptp_intensity;
							$data[$i][12] = $row->ave_z_score;
							$data[$i][13] = $row->ave_z_prime;
							$data[$i][14] = $row->user_id;
							$data[$i][15] = $row->project_id;
							$data[$i][16] = $row->list;
							
							$i++;
							break;
						}
					}
					
				}
			}
			/*
			foreach($query->result() as $row){
				if($row->count > 1){
					//$data[$i][0] = $row->plate_name;
				
					//$data[$i][2] = "NULL";
					//$data[$i][3] = "NULL";
					foreach($query2->result() as $row2){
						if($row2->bait_id == $row->plate_name){
						//echo "Reached";
							$data[$i][0] = $row2->bait_name;
							$data[$i][1] = $row->common_name;
							$data[$i][2] = $row->ave_z_prime;
							$data[$i][3] = $row->user_id;
							$data[$i][4] = $row->project_id;
							$data[$i][5] = $row->list;
							
							$i++;
							break;
						}
					}
				}
			}
			*/
			return $data;
		}
}
?>
