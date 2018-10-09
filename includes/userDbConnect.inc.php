<?php
// SQL-Query bereitstellen
$sqlquery = "SELECT `dbHost`, `dbPort`, `dbUsername`, `dbPassword`, `dbName` FROM `databases` WHERE `dbID` = '" . $_SESSION['dbID'] . "' AND `userID` = '" . $_SESSION['userID'] . "'";

// Anmeldedaten abfragen
$result = mysqli_fetch_assoc(mysqli_query($config['link'], $sqlquery));

// Datenbankverbindung
$userLink = mysqli_connect($result['dbHost'] . ':' . $result['dbPort'], $result['dbUsername'], $result['dbPassword'], $result['dbName']);

// Verbindung überprüfen
if (!$userLink) {
    exit('Connect Error: ' . mysqli_connect_error());
}

// Variablen zurücksetzten
unset($sqlquery);
unset($result);
?>