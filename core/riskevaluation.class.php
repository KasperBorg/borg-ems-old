<?php
/*
BORG EMS Risk Evaluation Class
*/

class RiskEvaluation {
    public $activity;
    public $risk;
    public $percausion;
    public $responsible;

    public function __construct($activity, $risk, $percausion, $responsible) {

        $this->activity = $activity;
        $this->risk = $risk;
        $this->percausion = $percausion;
        $this->responsible = $responsible;
    }

    public function getActivity() {
        return $this->activity;
    }

    public function getRisk() {
        return $this->risk;
    }

    public function getPercausion() {
        return $this->percausion;
    }

    public function getResponsible() {
        return $this->responsible;
    }
}
?>