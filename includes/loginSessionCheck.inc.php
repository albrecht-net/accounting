<?php
session_start();

// Array Sessiondata
$dataSession = array(
	'uid' => mysqli_real_escape_string($config['link'], $_SESSION['uid']),
	'username' => mysqli_real_escape_string($config['link'], $_SESSION['username'])
);

// Überprüft ob User-ID oder Benutzername in der Session leer sind (leer entspricht: kein Benutzer angemeldet)
if (empty($dataSession['uid']) || empty($dataSession['username'])) {
    // Mögliche Session löschen
    session_destroy();
    header("Location: login.php");
	exit();
} elseif (isset($dataSession['uid']) && isset($dataSession['username'])) {
	// SQL-Query bereitstellen
    $sqlquery = "SELECT * FROM `users` WHERE `uid` = '" . $dataSession['uid'] . "' AND `username` = '" . $dataSession['username'] . "'";
    
	// Prüft ob Session mit Datenbank übereinstimmt (Wenn 1 Resultat: Benutzer vorhanden und korrekt abgeglichen, Session wird nicht zurückgesetzt)
	if (mysqli_num_rows(mysqli_query($config['link'], $sqlquery)) != 1) {
		echo date('H:i:s') . ' Benutzer abfragen fehlgeschlagen';
		unsetSession();
		exit();
	}
}
?>