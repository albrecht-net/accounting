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
        'label' => mysqli_real_escape_string($userLink, trim($_POST['label'])),
        'customerNumber' => mysqli_real_escape_string($userLink, trim($_POST['customerNumber']))
    );

    // Leere Felder aus Eingabe Array entfernen
    $dataInput = array_diff($dataInput, array(NULL, '', 0));

    // Prüfen ob Eingabe vorhanden
    if (count($dataInput) > 0) {

        // SQL-Query bereitstellen
        $columns = "`" . implode("`, `", array_keys($dataInput)) . "`";
        $values = "'" . implode("', '", $dataInput) . "'";
        $sqlquery = "INSERT INTO `recipient` (" . $columns . ") VALUES (" . $values . ")";

        // SQL-Query ausführen und überprüfen
        if (!mysqli_query($userLink, $sqlquery)) {
            // Rückmeldung und Weiterleitung
            $_SESSION['response']['alert']['alertType'] = 'danger';
            $_SESSION['response']['message']['message'] = '<strong>MySQL Error:</strong> ' . mysqli_error($userLink);
            header('Location: ../recipient.php');
            exit();
        } else {
            // Rückmeldung und Weiterleitung
            $_SESSION['response']['alert']['alertType'] = 'primary';
            $_SESSION['response']['message']['message'] = 'Empfänger erfolgreich gespeichert';
            header('Location: ../recipient.php');
            exit();
        }

    } else {
        // Rückmeldung und Weiterleitung
        $_SESSION['response']['alert']['alertType'] = 'warning';
        $_SESSION['response']['message']['message'] = 'Keine Eingabe erfolgt';
        header('Location: ../recipient.php');
        exit();
    }
} else {
    http_response_code(405);
    header ('Allow: POST');
}
?>