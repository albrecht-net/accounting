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
        'oldPassword' => $_POST['inputOldPassword'],
        'password1' => $_POST['inputPassword1'],
        'password2' => $_POST['inputPassword2']
    );

    // Passwortübereinstimmung prüfen
    if ($dataInput['password1'] != $dataInput['password2']) {
        // Rückmeldung und Weiterleitung
        $_SESSION['response']['alert']['alertType'] = 'danger';
        $_SESSION['response']['alert']['alertDismissible'] = false;
        $_SESSION['response']['message']['message'] = 'Das neue Passwort wurde nicht korrekt wiederholt';
        header('Location: ../settings/account.php');
        exit();
    } else {
        // SQL-Query bereitstellen
        $sqlquery = "SELECT `password` FROM `users` WHERE `userID` = " . intval($_SESSION['userID']);
        $result = mysqli_query($config['link'], $sqlquery);

        // Benutzer abfragen
        if (mysqli_num_rows($result) != 1) {
            // Rückmeldung und Weiterleitung
            $_SESSION['response']['alert']['alertType'] = 'danger';
            $_SESSION['response']['alert']['alertDismissible'] = false;
            $_SESSION['response']['message']['message'] = 'Ungültige Eingabe';
            header('Location: ../settings/account.php');
            exit();
        } else {
            // Abfrage in Array schreiben
            $dataDb = mysqli_fetch_assoc($result);

            // Passwort validieren
            if (!password_verify($dataInput['oldPassword'], $dataDb['password'])) {
                // Rückmeldung und Weiterleitung
                $_SESSION['response']['alert']['alertType'] = 'danger';
                $_SESSION['response']['alert']['alertDismissible'] = false;
                $_SESSION['response']['message']['message'] = 'Bisheriges Passwort falsch';
                header('Location: ../settings/account.php');
                exit();
            } else {
                // Passwort Hash
                $dataInput['password'] = password_hash($dataInput['password1'], PASSWORD_DEFAULT);
                
                unset($dataInput['oldPassword']);
                unset($dataInput['password1']);
                unset($dataInput['password2']);

                // SQL-Query bereitstellen
                $set = [];
                foreach ($dataInput as $column => $value) {
                    $set[] = "`" . $column . "` = '" . $value . "'";
                }
                $sqlquery = "UPDATE `users` SET " . implode(", ", $set) . " WHERE `users`.`userID` = " . intval($_SESSION['userID']);

                // SQL-Query ausführen und überprüfen
                if (!mysqli_query($config['link'], $sqlquery)) {
                    echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
                    exit();
                }

                // Benutzer abmelden
                session_start();
                session_destroy();
                header("Location: ../login.php?passwordchanged=1");
            }
        }
    }
} else {
    http_response_code(405);
    header ('Allow: POST');
}
?>