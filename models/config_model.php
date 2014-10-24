<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Config_Model extends CI_Model{

	/*
	 * Table names to be used
	 */
	private $promoterTable      = "Promoter";
	private $transcriptionTable = "TranscriptorFactor";
	private $interactionTable   = "Interactions";
	private $imageTable         = "images";
	
	public function __construct(){
		parent::__construct();
	}
	
	public function getPromoterTable()     { return $this->promoterTable      ;}
	public function getTranscriptionTable(){ return $this->transcriptionTable ;}
	public function getInteractionTable()  { return $this->interactionTable   ;}
	public function getImageTable()        { return $this->imageTable         ;}
	
	public function checkLogin() {
		if(!$this->session->userdata('is_logged_in')) {redirect('login');}
		return True;
	}
	
}
