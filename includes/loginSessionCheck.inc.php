<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {
    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
    }
    
    require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

	// Array Sessiondata
	$dataSession = array(
		'userID' => intval(session::get('userID')),
		'username' => db::init(1)->escapeString(session::get('username'))
	);
	// Überprüft ob User-ID oder Benutzername in der Session leer sind (leer entspricht: kein Benutzer angemeldet)
	if (empty($dataSession['userID']) || empty($dataSession['username'])) {
		$lsc = false;
	} elseif (isset($dataSession['userID']) && isset($dataSession['username'])) {
		// SQL-Query bereitstellen
		$sqlquery = "SELECT * FROM `users` WHERE `userID` = '" . $dataSession['userID'] . "' AND `username` = '" . $dataSession['username'] . "' AND `activation` = 'Y' AND `status` = 'Y'";
		db::init(1)->query($sqlquery);

		// Prüft ob Sessionangaben mit Datenbank übereinstimmt
		if (db::init(1)->count() != 1) {
			session::delete('userID');
			session::delete('username');
			$lsc = false;
		} else {
			$lsc = true;
		}
	} else {
		$lsc = true;
	}
} else {
    http_response_code(204);
}
?>