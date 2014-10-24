<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed\n');

/*
 * class written by Justin Nelson
 * October 4th, 2011
 * 
 * Class Project Control handles the ajax request for changing the project
 * The input will be a new user_id and it will output a list of projects associated
 * with that user.
 * 
 * 
 */
class Send_Mail extends CI_Controller{

	/*
	 * default constructor
	 * loads the Autocomplete_Model class to access the database
	 */
	public function __construct(){
		parent::__construct();
	}
	public function index(){
		$this->load->view('contact');
	}
	 
    public function died($error) {
        // your error code can go here
        echo "We are very sorry, but there were error(s) found with the form you submitted. ";
        echo "These errors appear below.<br /><br />";
        echo $error."<br /><br />";
        echo "Please go back and fix these errors.<br /><br />";
        die();
    }
    
    public function clean_string($string) {
		$bad = array("content-type","bcc:","to:","cc:","href");
		return str_replace($bad,"",$string);
	}
			
	public function sendMail(){
		$this->input->post('user_id');
		
		
		$first_name = $this->input->post('fname');
		$last_name  = $this->input->post('lname');
		$email_from = $this->input->post('email');
		$comments   = $this->input->post('comment');
		
		// EDIT THE 2 LINES BELOW AS REQUIRED
		$email_to = "alos.diallo@umassmed.edu";
		//$email_to = "nels6685@umn.edu";
		$email_subject = "REPORT: SPOT-ON: " . $first_name . "_" . $last_name;
		
		$error_message = "";
		$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
		if(!preg_match($email_exp,$email_from)) {
			$error_message .= 'The Email Address you entered does not appear to be valid.<br />';
		}
		$string_exp = "/^[A-Za-z .'-]+$/";
		if(!preg_match($string_exp,$first_name)) {
			$error_message .= 'The First Name you entered does not appear to be valid.<br />';
		}
		if(!preg_match($string_exp,$last_name)) {
			$error_message .= 'The Last Name you entered does not appear to be valid.<br />';
		}
		if(strlen($comments) < 2) {
			$error_message .= 'The Comments you entered do not appear to be valid.<br />';
		}
		if(strlen($error_message) > 0) {
			$this->died($error_message);
		}
		
		$email_message = "Form details below.\n\n";
		$email_message .= "First Name: ".$this->clean_string($first_name)."\n";
		$email_message .= "Last Name: ".$this->clean_string($last_name)."\n";
		$email_message .= "Email: ".$this->clean_string($email_from)."\n";
		$email_message .= "Comments: ".$this->clean_string($comments)."\n";
		
		
		// create email headers
		$headers = 'From: '.$email_from."\r\n".
		'Reply-To: '.$email_from."\r\n" .
		'X-Mailer: PHP/' . phpversion();
		@mail($email_to, $email_subject, $email_message, $headers); 
		
		echo "Success~!";
	}
	
	
	
}

?>
