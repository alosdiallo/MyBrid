<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Model extends CI_Model{
	
	private $promoterTable = "";
	private $transcriptionTable = "";
	private $interactionTable = "";
	private $imageTable = "";
	public function __construct(){
		parent::__construct();
		$this->load->model('Config_Model');
		$this->promoterTable      = $this->Config_Model->getPromoterTable();
		$this->transcriptionTable = $this->Config_Model->getTranscriptionTable();
		$this->interactionTable   = $this->Config_Model->getInteractionTable();
		$this->imageTable         = $this->Config_Model->getImageTable();
	}
	
	public function insertUser($data){
		//if($this->session->userdata('admin') != 1){return 0;}
		//Sets up a fail if nothing happens
		$user_id = 0;
		
		//checks if the user already exists
		$query = $this->db->get_where('users', array('username' => $data['username']));
		
		if($query->num_rows > 0){
			return $user_id;
		}

		// Check for valid inputs
		if(strcmp($data['password'], $data['passconf']) != 0){return 0;}
		if(strcmp($data['firstname'], "") == 0)              {return 0;}
		if(strcmp($data['lastname'], "") == 0)               {return 0;}
		if(strcmp($data['email'], "") == 0)                  {return 0;}
		if(strcmp($data['username'], "") == 0)               {return 0;}

		// Set everything up into an array
		$data = array(	'id' => $this->db->insert_id(),
						'name' => $data['firstname'] ." ". $data['lastname'],
						'username' => $data['username'],
						'password' => md5($data['password']),
						'email' => $data['email'],
						'date' => time(),
						'admin' => 3);
						
		// Insert a new user into the database
		if($this->db->insert('users', $data)){
			$user_id = $this->db->insert_id();
		}
		return $user_id;
	}
	
	public function deleteUser($userId){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$query = $this->db->get_where($this->promoterTable, array('user_id' => $userId));
		if($query->num_rows > 0){return "There is still promoter data present!";}
		$query = $this->db->get_where($this->interactionTable, array('user_id' => $userId));
		if($query->num_rows > 0){return "There is still interaction data present!";}
		$query = $this->db->get_where('Projects', array('user_id' => $userId));
		if($query->num_rows > 0){return "There is still project data present!";}
		
		$this->db->delete('users', array('username' => $userId));
		return "You have successfully deleted ". $userId;
		//$this->db->delete('Projects', array('user_id' => $userId));
	}
	
	public function insertProject($userId, $projectId){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$query = $this->db->get_where('Projects', array('user_id' => $userId, 'project_id' => $projectId));
		if($query->num_rows > 0){return "Project already Present!";}
		
		$this->db->insert('Projects', array('user_id' => $userId, 'project_id' => $projectId));
		return "You have successfully inserted $projectId as a new project belonging to user $userId"; 
	}
	
	public function deleteProject($projectId){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$query = $this->db->get_where($this->promoterTable, array('project_id' => $projectId));
		if($query->num_rows > 0){return "There is still promoter data present!";}
		$query = $this->db->get_where($this->interactionTable, array('project_id' => $projectId));
		if($query->num_rows > 0){return "There is still interaction data present!";}
		
		$this->db->delete('Projects', array('project_id' => $projectId));
		return "You have successfully deleted ". $projectId;
		
	}
	
	public function deleteData($userId, $projectId){
		if($this->session->userdata('admin') != 1){return 0;}	
		$this->db->delete($this->promoterTable, array('project_id' => $projectId, 'user_id' => $userId));
		$this->db->delete($this->interactionTable, array('project_id' => $projectId, 'user_id' => $userId));
		return "You have successfully deleted $projectId data belonging to user $userId"; 
	}
	public function deletePromoterData($userId, $projectId){
		if($this->session->userdata('admin') != 1){return 0;}	
		$this->db->delete($this->promoterTable, array('project_id' => $projectId, 'user_id' => $userId));
		return "You have successfully deleted $projectId data belonging to user $userId from Promoter Table"; 
	}
	public function deleteInteractionData($userId, $projectId){
		if($this->session->userdata('admin') != 1){return 0;}	
		$this->db->delete($this->interactionTable, array('project_id' => $projectId, 'user_id' => $userId));
		return "You have successfully deleted $projectId data belonging to user $userId From Interaction Table"; 
	}
	public function changeUserPermissions(){}
	
	public function addImage($imageName, $userId, $projectId){
		if($this->session->userdata('admin') != 1){return 0;}
		$query = $this->db->get_where('images', array('image'=> $imageName, 'user_id' => $userId, 'project_id' => $projectId));
		if($query->num_rows > 0){return "There is already that image present in the table!";}
		//echo $query;
		
		
		$this->db->insert('images', array('image'=> $imageName, 'user_id' => $userId, 'project_id' => $projectId));
		return "You have successfully added $imageName to table belonging to user $userId in project $projectId";
	}
	
	public function addImageByUser($imageName, $userId, $projectId){
		if($this->session->userdata('admin') != 1){return 0;}
		$this->db->insert('images', array('image'=> $imageName, 'user_id' => $userId, 'project_id' => $projectId));
		return "You have successfully added $imageName to table belonging to user $userId in project $projectId";
	}
	
	public function insertPublication($projectId, $publicationId){
		if($this->session->userdata('admin') != 1){return 0;}
		
		//$this->db->where('project_id', $projectId);
		$this->db->update('Projects', array('publication'=> $publicationId), array('project_id'=> $projectId ) );
		return "You have successfully attached $publicationId as a publication to project $projectId";
	}
	public function deletePublication(){}
	
	public function insertPublicationTitle($projectId, $title){
		if($this->session->userdata('admin') != 1){return 0;}

		$this->db->update('Projects', array('title'=> $title), array('project_id'=> $projectId ) );
		return "You have successfully attached $title as a title to project $projectId";
	}
	public function insertPublicationAuthor($projectId, $author){
		if($this->session->userdata('admin') != 1){return 0;}

		$this->db->update('Projects', array('authors'=> $author), array('project_id'=> $projectId ) );
		return "You have successfully attached $author as a author to project $projectId";
	}
	public function insertPublicationAbstract($projectId, $abstract){
		if($this->session->userdata('admin') != 1){return 0;}

		$this->db->update('Projects', array('abstract'=> $abstract), array('project_id'=> $projectId ) );
		return "You have successfully attached $abstract as an abstract to project $projectId";
	}
	public function insertPublicationPaper($projectId, $paper){
		if($this->session->userdata('admin') != 1){return 0;}

		$this->db->update('Projects', array('paper'=> $paper), array('project_id'=> $projectId ) );
		return "You have successfully attached $paper as a paper to project $projectId";
	}
	public function insertPublicationData($projectId, $data){
		if($this->session->userdata('admin') != 1){return 0;}

		$this->db->update('Projects', array('data'=> $data), array('project_id'=> $projectId ) );
		return "You have successfully attached $data as a data to project $projectId";
	}
	public function insertPublicationYear($projectId, $year){
		if($this->session->userdata('admin') != 1){return 0;}

		$this->db->update('Projects', array('year'=> $year), array('project_id'=> $projectId ) );
		return "You have successfully attached $year as a year to project $projectId";
	}
	public function backupTables(){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$str = "";
		if($this->db->query("DROP TABLE INT_BU")){
			$str .= "Interaction Table Backup Dropped <br>";
		}
		if($this->db->query("DROP TABLE PROM_BU")){
			$str .= "Promoter Table Backup Dropped <br>";
		}
		if($this->db->query("DROP TABLE IMG_BU")){
			$str .= "Image Table Backup Dropped <br>";
		}
		if($this->db->query("CREATE TABLE INT_BU SELECT * FROM Interactions")){
			$str .= "Interaction Table Backup Created <br>";
		}
		if($this->db->query("CREATE TABLE PROM_BU SELECT * FROM Promoter")){
			$str .= "Promoter Table Backup Created <br>";
		}
		if($this->db->query("CREATE TABLE IMG_BU SELECT * FROM images")){
			$str .= "Image Table Backup Created <br>";
		}
		return $str;
	}
	public function addInteractionToTable($interactionId, $userId, $projectId){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$str = "";
		
		if($this->db->query("LOAD DATA LOCAL INFILE '/heap/UMassProject/publication/".$interactionId."' INTO TABLE Interactions FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' (plate_name, plate_number, array_coord, bait_sequence_name, bait_gene_promoter, y_coord, x_coord, orig_intensity_value, rc_intensity_value, ptp_intensity_value, z_score, z_prime, plate_median, call_type, bleed_over)")){
			$str .= "Interaction table has been updated with file ".$interactionId." <br>";
		}
		if($this->db->query("UPDATE Interactions SET modified_call = call_type WHERE user_id IS NULL")){
			$str .= "Modified calls Set<br>";
		}
		if($this->db->query("UPDATE Interactions SET user_id = '".$userId."', project_id = '".$projectId."' WHERE user_id IS NULL")){
			$str .= "userId: ".$userId." , projectId: ".$projectId." , SET! Upload Complete <br>";
		}
		
			
		return $str;
	}
	
	public function addPromoterToTable($promoterId, $userId, $projectId){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$str = "";
		
		if($this->db->query("LOAD DATA LOCAL INFILE '/heap/UMassProject/publication/".$promoterId."' INTO TABLE Promoter FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' (bait_id, bait_name, bait_name2, bait_name3, background_score)")){
			$str .= "Promoter table has been updated with file ".$promoterId." <br>";
		}
		if($this->db->query("UPDATE Promoter SET user_id = '".$userId."', project_id = '".$projectId."' WHERE user_id IS NULL")){
			$str .= "userId: ".$userId." , projectId: ".$projectId." , SET!<br>";
		}
		
		return $str;
	}
	
	public function addTranscriptionToTable($fileName, $list){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$str = "";
		
		if($this->db->query("LOAD DATA LOCAL INFILE '/heap/UMassProject/publication/$fileName' INTO TABLE TranscriptorFactor FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' (coordinate, orf_name, orf_name2, wb_gene, common_name, info, info2, alt_name, note, x_coord, y_coord,plate_number)")){
			if($this->db->query("UPDATE TranscriptorFactor SET list = '$list' WHERE list IS NULL")){
				return "SUCCESS!";
			} else {
				return "LIST NAME NOT ATTACHED TO PROJECT CORRECTLY! CONTACT JUSTIN";
			}
		} else {
			return "FAILURE!";
		}
	}
	
	

	public function renameProject($userName, $oldName, $newName){
		if($this->session->userdata('admin') != 1){return 0;}
		$str = "";
		
		if($this->db->query("UPDATE Projects SET project_id = '".$newName."' WHERE user_id = '".$userName."' AND project_id = '".$oldName."'")){
			$str .= "$oldName has been updated to $newName in Projects Table<br>";
		}
		if($this->db->query("UPDATE Interactions SET project_id = '".$newName."' WHERE user_id = '".$userName."' AND project_id = '".$oldName."'")){
			$str .= "$oldName has been updated to $newName in Interactions Table<br>";
		}
		if($this->db->query("UPDATE Promoter SET project_id = '".$newName."' WHERE user_id = '".$userName."' AND project_id = '".$oldName."'")){
			$str .= "$oldName has been updated to $newName in Promoter Table<br>";
		}
		if($this->db->query("UPDATE images SET project_id = '".$newName."' WHERE user_id = '".$userName."' AND project_id = '".$oldName."'")){
			$str .= "$oldName has been updated to $newName in images Table<br>";
		}
		
		return $str;
	}
	
	public function changeUserPermission($userName, $newValue){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$this->db->update('users', array('admin'=> $newValue), array('username'=> $userName ) );
		
		return "User $userName now has permission level $newValue";
	}
	
	public function changeProjectPermission($userName, $projectName, $newValue){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$this->db->update('Projects', array('permission'=> $newValue), array('user_id'=> $userName, 'project_id' => $projectName ) );
		
		return "User $userName and project $projectName now has permission level $newValue";
	}
	
	public function changeMetaProjects($user, $project, $metausers, $metaprojects){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$this->db->update('Projects', array('metausers'=> $metausers, 'metaprojects' => $metaprojects), array('user_id'=> $user, 'project_id' => $project ) );
		
		return "User $user project $project has been assigned $metausers and $metaprojects";
	}
	
	public function deleteImageTable($user, $project, $image){
		if($this->session->userdata('admin') != 1){return 0;}
		if($image != ""){
			$this->db->query("DELETE FROM images WHERE image LIKE '".$image."\_%' AND project_id = '$project' AND user_id = '$user'");
		}
		
		return "User $user project $project images starting with $image have been deleted";
	}
	
	public function deleteSingleBait($user, $project, $bait){
		if($this->session->userdata('admin') != 1){return 0;}
		$this->db->query("DELETE FROM Interactions WHERE plate_name = '$bait' AND project_id = '$project' AND user_id = '$user'");
		
		return "User $user project $project baits $bait have been deleted from the interaction table";
	}
	
	public function attachListToProject($user, $project, $list){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$this->db->query("UPDATE Projects SET tf_list = '$list' WHERE user_id = '$user' AND project_id = '$project'");
		
		return "User $user project $project lists $list have been updated the project table";
	}

	public function addRawProjectToInteractionTable($interactionId, $userId, $projectId){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$str = "";
		
		if($this->db->query("LOAD DATA LOCAL INFILE '/heap/UMassProject/publication/".$interactionId."' INTO TABLE Interactions FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' (plate_name, plate_number, array_coord, bait_sequence_name, bait_gene_promoter, y_coord, x_coord, orig_intensity_value, rc_intensity_value, ptp_intensity_value, z_score, z_prime, plate_median, call_type, bleed_over)")){
			$str .= "Interaction table has been updated with file ".$interactionId." <br>";
		}
		if($this->db->query("UPDATE Interactions SET modified_call = call_type WHERE user_id IS NULL")){
			$str .= "Modified calls Set<br>";
		}
		if($this->db->query("UPDATE Interactions SET user_id = '".$userId."', project_id = '".$projectId."' WHERE user_id IS NULL")){
			$str .= "userId: ".$userId." , projectId: ".$projectId." , SET! Upload Complete <br>";
		}
		
			
		return $str;
	}

}
?>
