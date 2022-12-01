<?php
/*
BORG EMS Station Class
*/

// Locale
setlocale(LC_ALL, "danish", "Danish_Denmark.1252", "danish_denmark", "dan_DNK", "da_DK.UTF-8");

// Includes
require_once 'database.class.php';

class Station {
    private $_connection;

    public $id;
    public $name;
    public $number;
    public $address;
    public $city;
    public $zipCode;
    public $phoneNumber;

    public function __construct($number) {
        
        // Initialize Database Connection
        $database = Database::getInstance();
        $this->_connection = $database->getConnection();

        $this->getStationFromNumber($number);
    }

    private function getStationFromNumber($number) {
        $number = mysqli_escape_string($this->_connection, $number);

        $sql = "SELECT * FROM stations WHERE number = '$number'";
        $result = $this->_connection->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $this->id = $row['id'];
               $this->name = $row['name'];
               $this->number = $row['number'];
               $this->address = $row['address'];
               $this->city = $row['city'];
               $this->zipCode = $row['zip_code'];
               $this->phoneNumber = $row['phone_number'];
            }
        } else {
            return false;
        }
        
        return false;
    }
    
    public function getID() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
    
    public function getNumber() {
        return $this->number;
    }
    
    public function getAddress() {
        return $this->address;
    }
    
    public function getCity() {
        return $this->city;
    }
    
    public function getZipCode() {
        return $this->zipCode;
    }
    
    public function getPhoneNumber() {
        return $this->phoneNumber;
    }
}
?>