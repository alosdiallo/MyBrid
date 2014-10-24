<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Picture{
	
	private $data;
	
	public function Picture($loadData){
			$this->data = array();
			$this->load($loadData);
	}
	
	public function load($loadData){
		
		if($loadData == ''){
			return $this->data;
		}
		
		$this->data['picture'] = $loadData['picture'];
		//$this->data['interaction_matrix'] = $loadData['interaction_matrix'];
		$this->data['coordinate'] = $loadData['coordinate'];
		$this->data['bait_gene'] = $loadData['bait_gene'];
		$this->data['prey_gene'] = $loadData['prey_gene'];
		$this->data['bait_id'] = $loadData['bait_id'];
		$this->data['prey_molecule'] = $loadData['prey_molecule'];
		$this->data['prey_variant'] = $loadData['prey_variant'];
		$this->data['dbd'] = $loadData['dbd'];
		$this->data['published_ixn'] = $loadData['published_ixn'];
		$this->data['bait_preycoord'] = $loadData['bait_preycoord'];
		$this->data['bait_preyorf'] = $loadData['bait_preyorf'];
		
		return $this->data;
	}
	
	public function getData(){
		return $this->data;
	}
	
}
