<?php
/*
BORG EMS Shortage List Forms Class
*/

// Includes
require_once 'core/database.class.php';
require_once 'core/user.class.php';
require_once 'core/station.class.php';

class ShortageList {
    private $_connection;
    private $_errors = array();

    public $user;
    public $date;
    public $station;
    public $materials;
    public $materialsFixed;

    public function __construct($user, $date, $station, $materials) {
        
        // Initialize Database Connection
        $database = Database::getInstance();
        $this->_connection = $database->getConnection();

        $this->user = $user;
        $this->date = mysqli_real_escape_string($this->_connection, $date);
        $this->station = $station;
        $this->materials = mysqli_real_escape_string($this->_connection, $materials);
        $this->materialsFixed = $this->fixNewLine($materials);
    }

    private function translateWeekdays($date) {
        switch ($date) {
            case "Mon":
                return "Mandag";
                break;
            case "Tue":
                return "Tirsdag";
                break;
            case "Wed":
                return "Onsdag";
                break;
            case "Thu":
                return "Torsdag";
                break;
            case "Fri":
                return "Fredag";
                break;
            case "Sat":
                return "Lørdag";
                break;
            case "Sun":
                return "Søndag";
                break;
            default:
                return $date;
                break;
        }
    }
    
    private function saveToDatabase() {
        $sql = "INSERT INTO shortage_list (user_id, date, station_id, materials)
                VALUES (".$this->user->getID().", '$this->date', ".$this->station->getID().", '$this->materials')";
        
        $result = $this->_connection->query($sql);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

   private function sendMail() {
        $headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$headers .= "From: ".$this->user->getFullName()." <mail@borginventar.dk>\r\n";

		$subject = "Mangelliste [".$this->station->getNumber()."] ".$this->station->getName();

		$message  = "
        <html>
        <body>
            <table>
                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Navn</td>
                    <td>".$this->user->getFullName()."</td> 
                </tr>
                
                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Ugedag</td>
                    <td>".$this->translateWeekdays(date('D', strtotime($this->date)))."</td> 
                </tr>
                
                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Dato</td>
                    <td>$this->date</td> 
                </tr>
                
                <tr>
                    <td></td>
                    <td><br /></td> 
                </tr> 

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Stations Nummer</td>
                    <td>".$this->station->getNumber()."</td> 
                </tr> 
                
                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Stations Navn</td>
                    <td>".$this->station->getName()."</td> 
                </tr> 

                <tr>
                    <td></td>
                    <td><br /></td> 
                </tr> 
                
                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Mangelliste</td>
                    <td>".$this->materialsFixed."</td> 
                </tr> 
            </table>
        </body>
        </html>
        ";

        if(!mail("mangelliste@borginventar.dk", $subject, $message, $headers)) {
            return false;
        }

        if(!mail($this->user->getEmail(), $subject, $message, $headers)) {
            return false;
        }

        return true;
    } 

    public function getErrors() {
        return $this->_errors;
    }

    public function create() {     
        require_once 'core/validation.class.php';

        $validation = new Validation;
        $errors = $validation->shortageList($this);
        
        if(!empty($errors)) {
            $this->_errors = $errors;
            return false;
        } else {            
            if($this->sendMail()) {
                if($this->saveToDatabase()) {
                    return true;
                } else {
                    $this->_errors[] = 'Kunne ikke gemme Mangelliste til databasen.';
                    return false;
                }
            } else {
                $this->_errors[] = 'Kunne ikke sende email.';
                return false;
            }
        }
    }

    public function fixNewLine($data) {
        return str_replace("\r\n", '<br />', $data); 
    }
}
?>