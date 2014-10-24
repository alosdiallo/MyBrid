<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Output_Model extends CI_Model{

	private $interactionTable;
	private $promoterTable      = "";
	private $transcriptionTable = "";
	private $interactionTable   = "";
	private $imageTable         = "";
	public function __construct(){
		parent::__construct();
		$this->load->model('Config_Model');
		$this->promoterTable      = $this->Config_Model->getInteractionTable();
		$this->transcriptionTable = $this->Config_Model->getInteractionTable();
		$this->interactionTable   = $this->Config_Model->getInteractionTable();
		$this->imageTable         = $this->Config_Model->getInteractionTable();
	}
	
	public function outputCSV(){}
	
	public function makeCSV(){
		$str = "SELECT * FROM $this->interactionTable ORDER BY plate_name, orf_name";
		
		$data = array();
		/*
		 * Make sure the user is logged in to access the database
		 */
		if( !$this->Config_Model->checkLogin() ) {return array();}
		$query = $this->db->query($string);
		
		if(!$query)
		{
			return false;
		}
		else{
			if($query->num_rows() > 0){
				//make CSV here
				$outstream = fopen("php://output", 'w');	
				
				$currentOrf; // Keeps track of the current orf
				//default values
				
				foreach($query->result() as $row){
					//Is this a new set of data?
					if($row->orf_name != $currentOrf) {
						//Are the numberpositive good enough for threshold?
						if($numberPositive > 1) {
							//find averages
							$ave_origIntensity = $total_origIntensity / $numberPositive;
							$ave_rcIntensity   = $total_rcIntensity   / $numberPositive;
							$ave_zScore        = $total_zScore        / $numberPositive;
							//Create array
							$vals = array(0=>$plateName, 
							              1=>$transcriptorFactor,
							              2=>$orfName,
							              3=>$arrayCoord,
							              4=>$numberPositive,
							              5=>$ave_origIntensity,
							              6=>$ave_rcIntensity,
							              7=>$ave_zScore,
							              8=>$bleedOver)
							//Print values to file
							fputcsv($outstream, $vals); // add parameters if you want
						}
						//Reset values
						$total_origIntensity = 0;
						$total_rcIntensity = 0;
						$total_zScore = 0;
						$numberPositive = 0;
						//Set currentOrf to orf_name
						$currentOrf = $row->orf_name;
					}
					//Is this a positive value?
					if($row->modified_call == "Positive"){
							$numberPositive++;
							$total_origIntensity += $row->orig_intensity_value;
							$total_rcIntensity   += $row->rc_intensity_value
							$total_zScore        += $row->z_score
							if($row->bleed_over == "BO") { $bleedOver = TRUE;}
					} else if ($row->modified_call == ""){
						if($row->call_type == "Positive"){
							$numberPositive++;
							$total_origIntensity += $row->orig_intensity_value;
							$total_rcIntensity   += $row->rc_intensity_value
							$total_zScore        += $row->z_score
							if($row->bleed_over == "BO") { $bleedOver = TRUE;}
						}
					}
					
					//retrieve general information
					$plateName          = $row->plate_name          ;
					$arrayCoord         = $row->array_coord         ;
					$transcriptorFactor = $row->transcriptor_factor ;
					$orfName            = $row->orf_name            ;
				}
				
				fclose($outstream);
				
			} else {
				return false;
			}
		}


	
	}


}

