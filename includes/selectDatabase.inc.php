<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Konfiguration einbinden
    require_once '../config.php';

    // Prüfen ob Benutzer angemeldet
    require 'loginSessionCheck.inc.php';
    if (!$lsc) {
        http_response_code(403);
        exit();
    }

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
        'dbID' => intval($_POST['dbID']),
        'saveDbSelection' => boolval($_POST['saveDbSelection'])
    );

    // Array GET-Variablen
    $dataInputGet = $_GET;

    // SQL-Query bereitstellen
    $sqlquery = "SELECT `dbID` FROM `databases` WHERE `dbID` = " . $dataInput['dbID'] . " AND `userID` = " . intval($_SESSION['userID']);
    $result = mysqli_query($config['link'], $sqlquery);

    // Prüfen ob nur 1 Resultat
    if (mysqli_num_rows($result) != 1) {
        // Rückmeldung und Weiterleitung
        $_SESSION['response']['alert']['alertType'] = 'danger';
        $_SESSION['response']['alert']['alertDismissible'] = false;
        $_SESSION['response']['message']['message'] = 'Ungültige Eingabe';
        
        if (empty($dataInputGet)) {
            header('Location: ../selectDatabase.php');
        } else {
            $dataInputGet;
            header('Location: ../selectDatabase.php?' . http_build_query($dataInputGet));
        }
        exit();
    } else {
        // Abfrage in Array schreiben
        $dataDb = mysqli_fetch_assoc($result);

        // Datenbank ID in Session schreiben
        $_SESSION['userDb']['dbID'] = intval($dataDb['dbID']);
        $_SESSION['userDb']['userDbSet'] = 1;

        // Datenbankauswahl speichern
        if ($dataInput['saveDbSelection']) {
            $sqlquery ="UPDATE `userconfig` SET `defaultDb` = " . $dataDb['dbID'] . " WHERE `userID` = " . intval($_SESSION['userID']);
            
            // SQL-Query ausführen und überprüfen
            if (!mysqli_query($config['link'], $sqlquery)) {
                echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
                exit();
            }
        }

        // Mit Ziel Datenbank verbinden
        if (include_once 'userDbConnect.inc.php') {
            // Fällige Daueraufträge prüfen
            include 'standingOrderCheck.inc.php';
        }

        // Reponse-Data aus Session löschen
        unset($_SESSION['response'], $response);

        // Weiterleitung
        if (empty($dataInputGet['rd'])) {
            header('Location: ../index.php');
        } else {
            $rd = $dataInputGet['rd'];
            header('Location: ../' . $rd);
        }
        exit();
    }
} else {
    http_response_code(405);
    header ('Allow: POST');
}
?>