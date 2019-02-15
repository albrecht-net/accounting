<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    session_start();

    // Konfiguration einbinden
    require_once '../config.php';

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
        'username' => mysqli_real_escape_string($config['link'], $_POST['inputUsername']),
        'password' => $_POST['inputPassword']
    );

    // Array GET-Variablen
    $dataInputGet = $_GET;

    // SQL-Query bereitstellen
    $sqlquery = "SELECT `username`, `password`, `userID` FROM `users` WHERE `username` = '" . $dataInput['username'] . "' AND `activation` = 'Y' AND `status` = 'Y'";
    $result = mysqli_query($config['link'], $sqlquery);

    // Benutzer abfragen
    if (mysqli_num_rows($result) != 1) {
        // Rückmeldung und Weiterleitung
        $_SESSION['response']['alert']['alertType'] = 'danger';
        $_SESSION['response']['alert']['alertDismissible'] = false;
        $_SESSION['response']['message']['message'] = 'Falsches Kennwort oder Benutzername';

        if (empty($dataInputGet)) {
            header('Location: ../login.php');
        } else {
            $dataInputGet;
            header('Location: ../login.php?' . http_build_query($dataInputGet));
        }
        exit();
    } else {
        // Abfrage in Array schreiben
        $dataDb = mysqli_fetch_assoc($result);

        // Passwort validieren
        if (!password_verify($dataInput['password'], $dataDb['password'])) {
            // Rückmeldung und Weiterleitung
            $_SESSION['response']['alert']['alertType'] = 'danger';
            $_SESSION['response']['alert']['alertDismissible'] = false;
            $_SESSION['response']['message']['message'] = 'Falsches Kennwort oder Benutzername';
            
            if (empty($dataInputGet)) {
                header('Location: ../login.php');
            } else {
                $dataInputGet;
                header('Location: ../login.php?' . http_build_query($dataInputGet));
            }
        } else {
            // Benutzerdaten in Session schreiben
            $_SESSION['userID'] = intval($dataDb['userID']);
            $_SESSION['username'] = $dataDb['username'];

            // Überprüfen ob Benutzer eine Standarddatenbank hat
            if (!$dataInputGet['forceDatabaseSelect']) {
                // SQL-Query bereitstellen
                $sqlquery = "SELECT `defaultDb` FROM `userconfig` WHERE `userID` = " . $dataDb['userID'];
                $result = mysqli_query($config['link'], $sqlquery);

                if (mysqli_num_rows($result) == 1) {
                    // Abfrage in Array schreiben
                    $dataDb = mysqli_fetch_assoc($result);

                    if (!empty($dataDb['defaultDb'])) {
                        // Datenbank ID in Session schreiben
                        $_SESSION['userDb']['dbID'] = intval($dataDb['defaultDb']);
                        $_SESSION['userDb']['userDbSet'] = 1;

                        // Mit Ziel Datenbank verbinden
                        if (include_once 'userDbConnect.inc.php') {
                            // Fällige Daueraufträge prüfen
                            include 'standingOrderCheck.inc.php';
                        }

                        // Weiterleitung
                        if (empty($dataInputGet['rd'])) {
                            header('Location: ../index.php');
                        } else {
                            $rd = $dataInputGet['rd'];
                            header('Location: ../' . $rd);
                        }
                        exit();
                    }
                }
            }
            // Benutzerdatenbank
            $_SESSION['userDb']['userDbSet'] = 0;

            // Weiterleitung
            if (empty($dataInputGet['rd'])) {
                header('Location: ../selectDatabase.php');
            } else {
                $rd = $dataInputGet['rd'];
                header('Location: ../selectDatabase.php?rd=' . urlencode($rd));
            }
            exit();
        }
    }
} else {
    http_response_code(405);
    header ('Allow: POST');
}
?>