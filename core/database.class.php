<?php
/*
* BORG EMS Mysql Database Class - Singleton (only one connection allowed)
*/
class Database {
	private $_connection;
	private static $_instance; // The single instance
	private $_host = "borginventar.dk.mysql";
	private $_username = "borginventar_dk";
	private $_password = "QVHKwLBq";
	private $_database = "borginventar_dk";
    
	/*
	Get an instance of the Database
	@return Instance
	*/
	public static function getInstance() {
		if(!self::$_instance) { // If no instance then make one
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
	// Constructor
	private function __construct() {
		$this->_connection = new mysqli($this->_host, $this->_username, $this->_password, $this->_database);
		$this->_connection->set_charset("utf8");

		// Error handling
		if(mysqli_connect_error()) {
			trigger_error("Failed to connect to to MySQL Database: " . mysql_connect_error(), E_USER_ERROR);
		}
	}
    
	// Magic method clone is empty to prevent duplication of connection
	private function __clone() {
        
    }
    
	// Get mysqli connection
	public function getConnection() {
		return $this->_connection;
	}
}
?>