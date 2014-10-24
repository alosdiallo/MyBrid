<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * class written by Philippe Ribeiro
 * on June 24th, 2011
 * 
 * The class Controller Login 
 * 
 */
class Login extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		
		//session_start();
		$this->load->helper('url');
		$this->load->helper(array('form', 'url'));
		$this->load->model('Login_Model');
		
		$this->base_url = base_url();
	}
	
	public function index(){
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('username', 'Username', 'required|valid_username');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
		/*
		 * in case that the form validation is not true
		 * reload the page again
		 */
		if ($this->form_validation->run() == FALSE){
			$this->load->view('login.php');
		}
		else{
			//if the user is already logged in
			if($this->session->userdata('is_logged_in')){
				redirect('umassproject');
			}
			//otherwise, make the login if that user was a valid one
			else{
				$res = $this->Login_Model->checkLogin($this->input->post('username'), $this->input->post('password'));
				//var_dump($res);
				if($res != false){
					//person has an account
					$session_data = array('username' => $this->input->post('username'), 'is_logged_in' => TRUE, 'admin' => $res->admin, 'user_mem' => 'jnelson', 'project_mem' => 'project1');
					$this->session->set_userdata($session_data);


					// Check to see if reviewer is being logged into and if so send an e-mail to alos.
					if($this->input->post('username') == 'Reviewer1'){
						$email_to = "alos.diallo@umassmed.edu";
						$email_from = "alos.diallo@umassmed.edu";
						$email_subject = "Reviewer account";
						$email_message = "The reviewer account has been logged into";
		
						// create email headers
						$headers = 'From: '.$email_from."\r\n".
							'Reply-To: '.$email_from."\r\n" .
							'X-Mailer: PHP/' . phpversion();
						@mail($email_to, $email_subject, $email_message, $headers); 
					}
					redirect('umassproject');
				} else {
					$this->load->view('login.php');
				}
			}
		}
	}
	
	/*
	 * Delete user
	 * @access public
	 * @param integer
	 * @return bool
	 * 
	 */
	public function delete($user_id){
			
			//checks if it was a valid user id
			if(!is_numeric($user_id)){
				//there was a problem
				return false;
			}
			else if($this->db->delete($this->users_table, array('id' => $user_id))){
				//database call was successful
				return true;
			}
			else{
				//there was a problem
				return false;
			}
	}

	public function sendMail(){
		$email_to = "alos.diallo@umassmed.edu";
		$email_from = "alos.diallo@umassmed.edu";
		$email_subject = "Reviewer account";
		$email_message = "The reviewer account has been logged into";
		
		// create email headers
		$headers = 'From: '.$email_from."\r\n".
		'Reply-To: '.$email_from."\r\n" .
		'X-Mailer: PHP/' . phpversion();
		@mail($email_to, $email_subject, $email_message, $headers); 
	}

	/*
	 * Logout user
	 * @acess public 
	 * @return void
	 * 
	 */
	public function logout(){
		//destroy sessions
		//print_r($this->session->userdata);
		unset($this->session->userdata);
		$this->session->sess_destroy();
		redirect($this->base_url);
	}
}
