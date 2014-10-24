<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class rawProjectAdmin extends CI_Controller {
	
	private $forbidCharacters = array("/", "'", '"');
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}
	public function index(){
		if($this->session->userdata('admin') != 1){redirect("");
	}
	
	public function addRawProjectToProduction(){
		if($this->session->userdata('admin') != 1){return 0;}
		$user = $this->input->post('username');
		$project = $this->input->post('projectname');
		$rawProject = $this->input->post('rawProject');
		
		// Clean it up
		$user = str_replace($this->forbidCharacters, "", $user);
		$project = str_replace($this->forbidCharacters, "", $project);
		
		if($user == "" || $project == "" || $rawProject == ""){
			echo "ERROR";
			return 0;
		}

		// Alright, I have all of the stuff that I need to do this. Let's do this then!
		// First let's upload to the Interactions table, <project>/dataFiles/output.list.txt in standard format goes in.
		$str = "";
		if($this->db->query("LOAD DATA LOCAL INFILE '/heap/UMassProject/raw_images/".$rawProject."dataFiles/output.list.txt' INTO TABLE Interactions FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' (plate_name, plate_number, array_coord, bait_sequence_name, bait_gene_promoter, y_coord, x_coord, orig_intensity_value, rc_intensity_value, ptp_intensity_value, z_score, z_prime, plate_median, call_type, bleed_over)")){
			$str .= "Interaction table has been updated with file ".$rawProject." <br>";
		}
		if($this->db->query("UPDATE Interactions SET modified_call = call_type WHERE user_id IS NULL")){
			$str .= "Modified calls Set<br>";
		}
		if($this->db->query("UPDATE Interactions SET user_id = '".$user."', project_id = '".$project."' WHERE user_id IS NULL")){
			$str .= "raw Project: ".$rawProject."userId: ".$user." , projectId: ".$project." , SET! Upload Complete <br>";
		}
		
		// It's done
		
		// Ok now let's grab those images and copy them into the image folder.
		$exclude_list = array(".", "..");
		$imageDirPath = "/heap/UMassProject/raw_images/".$rawProject;
		$imageFiles = array_diff(scandir($imageDirPath."/images/"), $exclude_list);

		foreach ($imageFile as $imageName) {
    		if(strtoupper(substr(strrchr($imageName),'.'),1)) == "PNG"){
    			// We have a png file, put it into the image table then copy it over
    			// put it in the table
				if($this->db->insert('images', array('image'=> $imageName, 'user_id' => $user, 'project_id' => $project))){
					$str .= "Database addition of ".$imageName." was successful!<br>"
				} else {
					$str .= "Database addition of ".$imageName." was failure!<br>"
				}

				// copy it over
				if(copy($imageDirPath."/images/".$imageName, "/heap/UMassProject/images/".$imageName)){
					$str .= "Copy of ".$imageName." was successful!<br>";
				} else {
					$str .= "Copy of ".$imageName." was failure!<br>";
				}
    		}	
    	}

		echo $str;
	}

	public function addRawProjectToPromoter(){
		if($this->session->userdata('admin') != 1){return 0;}
		$user = $this->input->post('username');
		$project = $this->input->post('projectname');
		$rawProject = $this->input->post('rawProject');
		
		$str = "";
		
		if($this->db->query("LOAD DATA LOCAL INFILE '/heap/UMassProject/raw_images/".$rawProject."dataFiles/promoter.txt' INTO TABLE Promoter FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' (bait_id, bait_name, bait_name2, bait_name3, background_score)")){
			$str .= "Promoter table has been updated with file ".$rawProject." <br>";
		}
		if($this->db->query("UPDATE Promoter SET user_id = '".$user."', project_id = '".$project."' WHERE user_id IS NULL")){
			$str .= "userId: ".$user." , projectId: ".$project." , SET!<br>";
		}
		
		return $str;
	}

}
