<?php

// Locale
setlocale(LC_ALL, "danish", "Danish_Denmark.1252", "danish_denmark", "dan_DNK", "da_DK.UTF-8");

if(isset($_GET['stationNumber']) && $_GET['stationNumber'] != "") {
    require_once 'core/station.class.php';
    $station = new Station($_GET['stationNumber']);
    utf8_encode_deep($station);
    echo json_encode($station);
} else {
    echo "Incorrect Parameters.";
}

// UTF-8 Encode Entire Object
function utf8_encode_deep(&$input) {
	if (is_string($input)) {
		$input = utf8_encode($input);
	} else if (is_array($input)) {
		foreach ($input as &$value) {
			utf8_encode_deep($value);
		}
		
		unset($value);
	} else if (is_object($input)) {
		$vars = array_keys(get_object_vars($input));
		
		foreach ($vars as $var) {
			utf8_encode_deep($input->$var);
		}
	}
}
?>