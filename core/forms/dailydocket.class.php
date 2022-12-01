<?php
/*
BORG EMS Daily Docket Forms Class
*/

// Includes
require_once 'core/database.class.php';
require_once 'core/user.class.php';
require_once 'core/validation.class.php';

class DailyDocket {
    private $_connection;
    private $_errors = array();

    public $userID;
    public $name;
    public $email;
    public $date;
    public $location;
    public $locationFixed;
    public $timeStart;
    public $timeEnd;
    public $timeTotal;
    public $timeStartStore;
    public $timeEndStore;
    public $timeTotalStore;
    public $domsNumber;
    public $ownCar;
    public $kilometers;
    public $workDescription;
    public $materials;
    public $workDescriptionFixed;
    public $materialsFixed;
    public $bridges;
    public $hotels;

    public function __construct($user, $date, $location, $timeStart, $timeEnd, $timeStartStore, $timeEndStore, $domsNumber, $ownCar, $kilometers, $workDescription, $materials, $bridges, $hotels) {
        
        // Initialize Database Connection
        $database = Database::getInstance();
        $this->_connection = $database->getConnection();

        $this->userID = mysqli_real_escape_string($this->_connection, $user->getID());
        $this->name = mysqli_real_escape_string($this->_connection, $user->getFirstName() . " " . $user->getLastName());
        $this->email = mysqli_real_escape_string($this->_connection, $user->getEmail());
        $this->date = mysqli_real_escape_string($this->_connection, $date);
        $this->location = mysqli_real_escape_string($this->_connection, $location);
        $this->locationFixed = $this->fixNewLine($location);
        $this->timeStart = $timeStart;
        $this->timeEnd = $timeEnd;
        $this->timeTotal = mysqli_real_escape_string($this->_connection, (((strtotime($this->timeEnd) - strtotime($this->timeStart))/60)/60));
        $this->timeStartStore = $timeStartStore;
        $this->timeEndStore = $timeEndStore;
        $this->timeTotalStore = mysqli_real_escape_string($this->_connection, (((strtotime($this->timeEndStore) - strtotime($this->timeStartStore))/60)/60));
        $this->domsNumber = mysqli_real_escape_string($this->_connection, $domsNumber);
        $this->ownCar = mysqli_real_escape_string($this->_connection, $ownCar);
        if($kilometers > 0) {
            $this->kilometers = mysqli_real_escape_string($this->_connection, $kilometers);
        } else {
            $this->kilometers = 0;
        }
        $this->workDescription = mysqli_real_escape_string($this->_connection, $workDescription);
        $this->materials = mysqli_real_escape_string($this->_connection, $materials);

        $this->workDescriptionFixed = $this->fixNewLine($workDescription);
        $this->materialsFixed = $this->fixNewLine($materials);

        $this->bridges = mysqli_real_escape_string($this->_connection, $bridges);
        $this->hotels = mysqli_real_escape_string($this->_connection, $hotels);

        $validation = new Validation;
        $errors = $validation->dailyDocket($this);
        
        if(!empty($errors)) {
            $this->_errors = $errors;
            return false;
        }

        $this->timeStart = number_format($timeStart, 2);
        $this->timeEnd = number_format($timeEnd, 2);

        $this->timeStartStore = number_format($timeStartStore, 2);
        $this->timeEndStore = number_format($timeEndStore, 2);
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
        $sql = "INSERT INTO daily_docket (user_id, date, location, time_start, time_end, time_total, time_start_store, time_end_store, time_total_store, doms_number, own_car, kilometers, work_description, materials, bridges, hotels)
                VALUES ($this->userID, '$this->date', '$this->location', '$this->timeStart', '$this->timeEnd', '$this->timeTotal', '$this->timeStartStore', '$this->timeEndStore', '$this->timeTotalStore', '$this->domsNumber', '$this->ownCar', $this->kilometers, '$this->workDescription', '$this->materials', '$this->bridges', '$this->hotels')";
        
        $result = $this->_connection->query($sql);

        if ($result) {
            return true;
        } else {
            echo $this->_connection->error;
            return false;
        }
    }

   private function sendMail() {
        $headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$headers .= "From: " . $this->name . " <mail@borginventar.dk>\r\n";

		$subject = $this->date;

		$message  = "
        <html>
        <body>
            <table>
                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Navn</td>
                    <td>$this->name</td> 
                </tr>

                <tr>
                    <td></td>
                    <td><br /></td> 
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
                    <td style=\"font-weight: bold; width: 130px;\">Samlet Tid</td>
                    <td>$this->timeTotal</td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Start Tid</td>
                    <td>$this->timeStart</td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Slut Tid</td>
                    <td>$this->timeEnd</td> 
                </tr>

                <tr>
                    <td></td>
                    <td><br /></td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Samlet Tid Butik</td>
                    <td>$this->timeTotalStore</td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Start Tid Butik</td>
                    <td>$this->timeStartStore</td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Slut Tid Butik</td>
                    <td>$this->timeEndStore</td> 
                </tr>

                <tr>
                    <td></td>
                    <td><br /></td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Quant</td>
                    <td>$this->domsNumber</td> 
                </tr>         

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Arbejdssted</td>
                    <td>".$this->locationFixed."</td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Opgavensart</td>
                    <td>".$this->workDescriptionFixed."</td> 
                </tr>            

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Materialer</td>
                    <td>".($this->materials == "" ? 'Ingen' : $this->materialsFixed)."</td> 
                </tr>        

                <tr>
                    <td></td>
                    <td><br /></td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Kilometer</td>
                    <td>$this->kilometers</td> 
                </tr>    
            
                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Egen Bil</td>
                    <td>$this->ownCar</td> 
                </tr>            
            
                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Broafgift</td>
                    <td>$this->bridges</td> 
                </tr>            
            
                <tr>
                    <td style=\"font-weight: bold; width: 130px;\">Hotel</td>
                    <td>$this->hotels</td> 
                </tr>                      
            </table>
        </body>
        </html>
        ";

        if(!mail("dagsedler@borginventar.dk", $subject, $message, $headers)) {
            return false;
        }

        if(!mail($this->email, $subject, $message, $headers)) {
            return false;
        }

        return true;
    } 

    public function getErrors() {
        return $this->_errors;
    }

    public function create() {     

        $validation = new Validation;
        $errors = $validation->dailyDocket($this);
        
        if(!empty($errors)) {
            $this->_errors = $errors;
            return false;
        } else {
            if($this->sendMail()) {
                if($this->saveToDatabase()) {
                    return true;
                } else {
                    $this->_errors[] = 'Kunne ikke gemme dagseddel til databasen.';
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