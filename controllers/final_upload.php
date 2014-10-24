<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Final_Upload extends CI_Controller {
	
	private $forbidCharacters = array("/", "'", '"');
	
	/*
	 * default contructor, loads the modules necessaries 
	 * in this class.
	 * @args: Register_Model is the Model of Register, which acess the database
	 * @args: helper url - used from the framework to sanitize the 
	 * 			arguments passed by the user
	 * @form_validation: actually checks whether all the forms were valid
	 */
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}
	public function index(){
		if(!$this->session->userdata('is_logged_in')){redirect('');}
		if($this->session->userdata('admin') > 2){redirect('');}
		$this->load->view('final_upload.php');
	}
	
	/*
	$file:	File to be made safe, the file will be appended with ".safe.txt" at the end of it
	$user:	User to compare
	$project:	Project to compare
	$table:	Table to check during comparison
	$tableComparer:	field in table to compare during comparison
	$fileComparerNumber:	tab index in file after explode to use in comparison
	*/
	public function createSafeToTableFile($file, $user, $project, $table, $tableComparer, $fieldComparerNumber){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		if(!file_exists($file)){return 0;}
		
		$inputHandle = fopen($file, "r");
		$outputHandle = fopen($file.".safe.txt", "w");
		
		
		
		$order = array("\r\n", "\r");
		$replace = '\n';
		
		$duplicates = array();
		$nonduplicates = array();
		
		if($inputHandle){
			while(($line = fgets($inputHandle)) !== false){
				$fields = explode("\t", $line);
				$fileComparer = $fields[$fieldComparerNumber];
				//Did I already find it to be a nonduplicate? if so just go ahead and write
				if(in_array($fileComparer, $nonduplicates)){
					fwrite($outputHandle, str_replace($order, $replace, $line)); // Note new line conversion
				} else {
					// Did I already find it to be a duplicate? If so then skip the check, it's a duplicate and has already been reported
					if(!in_array($fileComparer, $duplicates)){
						$query = $this->db->query("SELECT * FROM $table WHERE user_id = '$user' AND project_id = '$project' AND $tableComparer = '$fileComparer'");
						
						if($query->num_rows() == 0){
							fwrite($outputHandle, str_replace($order, $replace, $line)); // Note new line conversion
							$nonduplicates[] = $fileComparer; // Make sure to update the nonduplicates
						} else {
							echo $fileComparer." is a duplicate entry<br>";
							$duplicates[] = $fileComparer; // Make sure to update the duplicates
						}
					}
				}	
			}
		}
		fclose($inputHandle);
		fclose($outputHandle);
		
		return $file.".safe.txt";
	}
	
	public function addRawProjectToProduction(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		$user = $this->input->post('user');
		if($this->session->userdata('admin') != 1 && $this->session->userdata('username') != $user){echo "YOU DO NOT HAVE PERMISSION TO UPLOAD INTO THIS PROJECT, MAKE SURE YOU OWN THE PROJECT"; return 0;}
		
		$project = $this->input->post('project');
		$rawProject = $this->input->post('rawProject');
		
		// Clean it up
		$user = str_replace($this->forbidCharacters, "", $user);
		$project = str_replace($this->forbidCharacters, "", $project);
		
		if($user == "" || $project == "" || $rawProject == ""){
			echo "ERROR";
			return 0;
		}

		$fileToDatabase = "/heap/UMassProject/raw_images/".$rawProject."/dataFiles/output.list.txt";
		$fileToDatabase_safe = $this->createSafeToTableFile($fileToDatabase, $user, $project, "Interactions", "plate_name", 0);
		
		// Alright, I have all of the stuff that I need to do this. Let's do this then!
		// First let's upload to the Interactions table, <project>/dataFiles/output.list.txt in standard format goes in.
		
		if($fileToDatabase_safe){
			if($this->db->query("LOAD DATA LOCAL INFILE '".$fileToDatabase_safe."' INTO TABLE Interactions FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' (plate_name, plate_number, array_coord, bait_sequence_name, bait_gene_promoter, y_coord, x_coord, orig_intensity_value, rc_intensity_value, ptp_intensity_value, z_score, z_prime, plate_median, call_type, bleed_over)")){
				echo "Interaction table has been updated with file ".$rawProject." <br>";
			}
			if($this->db->query("UPDATE Interactions SET modified_call = call_type WHERE user_id IS NULL")){
				echo "Modified calls Set<br>";
			}
			if($this->db->query("UPDATE Interactions SET user_id = '".$user."', project_id = '".$project."' WHERE user_id IS NULL")){
				echo "raw Project: ".$rawProject."userId: ".$user." , projectId: ".$project." , SET! Upload Complete <br>";
			}
		
			//
			// It's done
			
			// Ok now let's grab those images and copy them into the image folder.
			$exclude_list = array(".", "..");
			$imageDirPath = "/heap/UMassProject/raw_images/".$rawProject;
			$imageFiles = array_diff(scandir($imageDirPath."/images/"), $exclude_list);

			foreach ($imageFiles as $imageName) {
				if(strtoupper(substr(strrchr($imageName,'.'),1)) == "PNG"){
					// We have a png file, put it into the image table then copy it over
					// put it in the table
					if($this->db->insert('images', array('image'=> $imageName, 'user_id' => $user, 'project_id' => $project))){
						echo "Database addition of ".$imageName." was successful!<br>";
					} else {
						echo "Database addition of ".$imageName." was failure!<br>";
					}

					// copy it over
					if(copy($imageDirPath."/images/".$imageName, "/heap/UMassProject/images/".$imageName)){
						echo "Copy of ".$imageName." was successful!<br>";
					} else {
						echo "Copy of ".$imageName." was failure!<br>";
					}
				}	
			}
		} else {
			echo "File was found unsafe to upload into database. Probably because it does not exist.<br>";
		}
	}

	
	public function addRawProjectToPromoter(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') != 1 && $this->session->userdata('username') != $user){return 0;}
		if(!$this->session->userdata('is_logged_in')) {redirect("");}
		$user = $this->input->post('user');
		$project = $this->input->post('project');
		$rawProject = $this->input->post('rawProject');
		
		$fileToDatabase = "/heap/UMassProject/raw_images/".$rawProject."/dataFiles/promoter.txt";
		$fileToDatabase_safe = $this->createSafeToTableFile($fileToDatabase, $user, $project, "Promoter", "bait_id", 0);
		
		if($fileToDatabase_safe){
			if($this->db->query("LOAD DATA LOCAL INFILE '".$fileToDatabase_safe."' INTO TABLE Promoter FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' (bait_id, bait_name, bait_name2, bait_name3, background_score)")){
				echo "Promoter table has been updated with file ".$rawProject." <br>";
			}
			if($this->db->query("UPDATE Promoter SET user_id = '".$user."', project_id = '".$project."' WHERE user_id IS NULL")){
				echo "userId: ".$user." , projectId: ".$project." , SET!<br>";
			}
		} else {
			echo "File was found unsafe to upload into database. Probably because it does not exist.<br>";
		}
	}

	public function runThumbnailGenerator(){
		if(!$this->session->userdata('is_logged_in')) {echo "LOGGED OUT"; return 0;}
		if($this->session->userdata('admin') > 2){echo "LOW PERMISSION"; return 0;}
		$execute_str = "sh /heap/UMassProject/images/convert.sh 2>&1 &";
		
		$success = shell_exec($execute_str);
		//if($success){echo "SUCCESS";}
		echo json_encode($success);
		//echo $success;
	}
}
