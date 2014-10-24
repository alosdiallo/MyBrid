<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Controller of the class Upload
 * handles the upload operation, uses codeigniter 
 * helper form to set up most of the forms
 * 
 * class written by Philippe Ribeiro
 * June 24th, 2010
 */
class Upload extends CI_Controller {

	/*
	 * constructor calls the CI_Controller parent
	 * loads the helper form and url
	 * starts a session and if the user is not
	 * logged in, redirect to the login page
	 */
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		
		if($this->session->userdata('is_logged_in') == false){
			redirect('login');
		}
		
	}

	/*
	 * function index, loads the upload page
	 * sets error to ''
	 * @access: public
	 * @arguments: void
	 * @return: void
	 */
	public function index(){
		$this->load->model('Config_Model');
		$this->Config_Model->checkLogin();
		
		$this->load->view('upload', array('error' => ' ' ));
		
	}

	public function addImageNameToTable(){
	
	}
	
	
	
	
	
	
	
	
	
	
	
	/*
	 * function that perfomes the upload'
	 * takes the data from the form and sends that to
	 * the upload successful page
	 * 
	 * @access : public
	 * @arguments : void
	 * @return : void
	 * 
	 */
	public function do_upload(){
		
		/*
		$i = 0;
		foreach( $_FILES['userfile']['tmp_name'] as $tmp_name){
			if($_FILES['userfile']['tmp_name'][$i]){
				$imageinfo = getimagesize($_FILES['userfile']['tmp_name'][$i]);
				if($imageinfo['mime'] != 'image/png') {
					echo "The file that you want to upload must be a png<br />";
				}
				$uploaddir = 'uploads/';
				$uploadfile = $uploaddir . basename($_FILES['userfile']['name'][$i]);
				if (move_uploaded_file($_FILES['userfile']['tmp_name'][$i], $uploadfile)) {
					echo "File is valid, and was successfully uploaded.<br />";
				} else {
					echo "File uploading failed.<br />";
				}
			} else {
				if($i == 0){
					echo "Uploading Nothing?";
				}
			}
			
			$i++;
		}

		*/
	}
}
?>
