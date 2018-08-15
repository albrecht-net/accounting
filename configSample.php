<?php /* WEB-APPLICATION CONFIG */
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

// Datenbankname weitergeben
$config['dbName'] = $data['dbName'];

return $config;
?>