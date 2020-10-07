<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
    }
    
    require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

    // Konfiguration einbinden
    require_once '../config.php';

    // Prüfen ob Benutzer angemeldet
    require 'loginSessionCheck.inc.php';
    if (!$lsc) {
        http_response_code(403);
        exit();
    }

    // Mit Ziel Datenbank verbinden
    require_once 'userDbConnect.inc.php';

    // Array Response
    $_SESSION['response'] = array(
        'alert' => array(
            'alertType' => NULL,
            'alertDismissible' => true
        ),
        'message' => array(
            'messageTitle' => NULL,
            'message' => NULL
        ),
        'values' => array()
    );

    // Array Eingabe
    $dataInput = array(
        'dbHost' => mysqli_real_escape_string($config['link'], trim(strtolower($_POST['dbHost']))),
        'dbPort' => intval($_POST['dbPort']),
        'dbUsername' => mysqli_real_escape_string($config['link'], trim(strtolower($_POST['dbUsername']))),
        'dbPassword' => $_POST['dbPassword'],
        'dbName' => mysqli_real_escape_string($config['link'], trim($_POST['dbName'])),
        'userID' => intval($_SESSION['userID'])
    );

    // Mit der Datenbank verbinden
    $tempLink = mysqli_connect($dataInput['dbHost'] . ':' . $dataInput['dbPort'], $dataInput['dbUsername'], $dataInput['dbPassword'], $dataInput['dbName']);

    // Verbindung überprüfen
    if (!$tempLink) {
        $_SESSION['response']['alert']['alertType'] = 'warning';
        $_SESSION['response']['message']['message'] = "Es wurde vergeblich versucht eine temporäre Verbindung zur angegebenen Datenbank aufzubauen. Bitte überprüfen Sie die Angaben.<hr>Folgender Fehler wurde von MySQL ausgegeben: " . mysqli_connect_error($tempLink);
        header('Location: ../settings/database.php');
        exit();
    } else {
        $dataFunctions = array(
            'created' => 'NOW()'
        );

        // Datenbankangaben speichern
        $columns = "`" . implode("`, `", array_keys($dataInput)) . "`, `" . implode("`, `", array_keys($dataFunctions)) . "`";
        $values = "'" . implode("', '", $dataInput) . "', " . implode(", ", $dataFunctions);
        $sqlquery = "INSERT INTO `databases` (" . $columns . ") VALUES (" . $values . ")";

        // SQL-Query ausführen und überprüfen
        if (!mysqli_query($config['link'], $sqlquery)) {
            echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
            exit();
        }

        // Auf vorhandene Tabellen abfragen
        $sqlquery = "SHOW TABLES";
        $result = mysqli_query($tempLink, $sqlquery);

        // Temporäre Datenbankverbindung schliessen
        mysqli_close($tempLink);

        $_SESSION['response']['alert']['alertType'] = 'primary';
        $_SESSION['response']['message']['messageTitle'] = 'Neue Datenbank erfolgreich gespeichert';

        if (mysqli_num_rows($result) >= 1) {
            $_SESSION['response']['message']['message'] = "Es wurden folgende Tabellen in der Datenbank gefunden:<ul class=\"list-unstyled\">";
            while ($row = mysqli_fetch_row($result)) {
                $_SESSION['response']['message']['message'] .= "<li>" . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . "</li>";
            }
            $_SESSION['response']['message']['message'] .= "</ul>";
        } else {
            $_SESSION['response']['message']['message'] = "<i>In dieser Datenbank wurden keine Tabellen erkannt</i>";
        }

        header('Location: ../settings/database.php');
    }
} else {
    http_response_code(405);
    header ('Allow: POST');
}
?>