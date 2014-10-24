<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	
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
		$this->load->model('Admin_Model');
		$this->load->helper('url');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}
	public function index(){
		if($this->session->userdata('admin') != 1){redirect("");}
		$this->load->view('admin.php');
	}
	
	public function getTranscriptionFactorLists(){
		if( !$this->Config_Model->checkLogin() ) {return array();}
		$query = $this->db->query("SELECT DISTINCT list FROM TranscriptorFactor;");
		
		$lists = array();

		if($query) {
			if($query->num_rows() > 0){
				foreach($query->result() as $row){
					$lists[] = $row->list;			
				}
			}
		}
		echo json_encode($lists);
	}
	
	
	
	
	
	/*
	locateFavoriteGene
	
	input:	favoriteGene from Post
			permission level
	output:	Array with locations (type: bait or prey, user, project)
	
	SELECT user_id, project_id, metausers, metaprojects, permission, tf_list FROM Projects;
	
	SELECT user_id, project_id FROM Projects WHERE tf_list = 'Y1H';
	
	SELECT user_id, project_id, metausers, metaprojects, permission, tf_list FROM Projects WHERE permission >= '1' AND (metausers LIKE '%jnelson%' OR user_id = 'jnelson') AND (metaprojects LIKE '%project1%' OR project_id = 'project1') limit 50;


	*/
	public function locateFavoriteGene(){
	
		//echo "success";
		$favoriteGene = mysql_real_escape_string($this->input->post('favoriteGene'));
		$permission = mysql_real_escape_string($this->session->userdata('admin'));
		
		$preyResults = $this->arrayUnique($this->locateFavoriteGene_PreyQuery($favoriteGene, $permission));
		$baitResults = $this->arrayUnique($this->locateFavoriteGene_BaitQuery($favoriteGene, $permission));
		
		echo json_encode(array_merge($preyResults, $baitResults));

		
	}
	
	
	
	
	public function arrayUnique($array){
		$resultArray = array();
		foreach($array as $arr){
			$unique = true;
			foreach($resultArray as $rarr){
				if($arr == $rarr){
					$unique = false;
				}
			}
			
			if($unique){
				$resultArray[] = $arr;
			}
		}
		return $resultArray;
	}
	
	
	
	/*
	locateFavoriteGene_BaitQuery
	
	Check to see if the favoriteGene is a BAIT and if it is retrieve the projects
	
	input:	favoriteGene
			permission
	output:	array consisting of (type = "BAIT", user, project) which contain favorite gene and is permissable
	
	*/
	public function	locateFavoriteGene_BaitQuery($favoriteGene, $permission){
		//Initialize
		$results = array();
		//End Initialize 
		
		$baitQuery = $this->db->query("SELECT DISTINCT user_id, project_id FROM Promoter WHERE bait_id = '$favoriteGene' OR bait_name = '$favoriteGene' OR bait_name2 = '$favoriteGene' OR bait_name3 = '$favoriteGene'");
		
		if($baitQuery) {
			if($baitQuery->num_rows() > 0){
				//Loop through
				foreach($baitQuery->result() as $row){
					$user = mysql_real_escape_string($row->user_id);
					$project = mysql_real_escape_string($row->project_id);
					
					$results = array_merge($results, $this->locateFavoriteGene_MetaprojectsQuery($user, $project, $permission));
				}
			} else {}//The query returned no results
		} else {} //There was no query, what happened here?
		
		// Before returning the results, set each result to have type of "BAIT"
		foreach($results as $result){
			$result->type = "BAIT";
		} // DONE setting results to "BAIT"
		
		// Now Make sure the results are unique
		//$results = array_unique($results);
		
		// Return the results
		return $results;
		
	}
	
	/*
	locateFavoriteGene_PreyQuery
	
	Check to see if the favoriteGene is a PREY and if it is retrieve the projects
	
	input:	favoriteGene
			permission
	output:	array consisting of (type = "PREY", user, project) which contain favorite gene and is permissable
	*/
	public function	locateFavoriteGene_PreyQuery($favoriteGene, $permission){
		//Initialize
		$results = array();
		//End Initialize 
		
		$preyQuery = $this->db->query("SELECT DISTINCT list FROM TranscriptorFactor WHERE orf_name = '$favoriteGene' OR orf_name2 = '$favoriteGene' OR wb_gene = '$favoriteGene' OR common_name = '$favoriteGene' OR info = '$favoriteGene'");
		
		if($preyQuery) {
			if($preyQuery->num_rows() > 0){
				//Loop through
				foreach($preyQuery->result() as $row){
					$list = mysql_real_escape_string($row->list);
					
					$results = array_merge($results, $this->locateFavoriteGene_ProjectListQuery($list, $permission));
					
				}
			} else {} //The query returned no results
		} else {} //There was no query, what happened here?

		// Before returning the results, set each result to have type of "PREY"
		foreach($results as $result){
			$result->type = "PREY";
		} // DONE setting results to "PREY"
		
		//print_r($results);
		// Now Make sure the results are unique
		//$results = array_unique((array)$results);
		
		// Return the results
		return $results;
	}
	
	/*
	locateFavoriteGene_ProjectListQuery
	
	Grab the projects taking into account metaprojects which have a certain list
	
	input:	listname
			permission
	output:	array consisting of (user, project) which has list name, including metaprojects
	*/
	public function locateFavoriteGene_ProjectListQuery($list, $permission){
		//Initialize
		$results = array();
		//End Initialize 
	
		$projectQuery = $this->db->query("SELECT user_id, project_id FROM Projects WHERE tf_list = '$list'");
		
		if($projectQuery) {
			if($projectQuery->num_rows() > 0){
				//Loop through
				foreach($projectQuery->result() as $row){
					$user = mysql_real_escape_string($row->user_id);
					$project = mysql_real_escape_string($row->project_id);
					
					$results = array_merge($results, $this->locateFavoriteGene_MetaprojectsQuery($user, $project, $permission));
				}
			} else {} //The query returned no results
		} else {} //There was no query, what happened here?
		
				

		return $results;
	}
	
	/*
	locateFavoriteGene_MetaprojectsQuery
	
	grab all metaprojects associated with a user and project
	
	input:	user
			project
			permission
	output:	array consisting of (user, project) for all projects, including metaprojects, which are permissible
	*/
	public function locateFavoriteGene_MetaprojectsQuery($user, $project, $permission){
		//Initialize
		$results = array();
		$loop_count = 0;
		//End Initialize
	
		$metaprojectsQuery = $this->db->query("SELECT user_id, project_id FROM Projects WHERE permission >= '$permission' AND (metausers LIKE '%$user%' OR user_id = '$user') AND (metaprojects LIKE '%$project%' OR project_id = '$project')");
		
		if($metaprojectsQuery) {
			if($metaprojectsQuery->num_rows() > 0){
				//Loop through
				foreach($metaprojectsQuery->result() as $row){
					$results[$loop_count]->user = $row->user_id;
					$results[$loop_count]->project = $row->project_id;
					$loop_count++;
				}
			} else {} //The query returned no results
		} else {} //There was no query, what happened here?
		
		return $results;
	}
	/**/
	///////
	// Adds new users to the database.
	///////
	public function addNewUser(){
		//if($this->session->userdata('admin') != 1){return 0;}
		//checks if all the forms are valid
		$this->form_validation->set_rules('firstname', 'First Name'           , 'required|valid_name'    );
		$this->form_validation->set_rules('lastname' , 'Last Name'            , 'required|valid_name'    );
		$this->form_validation->set_rules('username' , 'Username'             , 'required|valid_username');
		$this->form_validation->set_rules('password' , 'Password'             , 'required|min_length[6]' );
		$this->form_validation->set_rules('passconf' , 'Password Confirmation', 'required|min_length[6]' );
		$this->form_validation->set_rules('email'    , 'Email'                , 'required|valid_email'   );
		
		//if not, reload the main page
		if ($this->form_validation->run() == FALSE){
			$this->load->view('register.php');
		}
		
		// otherwise, stores all the information into the database
		// this-input-post takes the values from the page, dony by the framework
		else{
			
			$data = array(  'firstname' =>  $this->input->post('firstname'),
							'lastname'  =>  $this->input->post('lastname'),
							'password'  =>  $this->input->post('password'),
							'passconf'  =>  $this->input->post('passconf'),
							'email'     =>  $this->input->post('email'),
							'username'  =>  $this->input->post('username'));
			//if the user was sucessfully created
			// go the main page
			// it should create the user session and show it as logged in
			//print_r($data);			
			if($this->Admin_Model->insertUser($data, TRUE)){
				$this->load->view('register_success');
			}
			//otherwise, reload the page
			else{
				$this->load->view('register.php');
			}
		}
	}
	
	public function deleteUser(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		echo $this->Admin_Model->deleteUser($userName);
	}
	public function addProject(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		$projectName = $this->input->post('projectname');
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		echo $this->Admin_Model->insertProject($userName, $projectName);
	}
	
	public function deleteProject(){
		if($this->session->userdata('admin') != 1){return 0;}
		$projectName = $this->input->post('projectname');
		
		// Clean it up
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		
		echo $this->Admin_Model->deleteProject($projectName);
		
		
	}

	public function addPublication(){
		if($this->session->userdata('admin') != 1){return 0;}
		// Grab from post
		$project_id = $this->input->post('projectName');
		$title_id = addslashes($this->input->post('titleName'));
		$author_id = addslashes($this->input->post('authorName'));
		$abstract_id = addslashes($this->input->post('abstractName'));
		$paper_id = addslashes($this->input->post('paperName'));
		$data_id = addslashes($this->input->post('dataName'));
		$year_id = addslashes($this->input->post('yearName'));

		//Clean it up
		$project_id = str_replace($this->forbidCharacters, "", $project_id);
		
		$str = "";
		if($title_id){$str .= "<br>". $this->Admin_Model->insertPublicationTitle($project_id, $title_id);}
		if($author_id){$str .= "<br>". $this->Admin_Model->insertPublicationAuthor($project_id, $author_id);}
		if($abstract_id){$str .= "<br>". $this->Admin_Model->insertPublicationAbstract($project_id, $abstract_id);}
		if($paper_id){$str .= "<br>". $this->Admin_Model->insertPublicationPaper($project_id, $paper_id);}
		if($data_id){$str .= "<br>". $this->Admin_Model->insertPublicationData($project_id, $data_id);}
		if($year_id){$str .= "<br>". $this->Admin_Model->insertPublicationYear($project_id, $year_id);}

		// Do it and echo result.
		echo $str;
	}
	public function removePublication(){}
	public function deletePromoterData(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		$projectName = $this->input->post('projectname');
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		
		//echo $userName . " " . $projectName;
		echo $this->Admin_Model->deletePromoterData($userName, $projectName);
	}
	public function deleteInteractionData(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		$projectName = $this->input->post('projectname');
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		
		//echo $userName . " " . $projectName;
		echo $this->Admin_Model->deleteInteractionData($userName, $projectName);
	}
	
	public function deleteData(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		$projectName = $this->input->post('projectname');
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		
		//echo $userName . " " . $projectName;
		echo $this->Admin_Model->deleteData($userName, $projectName);
	}
	public function addImage(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		$projectName = $this->input->post('projectname');
		$imageName = $this->input->post('imagename');
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		
		//echo $userName . " " . $projectName;
		echo $this->Admin_Model->addImage($imageName, $userName, $projectName);
	}
	public function addImageByUser(){
		$userName = $this->input->post('username');
		$projectName = $this->input->post('projectname');
		$imageName = $this->input->post('imagename');
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		
		//echo $userName . " " . $projectName;
		echo $this->Admin_Model->addImage($imageName, $userName, $projectName);
	}
	
	public function backupTables(){
		if($this->session->userdata('admin') != 1){return 0;}
		
		echo $this->Admin_Model->backupTables();
	}
	public function addInteractionToTable(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		$projectName = $this->input->post('projectname');
		$interactionName = $this->input->post('interactionname');
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		
		echo $this->Admin_Model->addInteractionToTable($interactionName, $userName, $projectName);
	}
	public function addPromoterToTable(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		$projectName = $this->input->post('projectname');
		$promoterName = $this->input->post('promotername');
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		
		echo $this->Admin_Model->addPromoterToTable($promoterName, $userName, $projectName);
	}
	public function addTranscriptionToTable(){
		if($this->session->userdata('admin') != 1){return 0;}
		$transcriptionFactorName = $this->input->post('transcriptionFactorName');
		$transcriptionFactorList = $this->input->post('transcriptionFactorList');

		echo $this->Admin_Model->addTranscriptionToTable($transcriptionFactorName, $transcriptionFactorList);
	}
	
	
	
	public function renameProject(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		$oldProjectName = $this->input->post('oldprojectname');
		$newProjectName = $this->input->post('newprojectname');
		
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		$oldProjectName = str_replace($this->forbidCharacters, "", $oldProjectName);
		$newProjectName = str_replace($this->forbidCharacters, "", $newProjectName);
		
		echo $this->Admin_Model->renameProject($userName, $oldProjectName, $newProjectName);
	}
	
	public function changeUserPermission(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		$newValue = $this->input->post('value');
		
		$userName = str_replace($this->forbidCharacters, "", $userName);
		
		echo $this->Admin_Model->changeUserPermission($userName, $newValue);
	}
	public function changeProjectPermission(){
		if($this->session->userdata('admin') != 1){return 0;}
		$projectName = $this->input->post('projectname');
		$userName = $this->input->post('username');
		$newValue = $this->input->post('value');
		
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		$userName = str_replace($this->forbidCharacters, "", $userName);
		
		echo $this->Admin_Model->changeProjectPermission($userName, $projectName, $newValue);
	}
	
	public function changeMetaProjects(){
		if($this->session->userdata('admin') != 1){return 0;}
		$project = $this->input->post('project');
		$user = $this->input->post('user');
		$metaprojects = $this->input->post('metaprojects');
		$metausers = $this->input->post('metausers');
		
		$project = str_replace($this->forbidCharacters, "", $project);
		$user = str_replace($this->forbidCharacters, "", $user);
		
		echo $this->Admin_Model->changeMetaProjects($user, $project, $metausers, $metaprojects);
	}
	
	public function deleteImageTable(){
		if($this->session->userdata('admin') != 1){return 0;}
		$project = $this->input->post('project');
		$user = $this->input->post('user');
		$image = $this->input->post('baitImageName');
		
		$project = str_replace($this->forbidCharacters, "", $project);
		$user = str_replace($this->forbidCharacters, "", $user);
		
		echo $this->Admin_Model->deleteImageTable($user, $project, $image);
		
	}
	
	public function deleteSingleBait(){
		if($this->session->userdata('admin') != 1){return 0;}
		$project = $this->input->post('project');
		$user = $this->input->post('user');
		$bait = $this->input->post('bait');
		
		$project = str_replace($this->forbidCharacters, "", $project);
		$user = str_replace($this->forbidCharacters, "", $user);
		
		echo $this->Admin_Model->deleteSingleBait($user, $project, $bait);
	}
	
	public function attachListToProject(){
		if($this->session->userdata('admin') != 1){return 0;}
		
		$user = $this->input->post('user');
		$project = $this->input->post('project');
		$list = $this->input->post('list');
		
		$user = str_replace($this->forbidCharacters, "", $user);
		$project = str_replace($this->forbidCharacters, "", $project);
		
		
		echo $this->Admin_Model->attachListToProject($user, $project, $list);
	}
	
	
	/*
	 * function index
	 * @param: none
	 * @return: void
	 * @action: checks if the forms are valid, otherwise reloads the page again
	 * 			if the values are correct, sends them to the register model, which 
	 * 			sanitizes them. If everything is correct, it stores the user information
	 * 			into the table `users`, creating a new user 
	 */
	/*public function index(){
		//checks if all the forms are valid
		$this->form_validation->set_rules('firstname', 'First Name'           , 'required|valid_name'    );
		$this->form_validation->set_rules('lastname' , 'Last Name'            , 'required|valid_name'    );
		$this->form_validation->set_rules('username' , 'Username'             , 'required|valid_username');
		$this->form_validation->set_rules('password' , 'Password'             , 'required|min_length[6]' );
		$this->form_validation->set_rules('passconf' , 'Password Confirmation', 'required|min_length[6]' );
		$this->form_validation->set_rules('email'    , 'Email'                , 'required|valid_email'   );
		
		//if not, reload the main page
		if ($this->form_validation->run() == FALSE){
			$this->load->view('register.php');
		}
		// otherwise, stores all the information into the database
		// this-input-post takes the values from the page, dony by the framework
		else{
			
			$data = array(  'firstname' =>  $this->input->post('firstname'),
							'lastname'  =>  $this->input->post('lastname'),
							'password'  =>  $this->input->post('password'),
							'passconf'  =>  $this->input->post('passconf'),
							'email'     =>  $this->input->post('email'),
							'username'  =>  $this->input->post('username'));
			//if the user was sucessfully created
			// go the main page
			// it should create the user session and show it as logged in			
			if($this->Register_Model->insertUser($data)){
				$this->load->view('register_success');
			}
			//otherwise, reload the page
			else{
				$this->load->view('register.php');
			}
		}
	}*/
	
	/*
	 * creates a new user, given the data received from the forms
	 * and sets auto_login to true
	 * @access: private
	 * @param: data: array of data passed by the user
	 * 			auto_login always set up to true
	 * @return: boolean
	 */
	/*
	private function create($data, $auto_login = TRUE){
		
			//makes the insert operation, returns the $user_id
			$user_id = $this->Register_Model->setData($data);
			
			//something went wrong
			if($user_id == 0){
				return false;
			}
			
			if($auto_login){
				//destroys the old session
				$this->session->sess_destroy();
				
				//creates a fresh brand new session
				$this->session->sess_create();
				
				//Set session data
				$this->session->set_userdata(array('id' => $user_id, 'username' => $data['username']));
				
				//set logged_in to true
				$this->session->set_userdata(array('logged_in' => true));
			}
			//login as successful
			return true;
	}
	*/

	public function addRawProjectToInteractionTable(){
		if($this->session->userdata('admin') != 1){return 0;}
		$userName = $this->input->post('username');
		$projectName = $this->input->post('projectname');
		$interactionName = $this->input->post('interactionname');
		
		// Clean it up
		$userName = str_replace($this->forbidCharacters, "", $userName);
		$projectName = str_replace($this->forbidCharacters, "", $projectName);
		
		echo $this->Admin_Model->addInteractionToTable($interactionName, $userName, $projectName);
	}
}