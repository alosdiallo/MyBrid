<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register_Model extends CI_Model{
	
	private $username;
	private $email;
	private $password;
	private $name;
	
	public function __construct(){
		parent::__construct();
	}
	
	public function setData($data){
		$this->setUsername($data['username']);
		$this->setPassword($data['password'], $data['passconf']);
		$this->setEmail($data['email']);
		$this->setName($data['firstname'], $data['lastname']);
		
		$status = $this->insertNewUser();
		
		return $status;
	}
	
	private function insertNewUser(){
		//if($this->session->userdata('admin') != 1){return 0;}
		$user_id = 0;
		
		//checks if the user already exists
		$query = $this->db->get_where('users', array('username' => $this->getUsername()));
		
		if($query->num_rows > 0){
			return $user_id;
		}
		
		$data = array(	'id' => $this->db->insert_id(),
						'name' => $this->getName(),
						'username' => $this->getUsername(),
						'password' => md5($this->getPassword()),
						'email' => $this->getEmail(),
						'date' => time(),
						'admin' => '3');
						
		if($this->db->insert('users', $data)){
			$user_id = $this->db->insert_id();
		}
		return $user_id;
	}
	
	public function deleteUser($userId){
		$this->db->delete('users', $userId);
	}
	
	public function setName($firstname, $lastname){
		if((strcmp($firstname, "") != 0) && (strcmp($lastname, "") != 0)){
			$this->name = $firstname . " " . $lastname;
		}
		else{
			$this->name = "";
		}
	}
	public function setPassword($password, $passconf){
		if(strcmp($password, $passconf) == 0){
			$this->password = $password;
		}
		else{
			$this->password = "";
		}
	}
	public function setUsername($username){
		if(strcmp($username, "") != 0){
			$this->username = $username;
		}
		else{
			$this->username = "";
		}
	}
	
	public function setEmail($email){
		if(strcmp($email, "") != 0){
			$this->email = $email;
		}
		else{
			$this->email = "";
		}
	}
	
	public function getName(){
		return $this->name;
	}
	public function getPassword(){
		return $this->password;
		
	}
	public function getEmail(){
		return $this->email;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
}
?>
