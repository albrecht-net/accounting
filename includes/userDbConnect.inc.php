<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {
    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
    }
    
    require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

    if ($_SESSION['userDb']['userDbSet']) {
        // SQL-Query bereitstellen
        $sqlquery = "SELECT `dbHost`, `dbPort`, `dbUsername`, `dbPassword`, `dbName` FROM `databases` WHERE `dbID` = " . intval($_SESSION['userDb']['dbID']) . " AND `userID` = " . intval($_SESSION['userID']);

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
} else {
    http_response_code(204);
}
?>