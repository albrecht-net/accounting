<?php
/**
 * Diese Datei beinhaltet die Konfiguartion für die Web-Applikation
 * Eingaben können direkt in dieser Datei vorgenommen werden und diese dann von configSample.php zu config.php umbenennen.
 * 
 * Gültig ab: Accounting v0.5.0-alpha
 */

$data = array(
    // Servername oder IP-Addresse
    'dbHost' => '',

    // Port
    'dbPort' => '',

    // Benutzername der MySQL-Datenbank
    'dbUsername' => '',

    // Passwort der MySQL-Datenbank
    'dbPassword' => '',

    // Datenbankname für SmallReply
    'dbName' => ''
);

// Datenbankverbindung
$config['link'] = mysqli_connect($data['dbHost'] . ':' . $data['dbPort'], $data['dbUsername'], $data['dbPassword'], $data['dbName']);

// Verbindung überprüfen
if (!$config['link']) {
    exit('Connect Error: ' . mysqli_connect_error());
}

unset($data);
?>