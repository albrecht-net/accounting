<?php
session_start();

// Array Sessiondata
$dataSession = array(
	'uid' => mysqli_real_escape_string($config['link'], $_SESSION['uid']),
	'username' => mysqli_real_escape_string($config['link'], $_SESSION['username'])
);

// Überprüft ob User-ID oder Benutzername in der Session leer sind (leer entspricht: kein Benutzer angemeldet)
if (empty($dataSession['uid']) || empty($dataSession['username'])) {
    $lsc = FALSE;
} elseif (isset($dataSession['uid']) && isset($dataSession['username'])) {
	// SQL-Query bereitstellen
	$sqlquery = "SELECT * FROM `users` WHERE `userID` = '" . $dataSession['uid'] . "' AND `username` = '" . $dataSession['username'] . "'";
    
	// Prüft ob Sessionangaben mit Datenbank übereinstimmt
	if (mysqli_num_rows(mysqli_query($config['link'], $sqlquery)) != 1) {
		unset($_SESSION['uid']);
		unset($_SESSION['username']);
		$lsc = FALSE;
	} else {
		$lsc = TRUE;
	}
} else {
	$lsc = FALSE;
}
?>