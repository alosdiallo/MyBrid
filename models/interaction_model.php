<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * class Interaction_Model, implements the database access to 
 * the controller browse_interactions
 * 
 * written by Philippe Ribeiro
 * Department of Computer Science, University of Minnesota
 * Minneapolis, Minnesota, USA - 55455
 * 
 * June 29th, 2011
 * 
 *//*
class Interaction_Model extends CI_Model {

	//holds the data retrieved from the database
	private $data;
	
	/*
	 * default constructor, calls the superclass constructor
	 * and initializes data as an array
	 *//*
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
		$this->data = array();
	}
	
	/*
	 * this function is called from the controller
	 * given the transcriptor factor and the promoter
	 * retrieves the data associates with those values
	 * 
	 * @access : public
	 * @param: string, string
	 * @return : array
	 *//*
	public function getData($transcriptor, $promoter){
			
			//only transcriptor factor
			if(($transcriptor != "") && ($promoter == "")){
				$this->data = $this->getDataTranscriptorFactor($transcriptor);
			}
			//only promoters
			else if((strcmp($transcriptor, "") == 0) && (strcmp($promoter, "") != 0)){
				$this->data = $this->getDataPromoter($promoter);
			}
			else if((strcmp($transcriptor, "") != 0) && (strcmp($promoter, "") != 0)){
				$this->data = $this->getDataBoth($transcriptor, $promoter);
			}
			else{
				$this->data = array();
			}
			
			return $this->data;
		}
		/*
		 * in case the user selected both
		 * them search for values associates with both
		 * 
		 * @access : public
		 * @param : string, string
		 * @return : array
		 *//*
		public function getDataBoth($transcriptor, $promoter){
			//sets the query search
			$str = "SELECT * FROM $this->interactionTable WHERE bait_sequence='$promoter' OR bait_name='$transcriptor' AND gene_name='$transcriptor' OR orf='$transcriptor'" ;
			//class the function make Query
			return $this->makeQuery($str);
		}
		/*
		 * in case the user only selected transcriptor factors
		 * search for data associates with that transcriptor factor
		 * 
		 * @access : public
		 * @param : string
		 * @return : array
		 * 
		 *//*
		public function getDataTranscriptorFactor($transcriptor){
			$str = "SELECT * FROM $this->interactionTable WHERE gene_name ='$transcriptor' OR orf = '$transcriptor'";
			
			return $this->makeQuery($str);
		}
		/*
		 * in case the user only selected promoters
		 * search for the data associates with that promoter
		 * 
		 * @access : public
		 * @param : string
		 * @return : array
		 *//*
		public function getDataPromoter($promoter){
			$str = "SELECT * FROM $this->interactionTable WHERE bait_sequence ='$promoter' OR bait_name='$promoter'";
			
			return $this->makeQuery($str);
		}
		
		/*
		 * function makeQuery, executes the SQL query that is 
		 * passed in by the argument and returns the results obtained
		 * from that query
		 * 
		 * @access : public
		 * @param : string
		 * @return : array
		 *//*
		public function makeQuery($string){
			if( !$this->Config_Model->checkLogin() ) {return array();}
			$query = $this->db->query($string);
			
			if(!$query){
				return $this->data;
			}
			else{
				
				if($query->num_rows() > 0){
					
					foreach($query->result() as $row){
						$this->data[] = $row;
					}
				}
			}
			
			return $this->data;
		}
		/*
		 * function getPictures gets all the pictures associated 
		 * with the values passed into the array @platename
		 * for the interactions results, there must have only one
		 * picture associated with that image
		 * 
		 * @access : public
		 * @param : array
		 * @return : array
		 * 
		 *//*
		public function getPictures($platename){
			
			$pictures = array();
			$platename = array_unique($platename);
			if( !$this->Config_Model->checkLogin() ) {return array();}
			foreach($platename as $plate){
				$str = "SELECT * FROM $this->imageTable WHERE image LIKE '$plate%'";
				
				$query = $this->db->query($str);
				
				if(!$query){
					return false;
				}
				//for the this case, it should return only one row
				//there may be a source of bugs, once that there may have more 
				//results for different queries
				else{
					if($query->num_rows() > 0){
						$pictures[] = $query->row();
					}
				}
			}
			//returns the pictures
			return $pictures;
		}
	
}*/
