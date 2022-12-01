<?php
/*
BORG EMS Security Class
*/

// Includes
require_once 'database.class.php';

class Security {
	private $_connection;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->_connection = $database->getConnection();
    }
    
	public function checkLogin($email, $password) {
        $email 	  = strtolower(mysqli_escape_string($this->_connection, $email));
        $password = strtolower(mysqli_escape_string($this->_connection, $password));
        
        $sql = "SELECT * FROM users WHERE email = '".$email."' AND password = '".$password."'";
        $result = $this->_connection->query($sql);
        
        if ($result->num_rows > 0) {                
            while($row = $result->fetch_assoc()) {
                $_SESSION['id'] = $row["id"];
            }
            
            return true;
        } else {
            return false;
        }
        
        return false;
    }

	public function isLoggedIn() {
		if(isset($_SESSION['id'])) {
			return true;
		} else {
			return false;
		}
	}
}