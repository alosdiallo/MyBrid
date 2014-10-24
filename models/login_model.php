<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_Model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
	
	public function checkLogin($username, $password){
		
		//needs to encrypt beforehand
		$password = md5($password);
		$str = "SELECT name, username, email, admin FROM users WHERE password='".mysql_real_escape_string($password)."' and username='".mysql_real_escape_string($username)."'";
		
		$query = $this->db->query($str);
		
		if(!$query){
			return false;
		}
		
		else{
			if($query->num_rows() > 0){
				return $query->row();
			}
			
			return false;
		}
		
	}
}
?>
