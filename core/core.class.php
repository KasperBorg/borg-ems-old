<?php
/*
BORG EMS Core Class
*/

// Includes
require_once 'core/database.class.php';
require_once 'core/user.class.php';

class Core {
    private $_connection;
    private $_user;

    public function __construct() {
        $database = Database::getInstance();
        $this->_connection = $database->getConnection();
        $this->_user = new User($_SESSION['id']);
    }

    public function getUsers() {
        $sql = "SELECT * FROM users";
        $result = $this->_connection->query($sql);
        
        $users = array();

        while($row = $result->fetch_assoc()) {
               $users[] = new User($row['id']);
        }

        return $users;
    }

    public function printTopMenu() {
        ?>
        <nav class="menu">
            <ul>
                <li><a href="index.php" <?php echo (basename($_SERVER['SCRIPT_FILENAME']) == 'index.php' ? 'class="selected"' :  " TEST") ?>>Dagsedler</a></li>
                <li><a href="mangelliste.php" <?php echo (basename($_SERVER['SCRIPT_FILENAME']) == 'mangelliste.php' ? 'class="selected"' :  " TEST") ?>>Mangelliste</a></li>
        <?php
        if($this->_user->getRole() > 0) {
        ?>
                <li><a href="sja.php" <?php echo (basename($_SERVER['SCRIPT_FILENAME']) == 'sja.php' ? 'class="selected"' :  " TEST") ?>>SJA</a></li>
        <?php
        }
        ?>
                <li><a href="logout.php">Log ud</a></li>        
            </ul>
        </nav>
        <?php
    }
}
?>