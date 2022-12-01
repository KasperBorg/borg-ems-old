<?php
/*
BORG EMS Validation Class
*/

// Locale
setlocale(LC_ALL, "danish", "Danish_Denmark.1252", "danish_denmark", "dan_DNK", "da_DK.UTF-8");

class Validation {
    
    /* 
    Checks if input is a valid doms number. 
    
    Example of a valid doms number:    1234-123456
    Example of an invalid doms number: 123-12345
    */
    public function domsNumber($input) {
        if(preg_match("/^\d{1,4}-\d{6}$/", $input)) {
            return true;
		} else {
			return false;
		}
    }
    
    /*
    Checks if input is a valid time. Input must be rounded up to quaters.
    
    Examples of valid times:   12.00, 15.15
    Examples of invalid times: 12.04, 15:46
    */
    public function time($input) {
        if(preg_match("/^([01]?[0-9]|2[0-3])\.(00|30)$/", $input)) {
	        return true;
		} else {
			return false;
		}
    }
    
    /*
    Checks if input is between 0 and 1999.
    */
    public function kilometers($input) {
        if(preg_match("/^1?\d?\d?\d$/", $input)) {
	        return true;
		} else {
			return false;
		}
    }

    /*
    Checks if input from the daily docket formular is valid.
    */
    public function dailyDocket($object) {
        $errors = array();
        
        if(empty($object->name)) {
            $errors[] = 'Angiv venligst et navn.';
        }
        
        if(empty($object->date)) {
            $errors[] = 'Angiv venligst en dato.';            
        }
        
        if(empty($object->location)) {
            $errors[] = 'Angiv venligst et arbejdssted.';
        }
        
        if(empty($object->timeStart)) {
            $errors[] = 'Angiv venligst et start tidspunkt.';            
        } else {
            if(!$this->time($object->timeStart)) {
                $errors[] = $object->timeStart.' er ikke et gyldigt start tidspunkt.';
            }
        }
        
        if(empty($object->timeEnd)) {
            $errors[] = 'Angiv venligst et slut tidspunkt.';
        } else {
            if(!$this->time($object->timeEnd)) {
                $errors[] = $object->timeEnd.' er ikke et gyldigt slut tidspunkt.';
            }
        }

        if(empty($object->timeStartStore)) {
            $errors[] = 'Angiv venligst et start tidspunkt for butik.';            
        } else {
            if(!$this->time($object->timeStartStore)) {
                $errors[] = $object->timeStartStore.' er ikke et gyldigt butik start tidspunkt.';
            }
        }
        
        if(empty($object->timeEndStore)) {
            $errors[] = 'Angiv venligst et slut tidspunkt for butik.';
        } else {
            if(!$this->time($object->timeEndStore)) {
                $errors[] = $object->timeEndStore.' er ikke et gyldigt butik slut tidspunkt.';
            }
        }
                
        if(empty($object->kilometers)) {
            
        } else {
            if(!$this->kilometers($object->kilometers)) {
                $errors[] = $object->kilometers.' er ikke et gyldigt antal kilometer.';
            } 
        }
        
        if(empty($errors)) {
            return null;
        } else {
            return $errors;
        }
    }    
    
    /*
    Checks if input from the shortage list formular is valid.
    */
    public function shortageList($object) {
        $errors = array();

        if(empty($object->date)) {
            $errors[] = 'Angiv venligst en dato.';
        }
        
        if(empty($object->station->getID())) {
            $errors[] = 'Angiv venligst et gyldigt stations nummer.';            
        }
                
        if(empty($object->materials)) {
            $errors[] = 'Angiv venligst en mangelliste.';
        }
        
        if(empty($errors)) {
            return null;
        } else {
            return $errors;
        }
    }

    /*
    Checks if input from the SJA formular is valid.
    */
    public function sja($object) {
        $errors = array();

        if(empty($object->users)) {
            $errors[] = 'Angiv venligst montører.';
        }

        if(empty($object->majorActivity)) {
            $errors[] = 'Angiv venligst en aktivitet.';
        }

        if(empty($object->date)) {
            $errors[] = 'Angiv venligst en dato.';
        }

        if(empty($object->station->getID())) {
            $errors[] = 'Angiv venligst et gyldigt stations nummer.';            
        }

        if(empty($object->securityCoordinator)) {
            $errors[] = 'Angiv venligst en sikkerhedskoordinator.';
        }

        if(empty($object->client)) {
            $errors[] = 'Angiv venligst en Opdragsgiver hos Circle K.';
        }

        foreach ($object->riskEvaluations as $riskEvaluation) {   
            if(empty($riskEvaluation->getActivity())) {
                $errors[] = 'Angiv venligst en aktivitet under risikovurderingen.';
            }

            if(empty($riskEvaluation->getRisk())) {
                $errors[] = 'Angiv venligst en risiko under risikovurderingen.';
            }

            if(empty($riskEvaluation->getPercausion())) {
                $errors[] = 'Angiv venligst en sikkerhedsforanstaltning under risikovurderingen.';
            }

            if(empty($riskEvaluation->getResponsible())) {
                $errors[] = 'Angiv venligst hvem der er ansvarlig under risikovurderingen.';
            }
        }
        
        if(empty($errors)) {
            return null;
        } else {
            return $errors;
        }
    }
}