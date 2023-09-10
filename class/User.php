<?php
class User {	
   
	private $userTable = 'hms_users';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	    
	
	public function login(){
		if($this->email && $this->password) {
			$loginTable = '';
			if($this->loginType == 'admin') {
				$loginTable = "hms_users";
			} else if ($this->loginType == 'doctor') {
				$loginTable = "hms_doctor";
			} else if ($this->loginType == 'patient') {
				$loginTable = "hms_patients";
			}
			$sqlQuery = "
				SELECT * FROM ".$loginTable." 
				WHERE email = ? AND password = ?";			
			$stmt = $this->conn->prepare($sqlQuery);
			$password = md5($this->password);
			$stmt->bind_param("ss", $this->email, $password);	
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows > 0){
				$user = $result->fetch_assoc();
				$_SESSION["userid"] = $user['id'];
				$_SESSION["role"] = $this->loginType;
				$_SESSION["name"] = $user['email'];					
				return 1;		
			} else {
				return 0;		
			}			
		} else {
			return 0;
		}
	}
	
	public function loggedIn (){
		if(!empty($_SESSION["userid"])) {
			return 1;
		} else {
			return 0;
		}
	}
}
?>