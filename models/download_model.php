<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	
class Download_Model extends CI_Model{

	private $default_z_score = 2.05;

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
		
		public function getData($tf_list, $userId, $projectId){
		
				$data = array();
				
				foreach($tf_list as $transcriptor){
					
						$result = $this->getAverageData($transcriptor, $userId, $projectId, $this->default_z_score);
						if(count($result) != 0){
							$data[] = $result;
						}
				}
				
				return $data;
		}
		
		/*
		 * function getAllPositives
		 * 
		 * makes a list from the query result obtained from the 
		 * getAllTF function, storing them in an array
		 * 
		 * @access : public
		 * @param: void
		 * @return : array (objects)
		 * 
		 $str = "SELECT DISTINCT Interactions.plate_name, Promoter.bait_name, Promoter.bait_name2, Promoter.bait_name3, Interactions.array_coord, TranscriptorFactor.orf_name, TranscriptorFactor.orf_name2, TranscriptorFactor.wb_gene, TranscriptorFactor.common_name, TranscriptorFactor.info, Interactions.plate_median, COUNT(*), AVG(orig_intensity_value), AVG(rc_intensity_value), AVG(ptp_intensity_value), AVG(Interactions.z_score), AVG(Interactions.z_prime), Interactions.user_id, Interactions.project_id FROM Interactions, TranscriptorFactor, Promoter WHERE Interactions.array_coord = TranscriptorFactor.coordinate AND Promoter.bait_id = Interactions.plate_name AND Interactions.user_id = '$userId' AND Interactions.project_id = '$projectId' AND Promoter.user_id = '$userId' AND Promoter.project_id = '$projectId' AND Interactions.modified_call = 'Positive' GROUP BY Interactions.plate_name, Interactions.array_coord";
		 
		 private $header = array('Plate Name', 'Bait Name', 'Bait Name 2', 'Bait Name 3', 'Array Coord', 'Orf Name', 'Orf Name 2', 'WB Gene', 'Common Name', 'Info', 'Plate Median', 'Positives', 'PTP Intensity', 'Z-Prime', 'User', 'Project');
		 */
		public function getAllPositive($user, $project, $duplicate = 0){
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
			if($duplicate){
				$str .= "AND Interactions.duplicate_call = 'Positive' ";
			}
			$str .= "GROUP BY Interactions.plate_name, Interactions.array_coord";
			
			
			
			$query = $this->db->query($str);
			
			$query2 = $this->db->query("SELECT DISTINCT * FROM Promoter WHERE user_id = '$user' AND project_id = '$project'");
			
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

			return $data;
		}
		
		
		/*
		 * function getAllTF
		 * 
		 * retrives a list containing all the transcriptor factors
		 * presented on the database;
		 * 
		 * @access : private
		 * @param: void
		 * @return : array (objets)
		 */
		private function getAllTF(){
			$str = "SELECT DISTINCT transcriptor_factor FROM $this->interactionTable  WHERE call_type='Positive'";
			return $this->makeQuery($str);
		}
		
		/*
		 * function getAverageData
		 * given the transcriptor passed in by the user, it uses that 
		 * value in the search for the average results
		 * 
		 * @access : public
		 * @param : String
		 * @return : array (objects) 
		 * 
		 */
		 
		public function getAverageData($transcriptor, $userId, $projectId, $zScore){
			
			$str = "SELECT plate_name, array_coord, transcriptor_factor, orf_name, bleed_over, COUNT(*) as count, 
					TRUNCATE(avg(orig_intensity_value), 3) as avg_orig_intensity_value, 
					TRUNCATE(avg(rc_intensity_value), 3) as avg_rc_intensity_value,
					TRUNCATE(avg(ptp_intensity_value), 3) as avg_ptp_intensity_value, TRUNCATE(avg(ptp_intensity_value), 3) as avg_ptp_intensity_value, 
					TRUNCATE(avg(z_score), 3) as avg_zscore 
					FROM $this->interactionTable
					WHERE (transcriptor_factor = '$transcriptor' 
					    OR orf_name = '$transcriptor') 
					    AND call_type = 'Positive' 
					    AND z_score >= $zScore
					    AND user_id = '$userId'
					    AND project_id = '$projectId'
					group by plate_name, array_coord, transcriptor_factor, orf_name, bleed_over;";
			
			return $this->makeQuery($str);
		}
		/*
		 * function makeQuery
		 * 
		 * takes a sql string and executes the query
		 * returns false if the query is invalid or num_rows == 0
		 * else append the $row result to $data array.
		 * 
		 * @access : private
		 * @param: $string (SQL query)
		 * @return : array (Objects)
		 * 
		 */
		private function makeQuery($string){
			
			$data = array();
			if( !$this->Config_Model->checkLogin() ) {return array();}
			$query = $this->db->query($string);
			
			if(!$query || $query->num_rows() == 0){
				return false;
			}
			
			foreach($query->result() as $row){
				
					if(count($row) == 0){
						continue;
					}
					$data[] = $row;
			}
			
			return $data;
		}
		
}

?>
