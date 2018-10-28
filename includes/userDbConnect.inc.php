<?php
if ($_SESSION['userDb']['userDbSet']) {
    // Array Sessiondata
    $dataSession = array(
        'dbID' => mysqli_real_escape_string($config['link'], $_SESSION['userDb']['dbID']),
        'userID' => mysqli_real_escape_string($config['link'], $_SESSION['userID'])
    );

    // SQL-Query bereitstellen
    $sqlquery = "SELECT `dbHost`, `dbPort`, `dbUsername`, `dbPassword`, `dbName` FROM `databases` WHERE `dbID` = '" . $dataSession['dbID'] . "' AND `userID` = '" . $dataSession['userID'] . "'";

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
}
?>