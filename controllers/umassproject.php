<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * main controller of the website
 * defines the main Class, UMassProject
 * 
 * class written by Philippe Ribeiro
 * June 24th, 2011
 */
class UMassProject extends CI_Controller {

	/*
	 * constructor, only calls the
	 * CI_Controller
	 */
	public function __construct(){
		parent::__construct();
	}
	/*
	 * loads the main page
	 * @acess : public
	 * @arguments : void
	 * @returns: void
	 */
	public function index(){
		$this->load->view('index.php');
	}
	
	/*
	 * the data controller
	 * @acess : public
	 * @arguements : void
	 * @returns : void
	 */
	public function data(){
		$this->load->view('data.php');
	}
	
	/*
	 * controller function for the browser
	 * interaction controller
	 * 
	 * @arguments : void
	 * @access : public
	 * @return : void
	 */
	public function browse_interactions(){
		$this->load->view('browse_interactions.php');
	}
	
	/*
	 * controller for upload data
	 * @access : public
	 * @arguments : void
	 * @return : void
	 */
	public function upload(){
		$this->load->view('upload.php');
	}
	
	/*
	 * the login controller
	 * @access : public
	 * @arguments : void
	 * @return : void
	 */
	public function login(){
		$this->load->view('login.php');
	}
	/*
	 * the register controller
	 * @access : public
	 * @arguments : void
	 * @return : void
	 * 
	 */
	public function register(){
		$this->load->view('register.php');
	}
}
