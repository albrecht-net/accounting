<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
    }
    
    require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

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
        'username' => db::init(1)->escapeString($_POST['inputUsername']),
        'password' => $_POST['inputPassword']
    );

    // Array GET-Variablen
    $dataInputGet = $_GET;

    $sqlquery = "SELECT `username`, `password`, `userID` FROM `users` WHERE `username` = '" . $dataInput['username'] . "' AND `activation` = 'Y' AND `status` = 'Y'";
    db::init(1)->query($sqlquery);

    // Benutzer abfragen
    if (db::init(1)->count() != 1) {
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
        $dataDb = db::init(1)->first();

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
            // Benutzerdaten in Session schreibenuser23rearID
            session::put('userID', intval($dataDb['userID']));
            session::put('username', $dataDb['username']);

            // Reponse-Data aus Session löschen
            session::delete('response');

            // Überprüfen ob Benutzer eine Standarddatenbank hat
            if (!$dataInputGet['forceDatabaseSelect']) {
                $sqlquery = "SELECT `defaultDb` FROM `userconfig` WHERE `userID` = " . $dataDb['userID'];
                db::init(1)->query($sqlquery);

                if (db::init(1)->count() == 1) {
                    
                    // Abfrage in Array schreiben
                    $dataDb = db::init(1)->first();
                    
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
            session::put('userDbSet', false);

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