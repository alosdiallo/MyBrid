<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed\n');
       
/*
 * class Downloads
 * 
 * Allows the page to dynamically generate a new interaction matrix file
 * whenever the user makes a request
 * 
 * written by Philippe Ribeiro, August 27th, 2011
 * 
 */
class Downloads extends CI_Controller{

	//
	private $file;
	private $url;
	private $time;
	
	public function __construct(){
		
		parent::__construct();
		$this->load->model('Download_Model');
		$this->time = date('Y-m-d_H:i:s');
		$this->file = '/heap/UMassProject/downloads/avg_data' . $this->time . '.csv';
		
	}
	
	public function downloadFile(){
		
		$data = $this->input->post('data');
		$promoter = $this->input->post('promoter');
		
		//print_r($data);
		if(!$data){ 
			echo "No data"; 
			return;
		}
		$headers = array(0 => "array_coord", 1 => "common_name", 2 => "orf_name", 3 => "wb_name", 4 => "ave_ptp_intensity", 5 => "ave_z_prime");
		
		///Create the file!
		$fp = fopen($this->file, 'w');
		
		$plate_number = $promoter['plate_number'] + 1;
		
		fwrite($fp, "bait_id='".$promoter['bait_id']."' bait_alt_names='".$promoter['bait_name'].", ".$promoter['bait_name2'].", ".$promoter['bait_name3']."' plate_number=".$plate_number."\n");
		fputcsv($fp, $headers, chr(44));
		//writes the intermatrix to the file in .csv format
		foreach($data as $line){
				//print_r($line);
				fputcsv($fp, $line, chr(44));
		}
		
		chmod($this->file, 0777);
		fclose($fp);
		/// END CREATE THE FILE

		if(file_exists($this->file)){
			$this->url = 'http://franklin-umh.cs.umn.edu/UMassProject/downloads/avg_data' . $this->time . '.csv';
			echo json_encode($this->url);
		}
		else{
			echo json_encode(false);
		}
	}
	
	public function downloadSequenceFile(){
		$bait = $this->input->post('bait');
		$bait_genename = $this->input->post('bait_genename');
		$bait_altname = $this->input->post('bait_altname');
		
		if(!$bait){ 
			echo "No data"; 
			return;
		}
		$SEQUENCE_FILE_NAME = "/heap/UMassProject/publication/baits_new.txt";
		$OUTPUT_FILE = '/heap/UMassProject/downloads/seq_data_' . $bait . '.csv';
		$file_input = fopen($SEQUENCE_FILE_NAME, "r");
		$file_output = fopen($OUTPUT_FILE, "w");
		
		if ($file_input){
			$success = false;
			while($line = fgets($file_input)){
				$line_explode = explode("\t", $line);
				
				if(strToUpper($line_explode[0]) == strToUpper($bait)){
					//print_r($line_explode);
					$success = true;
					fwrite($file_output, "Bait Id = $bait, Bait Gene Name = $bait_genename, Alternate Bait Name = $bait_altname\n");
					fwrite($file_output, $line_explode[2]);
					fwrite($file_output, $line_explode[1]);
				}
			}
		}
		
		fclose($file_input);
		
		chmod($OUTPUT_FILE, 0777);
		fclose($file_output);
		
		if(!$success){
			echo "No data";
			return;
		}
		
		if(file_exists($OUTPUT_FILE)){
			$url = 'http://franklin-umh.cs.umn.edu/UMassProject/downloads/seq_data_' . $bait . '.csv';
			echo json_encode($url);
		} else {
			echo json_encode(false);
		}
	}
}
?>
