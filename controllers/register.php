<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * class written by Philippe Ribeiro
 * in June 14th, 2011
 * 
 * class Register allows a new user to register itself, in other
 * to access the database
 * 
 * this is the controller for the class (MVC architecture)
 * 
 */
class Register extends CI_Controller {

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
		$this->load->model('Register_Model');
		$this->load->helper('url');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
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
	public function index(){
		//if(!$this->session->userdata('admin')){redirect("");}
		
		//checks if all the forms are valid
		$this->form_validation->set_rules('firstname', 'Name', 'required|valid_name');
		$this->form_validation->set_rules('lastname', 'Name', 'required|valid_name');
		$this->form_validation->set_rules('username', 'Username', 'required|valid_username');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
		$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|min_length[6]');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		
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
			if($this->create($data, TRUE)){
				$this->load->view('register_success');
			}
			//otherwise, reload the page
			else{
				$this->load->view('register.php');
			}
		}
	}
	
	/*
	 * creates a new user, given the data received from the forms
	 * and sets auto_login to true
	 * @access: private
	 * @param: data: array of data passed by the user
	 * 			auto_login always set up to true
	 * @return: boolean
	 */
	private function create($data, $auto_login = TRUE){
			//if($this->session->userdata('admin') != 1){return 0;}
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
}
