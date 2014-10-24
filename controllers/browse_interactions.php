<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once("interaction.php");

class Browse_Interactions extends CI_Controller{

	private $data;
	private $transcriptor;
	private $promoter;
	private $pictures;
	
	public function __construct(){
		parent::__construct();
		$this->data = array();
		$this->pictures = array();
		$this->transcriptor = "";
		$this->promoter = "";
	}
	
	public function index(){
		$this->load->model('Data_Model');
		
		/*
		***
		* These two calls get the transcriptor tags and the promotor
		* tags from the data model to be loaded into the view, data.php
		**/
		$tags['transcriptorTags'] = $this->Data_Model->getTranTags();
		$tags['promotorTags'] = $this->Data_Model->getPromTags();
		
		/*
		***
		* Keeps track of if a query has been made but failed.
		**/
		$tags['queryFail'] = false;
		
		/*
		***
		* These next few lines retrieve the data associated with
		* each plateset and loads it into the view results.php
		**/
		$this->setValues($this->input->post('transcriptor'), $this->input->post('promoter'));
	
		/*
		*** $this->data
		* contains ALL of the data related to the time courses including
		* the pictures, the data is structured as such:
		*	Array (
		*		[0] => stdClass Object (
		*			[idbait] => EA_A02
		*			[bait_gene] => Y57A10A.27
		*			[ixn_gene] => 3-F7
		*			[bait_preycoord] => "EAA02,03-F07"
		*			[bait_preyorf] => "EAA02,C27C12.6"
		*			[positive] => yes
		*			[redudant] => 1
		*			[prey_molecule] => C27C12.6
		*			[prey_gene] => dmd-4
		*			[prey_variant] => C27C12.6
		*			[dbd] => ZF - DM
		*			[published_ixn] => 4W
		*			[paper] =>
		*			[pictures] => Array (
		*				[0] => EA_A02_N_1-4_5mM_Xgal_7d_W.JPG
		*				[1] => EA_A02_N_5-8_5mM_Xgal_7d_W.JPG
		*				[2] => EA_A02_N_9-12_5mM_Xgal_7d_W.JPG 
		* 			) 
		* 			[interaction_matrix] => [Large 2D Array]
		*		)
		*		[1] => stdClass Object (
		*			[.]
		*			[.]
		*			[.] 
		*		[.] => stdClass Object ([...])
		*		[.] => stdClass Object ([...])
		*		[.] => stdClass Object ([...])
		*		[n] => stdClass Object ([...])
		*	)		 
		**/
		
		/*
		***
		* Should a query be made? if promotor and transcriptor are both
		* undefined (returned false on post) then it shouldn't be made.
		**/
		if($this->getPromoter() || $this->getTranscriptor())
		{
			$this->data = $this->Data_Model->getData($this->getTranscriptor(), $this->getPromoter());
			$this->Data_Model->setPictures($this->data);
			//$this->setInteractions($this->data);
			$loadData['data'] = $this->data;
			
			/*
			***
			* Set up Error Messages
			**/
			
			/*
			***
			* Checks to see if a query has been made and was successful, 
			* if not it will set up query fail error message.
			**/
			if(!($this->data))
			{
				$tags['queryFail'] = true;
			}
			
			/*
			***
			* Set up last minute data manipulations
			**/
			
			if($this->data && $this->getTranscriptor())
			{
				$loadData['position'] = $this->Data_Model->parseCoordinates($this->data[0]->bait_preycoord);
			} else {
				$loadData['position'] = null;
			}
			/*
			 * sets up a list of transcriptors
			 */
			$loadData['transMatrix'] = $this->Data_Model->getTranscriptorMatrix();
		}
		$this->load->view('browse_interactions.php', $tags);
		if($this->data != false)
		{
			$this->load->view('view_interactions.php', $loadData);
			//$this->load->view('results.php', $loadData);
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		/*
		$this->load->view('browse_interactions.php', $tags);
		$this->load->model('Interaction_Model');
		
		
		$this->setValues($this->input->post('transcriptor'), $this->input->post('promoter'));
		$this->data = $this->Interaction_Model->getData($this->getTranscriptor(), $this->getPromoter());
		$platename = $this->getPlatename($this->data);
		$path = $this->formatPlatename($platename);
		$this->pictures = $this->Interaction_Model->getPictures($path);

		//print_r($this->pictures);
		if(count($this->data) != 0){
			$info['data'] = $this->data;
			$info['pictures'] = $this->pictures;
			
			$this->load->view('view_interactions.php', $info);
		}
		*/
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function formatPlatename($platename){
		
		$path = array();
		foreach($platename as $plate){
			
			$temp = preg_split("/_/", $plate);
			$img = $temp[0] . "_" . $temp[1] . "_N_" . $temp[2];
			array_push($path, $img);
		}
		
		return $path;
	}
	public function getJSON(){
		echo json_encode($this->input->post('transcriptor'));
	}
	
	public function getPlateName($platename){
		$plates = array();
		
		foreach($platename as $plate){
			array_push($plates, $plate->plate_name);
		}

		return $plates;
	}
	
	public function setValues($transcriptor, $promoter){
		$this->setPromoter($promoter);
		$this->setTranscriptor($transcriptor);
	}
	
	public function setPromoter($promoter){
		if(($promoter == "enter your keywords here") || ($promoter == "")){
			$this->promoter = "";
		}
		else{
			$this->promoter = $promoter;
		} 
	}
	
	public function setTranscriptor($transcriptor){
		if(($transcriptor == "enter your keywords here") || ($transcriptor == "")){
			$this->transcriptor = "";
		}
		else{
			$this->transcriptor = $transcriptor;
		} 
	}
	
	public function getPromoter(){
		return $this->promoter;
	}
	
	public function getTranscriptor(){
		return $this->transcriptor;
	}
}
