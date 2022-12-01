<?php
/*
BORG EMS User Class
*/

// Includes
require_once 'database.class.php';

class User {
    private $_connection;
    
    private $_id;
    private $_email;
    private $_firstName;
    private $_lastName;
    private $_role;

    public function __construct($data) {
        $database = Database::getInstance();
        $this->_connection = $database->getConnection();

        if(ctype_digit($data)) {
            $this->getUserFromID($data);
        } else {
            $this->getUserFromFullName($data);
        }
    }

    private function getUserFromFullName($fullname) {
        $fullname = mysqli_escape_string($this->_connection, $fullname);
        $fullname = explode(" ", $fullname);
        $firstname = $fullname[0];
        $lastname = $fullname[1];

        $sql = "SELECT * FROM users WHERE first_name = '$firstname' AND last_name = '$lastname'";
        $result = $this->_connection->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $this->_id = $row['id'];
               $this->_email = $row['email'];
               $this->_firstName = $row['first_name'];
               $this->_lastName = $row['last_name'];
               $this->_role = $row['role'];
            }
        } else {
            return false;
        }
        
        return false;
    }

    private function getUserFromID($id) {
        $id = mysqli_escape_string($this->_connection, $id);

        $sql = "SELECT * FROM users WHERE id = '".$id."'";
        $result = $this->_connection->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $this->_id = $row['id'];
               $this->_email = $row['email'];
               $this->_firstName = $row['first_name'];
               $this->_lastName = $row['last_name'];
               $this->_role = $row['role'];
            }
        } else {
            return false;
        }
        
        return false;
    }

    public function getID() {
        return $this->_id;
    }
    
    public function getEmail() {
        return $this->_email;
    }
    
    public function getFirstName() {
        return $this->_firstName;
    }
    
    public function getLastName() {
        return $this->_lastName;
    }

    public function getFullName() {
        return $this->_firstName . " " . $this->_lastName;
    }

    public function getRole() {
        return $this->_role;
    }
}
?>