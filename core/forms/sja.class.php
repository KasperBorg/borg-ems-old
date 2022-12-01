<?php
/*
BORG EMS Secure Job Analysis Form Class
*/

// Includes
require_once 'core/database.class.php';
require_once 'core/user.class.php';
require_once 'core/station.class.php';

class SJA {
    private $_connection;
    private $_errors = array();

    public $users;
    public $usersRaw;
    public $userSent;
    public $majorActivity;
    public $date;
    public $station;
    public $securityCoordinator;
    public $client;
    public $specialConcerns;
    public $nearMiss;
    public $riskEvaluations = array();

    public function __construct($users, $userSent, $majorActivity, $date, $station, $securityCoordinator, $client, $specialConcerns, $nearMiss, $riskEvaluations) {
        
        // Initialize Database Connection
        $database = Database::getInstance();
        $this->_connection = $database->getConnection();

        $this->usersRaw = $users;
        $this->users = $this->userNameToObject($users);
        $this->userSent = $userSent;
        $this->majorActivity = mysqli_real_escape_string($this->_connection, $majorActivity);
        $this->date = mysqli_real_escape_string($this->_connection, $date);
        $this->station = $station;
        $this->securityCoordinator = mysqli_real_escape_string($this->_connection, $securityCoordinator);
        $this->client = mysqli_real_escape_string($this->_connection, $client);
        $this->specialConcerns = mysqli_real_escape_string($this->_connection, $specialConcerns);
        $this->nearMiss = mysqli_real_escape_string($this->_connection, $nearMiss);
        $this->riskEvaluations = $riskEvaluations;
    }

    private function userNameToObject($usersRaw) {
        $usernames = explode(", ", $usersRaw);
        $users = array();

        foreach($usernames as $username) {
            $users[] = new User($username);
        }

        return $users;
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
        $sql = "INSERT INTO sja (date, station_id, security_coordinator, client, activity, special_concerns, near_miss_incidents)
                VALUES ('".$this->date."', '".$this->station->getID()."', '".$this->securityCoordinator."', '".$this->client."', '".$this->majorActivity."', '".$this->specialConcerns."', '".$this->nearMiss."')";
        
        $result = $this->_connection->query($sql);

        if (!$result) {
            echo "Error Inserting SJA: " . $this->_connection->error;
            return false;
        }

        $sja_id = $this->_connection->insert_id;

        foreach($this->riskEvaluations as $riskEvaluation) {
            $sql = "INSERT INTO risk_evaluation (activity, risk, percausion, responsible, sja_id)
                    VALUES ('".$riskEvaluation->getActivity()."', '".$riskEvaluation->getRisk()."', '".$riskEvaluation->getPercausion()."', '".$riskEvaluation->getResponsible()."', '".$sja_id."')";
        
            $result = $this->_connection->query($sql);

            if (!$result) {
                echo "Error Inserting Risk Evaluations: " . $this->_connection->error;
                return false;
            }
        }

        foreach($this->users as $user) {
            $sql = "INSERT INTO sja_users (user_id, sja_id)
                    VALUES ('".$user->getID()."', '".$sja_id."')";
        
            $result = $this->_connection->query($sql);

            if (!$result) {
                echo "Error Inserting Users: " . $this->_connection->error;
                return false;
            }
        }

        return true;
    }

    private function sendMail() {
        $headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$headers .= "From: ".$this->userSent->getFullName()." <mail@borginventar.dk>\r\n";

		$subject = "Sikkert Job Analyse [".$this->station->getNumber()."] ".$this->station->getName();

		$message  = "
        <html>
        <body>
            <h1>Sikkert Job Analyse</h1>
            
            <br />

            <table>
                <tr>
                    <td style=\"font-weight: bold; width: 180px;\"><h3>Information</h3></td>
                    <td></td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Sendt Af</td>
                    <td>".$this->userSent->getFullName()."</td> 
                </tr>
                
                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Ugedag</td>
                    <td>".$this->translateWeekdays(date('D', strtotime($this->date)))."</td> 
                </tr>
                
                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Dato</td>
                    <td>$this->date</td> 
                </tr>
                
                <tr>
                    <td></td>
                    <td><br /></td> 
                </tr> 
                
                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Montører</td>
                    <td>".$this->usersRaw."</td> 
                </tr> 

                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Aktivitet</td>
                    <td>".$this->majorActivity."</td> 
                </tr> 

                <tr>
                    <td></td>
                    <td><br /></td> 
                </tr> 

                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Stations Nummer</td>
                    <td>".$this->station->getNumber()."</td> 
                </tr> 
                
                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Stations Navn</td>
                    <td>".$this->station->getName()."</td> 
                </tr> 

                <tr>
                    <td></td>
                    <td><br /></td> 
                </tr>
                
                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Sikkerhedskoordinator</td>
                    <td>".$this->securityCoordinator."</td> 
                </tr> 

                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Montører</td>
                    <td>".$this->client."</td> 
                </tr> 

                <tr>
                    <td></td>
                    <td><br /></td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Specielle hensyn på denne service station</td>
                    <td>".$this->specialConcerns."</td> 
                </tr> 

                <tr>
                    <td style=\"font-weight: bold; width: 180px;\">Montører</td>
                    <td>".$this->nearMiss."</td> 
                </tr>

                <tr>
                    <td style=\"font-weight: bold; width: 180px;\"><br /><br /><h3>Risikovurderinger</h3></td>
                    <td></td> 
                </tr>
                ";
        $i = 1;
        foreach($this->riskEvaluations as $riskEvaluation) {
            $message .= "
            <tr>
                <td style=\"font-weight: bold; width: 180px;\"></td>
                <td style=\"font-weight: bold;\">Risikovurdering $i</td>
            </tr>
            
            <tr>
                <td style=\"font-weight: bold; width: 180px;\">Aktivitet</td>
                <td>".$riskEvaluation->getActivity()."</td> 
            </tr>

            <tr>
                <td style=\"font-weight: bold; width: 180px;\">Risiko</td>
                <td>".$riskEvaluation->getRisk()."</td> 
            </tr>

            <tr>
                <td style=\"font-weight: bold; width: 180px;\">Sikkerhedsforanstaltning</td>
                <td>".$riskEvaluation->getPercausion()."</td> 
            </tr>

            <tr>
                <td style=\"font-weight: bold; width: 180px;\">Ansvarlig</td>
                <td>".$riskEvaluation->getResponsible()."</td> 
            </tr>
            
            <tr>
                    <td></td>
                    <td><br /></td> 
            </tr>
            ";   

            $i++;    
        }

        $message .= "                
            </table>
        </body>
        </html>
        ";

        if(!mail("sja@borginventar.dk", $subject, $message, $headers)) {
            return false;
        }

        if(!mail($this->userSent->getEmail(), $subject, $message, $headers)) {
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
        $errors = $validation->sja($this);
        
        if(!empty($errors)) {
            $this->_errors = $errors;
            return false;
        } else {            
            if($this->sendMail()) {
                if($this->saveToDatabase()) {
                    return true;
                } else {
                    $this->_errors[] = 'Kunne ikke gemme SJA til databasen.';
                    return false;
                }
            } else {
                $this->_errors[] = 'Kunne ikke sende email.';
                return false;
            }
        }
    }

}
?>