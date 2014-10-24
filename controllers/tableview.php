<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * main controller of the website
 * defines the main Class, UMassProject
 * 
 * class written by Philippe Ribeiro
 * June 24th, 2011
 */
class TableView extends CI_Controller {
	
	private $time;
	private $file;
	private $header = array('Bait ID', 'Bait Gene Name', 'Bait Alternate Name', 'Bait Family', 'Prey Array Coordinate', 'Prey Alternate Name', 'Prey Gene Name', 'Prey Family', 'Number of Positive Colonies', 'Average Raw Normalized Intensity', 'Average Row Column Normalized Intensity', 'Average Bait to Bait Normalized Intensity', 'Average Z-score', 'Average Z-Prime', 'User', 'Project', 'List Name');
	
	
	private $loaddata;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('Table_Model');
		$this->load->model('Data_Model');
		
		$this->time = date('Y-m-d_H:i:s');
		$this->file = '/heap/UMassProject/downloads/table' . $this->time . '.csv';
	}
	
	private function createFile($data){
		
		$fp = fopen($this->file, 'w');
		//print_r($data);
		fputcsv($fp, $this->header);		
		//writes the intermatrix to the file in .csv format
		//fputcsv($fp, $data, chr(44));
		
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
		if(!$this->session->userdata('is_logged_in')) {redirect('login');}
		$this->load->view('tableview.php');
	}
	
	public function getTable(){
		$queryData['interactionSearch']   = true;
		$queryData['promoterSearch']      = true;
	
		$queryData['arrayCoord']          = stripslashes($this->input->post('array_coord'));
		
		$queryData['zScore_min']          = stripslashes($this->input->post('z_score_min'));
		$queryData['zScore_max']          = stripslashes($this->input->post('z_score_max'));
		
		$queryData['zPrime_min']          = stripslashes($this->input->post('z_prime_min'));
		$queryData['zPrime_max']          = stripslashes($this->input->post('z_prime_max'));
		
		$queryData['origIntensity_min']   = stripslashes($this->input->post('orig_intensity_min'));
		$queryData['origIntensity_max']   = stripslashes($this->input->post('orig_intensity_max'));
		
		$queryData['rcIntensity_min']     = stripslashes($this->input->post('rc_intensity_min'));
		$queryData['rcIntensity_max']     = stripslashes($this->input->post('rc_intensity_max'));
		
		$queryData['ptpIntensity_min']    = stripslashes($this->input->post('ptp_intensity_min'));
		$queryData['ptpIntensity_max']    = stripslashes($this->input->post('ptp_intensity_max'));
		
		$queryData['positive']            = stripslashes($this->input->post('positive'));
		$queryData['bleedover']           = stripslashes($this->input->post('bleedover'));
		
		// Grab metaproject and project information
		$project_info = $this->Data_Model->getMetaProjects($this->input->post('user_id'), $this->input->post('project_id'));
		
		if($project_info['users'] && $project_info['projects']){
			$users = $project_info['users'];
			$projects = $project_info['projects'];
		} else {
			$users = array(0 => "NULL");
			$projects = array(0 => "NULL");
		}
		
		// Setup Bait and Prey for multiexplode and IN statement
		$pattern = '/,(\s*)/'; 
		
		$transcriptors = trim(preg_replace($pattern, '","', stripslashes($this->input->post('transcriptor'))), '","');
		$promoters     = trim(preg_replace($pattern, '","', stripslashes($this->input->post('promoter'))), '","');
		
		if($transcriptors){$transcriptors = '"'.$transcriptors.'"';}
		if($promoters){$queryData['promoter'] = '"'.$promoters.'"';} // Set up the promoter search woohoo!
		
		
		
		
		
		// Initialize for the loop.
		$data = array();
		$user_count = 0;
		// Loop through the projects and grab the promoter data then join the data with the old data
		foreach($users as $user){
			$queryData['userId'] = $users[$user_count];
			$queryData['projectId'] = $projects[$user_count];
			
			// START SET UP TRANSCRIPTION FACTOR SEARCH
			if($transcriptors){
				$transcriptionData = $this->Table_Model->getTranscriptionFactorData($transcriptors, $queryData['userId'], $queryData['projectId']);
				
				if(isset($transcriptionData[0])){
					$first = true;
					foreach($transcriptionData as $data){
						if($first){
							$str = '"'.$data->coordinate.'"';
							$first = false;
						} else {
							$str .= ','.'"'.$data->coordinate.'"';
						}
					}
					$queryData['transcriptionFactor'] = $str;
				}
			}
			// END SET UP TRANSCRIPTION FACTOR SEARCH
			
			
			$to_be_merged_data = $this->Table_Model->getPromoterData($queryData);
			if($to_be_merged_data){
				$data = array_merge($data, $to_be_merged_data);
			}
			$user_count++;
		}
	
		if($data != false){
			echo json_encode($data);
		} else{
			echo json_encode(false);
		}
		
	}
	
	public function downloadTable(){
		$queryData['interactionSearch']   = true;
		$queryData['promoterSearch']      = true;
	
		$queryData['arrayCoord']          = stripslashes($this->input->post('array_coord'));
		
		$queryData['zScore_min']          = stripslashes($this->input->post('z_score_min'));
		$queryData['zScore_max']          = stripslashes($this->input->post('z_score_max'));
		
		$queryData['zPrime_min']          = stripslashes($this->input->post('z_prime_min'));
		$queryData['zPrime_max']          = stripslashes($this->input->post('z_prime_max'));
		
		$queryData['origIntensity_min']   = stripslashes($this->input->post('orig_intensity_min'));
		$queryData['origIntensity_max']   = stripslashes($this->input->post('orig_intensity_max'));
		
		$queryData['rcIntensity_min']     = stripslashes($this->input->post('rc_intensity_min'));
		$queryData['rcIntensity_max']     = stripslashes($this->input->post('rc_intensity_max'));
		
		$queryData['ptpIntensity_min']    = stripslashes($this->input->post('ptp_intensity_min'));
		$queryData['ptpIntensity_max']    = stripslashes($this->input->post('ptp_intensity_max'));
		
		$queryData['positive']            = stripslashes($this->input->post('positive'));
		$queryData['bleedover']           = stripslashes($this->input->post('bleedover'));
		
		// Grab metaproject and project information
		$project_info = $this->Data_Model->getMetaProjects($this->input->post('user_id'), $this->input->post('project_id'));
		
		if($project_info['users'] && $project_info['projects']){
			$users = $project_info['users'];
			$projects = $project_info['projects'];
		} else {
			$users = array(0 => "NULL");
			$projects = array(0 => "NULL");
		}
		
		// Setup Bait and Prey for multiexplode and IN statement
		$pattern = '/,(\s*)/'; 
		
		$transcriptors = trim(preg_replace($pattern, '","', stripslashes($this->input->post('transcriptor'))), '","');
		$promoters     = trim(preg_replace($pattern, '","', stripslashes($this->input->post('promoter'))), '","');
		
		if($transcriptors){$transcriptors = '"'.$transcriptors.'"';}
		if($promoters){$queryData['promoter'] = '"'.$promoters.'"';} // Set up the promoter search woohoo!
		
		
		
		
		
		// Initialize for the loop.
		$data = array();
		$user_count = 0;
		// Loop through the projects and grab the promoter data then join the data with the old data
		foreach($users as $user){
			$queryData['userId'] = $users[$user_count];
			$queryData['projectId'] = $projects[$user_count];
			
			// START SET UP TRANSCRIPTION FACTOR SEARCH
			if($transcriptors){
				$transcriptionData = $this->Table_Model->getTranscriptionFactorData($transcriptors, $queryData['userId'], $queryData['projectId']);
				
				if(isset($transcriptionData[0])){
					$first = true;
					foreach($transcriptionData as $data){
						if($first){
							$str = '"'.$data->coordinate.'"';
							$first = false;
						} else {
							$str .= ','.'"'.$data->coordinate.'"';
						}
					}
					$queryData['transcriptionFactor'] = $str;
				}
			}
			// END SET UP TRANSCRIPTION FACTOR SEARCH
			
			
			$to_be_merged_data = $this->Table_Model->getPromoterData($queryData);
			if($to_be_merged_data){
				$data = array_merge($data, $to_be_merged_data);
			}
			$user_count++;
		}
	
		$this->createFile($data);
		
		if(file_exists($this->file)){
			$url = 'http://franklin-umh.cs.umn.edu/UMassProject/downloads/table' . $this->time . '.csv';
			echo json_encode($url);
		}
		else{
			echo json_encode(false);
		}
		
	}
	
}
