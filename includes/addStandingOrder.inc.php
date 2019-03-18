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
        'template' => intval($_POST['template']),
        'label' => mysqli_real_escape_string($userLink, trim($_POST['label'])),
        'validFromType' => intval($_POST['validFromType']),
        'validFromValue' => mysqli_real_escape_string($userLink, $_POST['validFromValue']),
        'periodicityType' => intval($_POST['periodicityType']),
        'periodicityValue' => intval($_POST['periodicityValue']),
        'validToType' => intval($_POST['validToType']),
        'validToValue' => mysqli_real_escape_string($userLink, $_POST['validToValue']),
        'initialEvents' => intval($_POST['initialEvents']),
        'remainingEvents' => intval($_POST['initialEvents'])
    );
    // Leere Felder aus Eingabe Array entfernen
    $dataInput = array_diff($dataInput, array(NULL, '', 0));

    // Prüfen ob Eingabe vorhanden
    if (count($dataInput) > 0) {
        $dataFunctions = array(
            'created' => 'NOW()',
            'updated' => 'NOW()'
        );

        // Eingabe-Kombinationen prüfen nach Wiederholungstyp
        switch ($dataInput['periodicityType']) {
            case 1: // Tag
                if (!in_array($dataInput['validFromType'], array(1)) || $dataInput['periodicityValue'] < 1) {
                    // Rückmeldung und Weiterleitung
                    $_SESSION['response']['alert']['alertType'] = 'warning';
                    $_SESSION['response']['message']['message'] = 'Ungültige Eingabe1.';
                    header('Location: ../standingOrder.php');
                    exit();
                }
                break;
            case 2: // Monat
                if (!in_array($dataInput['validFromType'], array(1)) || $dataInput['periodicityValue'] < 1) {
                    // Rückmeldung und Weiterleitung
                    $_SESSION['response']['alert']['alertType'] = 'warning';
                    $_SESSION['response']['message']['message'] = 'Ungültige Eingabe';
                    header('Location: ../standingOrder.php');
                    exit();
                }
                break;
            case 4: // Monat
                if (!in_array($dataInput['validFromType'], array(1, 2)) || $dataInput['periodicityValue'] < 1) {
                    // Rückmeldung und Weiterleitung
                    $_SESSION['response']['alert']['alertType'] = 'warning';
                    $_SESSION['response']['message']['message'] = 'Ungültige Eingabe';
                    header('Location: ../standingOrder.php');
                    exit();
                }
                break;
            case 8: // Jahr
                if (!in_array($dataInput['validFromType'], array(1, 2)) || $dataInput['periodicityValue'] < 1) {
                    // Rückmeldung und Weiterleitung
                    $_SESSION['response']['alert']['alertType'] = 'warning';
                    $_SESSION['response']['message']['message'] = 'Ungültige Eingabe';
                    header('Location: ../standingOrder.php');
                    exit();
                }
                break;
            case 16: // Montag - Freitag
                $dataInput['periodicityValue'] = 1;
                if (!in_array($dataInput['validFromType'], array(1))) {
                    // Rückmeldung und Weiterleitung
                    $_SESSION['response']['alert']['alertType'] = 'warning';
                    $_SESSION['response']['message']['message'] = 'Ungültige Eingabe';
                    header('Location: ../standingOrder.php');
                    exit();
                }
                break;
            default:
                // Rückmeldung und Weiterleitung
                $_SESSION['response']['alert']['alertType'] = 'warning';
                $_SESSION['response']['message']['message'] = 'Ungültige Eingabe';
                header('Location: ../standingOrder.php');
                exit();
        }

        // Erstes (nächstes) Ausführdatum festlegen
        if ($dataInput['validFromType'] == 1) { // Nutze Startdatum
            if ($dataInput['periodicityType'] == 16) { // Wenn Montag - Freitag Startdatum auf Wochentag verschieben
                if (date('N', strtotime($dataInput['validFromValue'])) > 5) {
                    $dataInput['nextExecutionDate'] = date_format(date_modify(date_create($dataInput['validFromValue']), 'next monday'), 'Y-m-d');
                } else {
                    $dataInput['nextExecutionDate'] = $dataInput['validFromValue'];
                }
            } else {
                $dataInput['nextExecutionDate'] = $dataInput['validFromValue'];
            }
        } elseif ($dataInput['validFromType'] == 2) { // Nutze Monatsende
            $dataInput['nextExecutionDate'] = date_format(date_create($dataInput['validFromValue']), 'Y-m-t');
        }

        // Enddatum festlegen
        if ($dataInput['validToType'] == 1) { // Kein Enddatum
            unset($dataInput['validToValue']);
        } elseif ($dataInput['validToType'] == 2) { // Nutze Enddatum
            if ($dataInput['validToValue'] < $dataInput['nextExecutionDate']) {
                // Rückmeldung und Weiterleitung
                $_SESSION['response']['alert']['alertType'] = 'warning';
                $_SESSION['response']['message']['message'] = 'Das Startdatum muss vor das Enddatum gesetzt werden';
                header('Location: ../standingOrder.php');
                exit();
            }
        } elseif ($dataInput['validToType'] == 4) { // Anzahl Wiederholungen
            switch ($dataInput['periodicityType']) {
                case 1: // Tag
                    $dataInput['validToValue'] = date_format(date_modify(date_create($dataInput['nextExecutionDate']), $dataInput['periodicityValue'] * ($dataInput['initialEvents'] - 1) . ' day'), 'Y-m-d');
                    break;
                case 2: // Woche
                    $dataInput['validToValue'] = date_format(date_modify(date_create($dataInput['nextExecutionDate']), $dataInput['periodicityValue'] * ($dataInput['initialEvents'] - 1) . ' week'), 'Y-m-d');
                    break;
                case 4: // Monat
                    if ($dataInput['validFromType'] == 1) {
                        $dataInput['validToValue'] = date_format(date_modify(date_create($dataInput['nextExecutionDate']), $dataInput['periodicityValue'] * ($dataInput['initialEvents'] - 1) . ' month'), 'Y-m-d');
                    } elseif ($dataInput['validFromType'] == 2) {
                        $dataInput['validToValue'] = date_format(date_modify(date_create(date_format(date_create($dataInput['nextExecutionDate']), 'Y-m')), $dataInput['periodicityValue'] * ($dataInput['initialEvents'] - 1) . ' month'), 'Y-m-t');
                    }
                    break;
                case 8: // Jahr
                    if ($dataInput['validFromType'] == 1) {
                        $dataInput['validToValue'] = date_format(date_modify(date_create($dataInput['nextExecutionDate']), $dataInput['periodicityValue'] * ($dataInput['initialEvents'] - 1) . ' year'), 'Y-m-d');
                    } elseif ($dataInput['validFromType'] == 2) {
                        $dataInput['validToValue'] = date_format(date_modify(date_create(date_format(date_create($dataInput['nextExecutionDate']), 'Y-m')), $dataInput['periodicityValue'] * ($dataInput['initialEvents'] - 1) . ' year'), 'Y-m-t');
                    }
                    break;
                case 16: // Montag - Freitag
                    $dataInput['validToValue'] = date_format(date_modify(date_create($dataInput['nextExecutionDate']), $dataInput['periodicityValue'] * ($dataInput['initialEvents'] - 1) . ' weekday'), 'Y-m-d');
                    break;
            }
        } else {
            // Rückmeldung und Weiterleitung
            $_SESSION['response']['alert']['alertType'] = 'warning';
            $_SESSION['response']['message']['message'] = 'Ungültige Eingabe';
            header('Location: ../standingOrder.php');
            exit();
        }

        // SQL-Query bereitstellen
        $columns = "`" . implode("`, `", array_keys($dataInput)) . "`, `" . implode("`, `", array_keys($dataFunctions)) . "`";
        $values = "'" . implode("', '", $dataInput) . "', " . implode(", ", $dataFunctions);
        $sqlquery = "INSERT INTO `standingOrder` (" . $columns . ") VALUES (" . $values . ")";

        // SQL-Query ausführen und überprüfen
        if (!mysqli_query($userLink, $sqlquery)) {
            // Rückmeldung und Weiterleitung
            $_SESSION['response']['alert']['alertType'] = 'danger';
            $_SESSION['response']['message']['message'] = '<strong>MySQL Error:</strong> ' . mysqli_error($userLink);
            header('Location: ../standingOrder.php');
            exit();
        } else {
            // Rückmeldung und Weiterleitung
            $_SESSION['response']['alert']['alertType'] = 'primary';
            $_SESSION['response']['message']['message'] = 'Dauerauftrag erfolgreich gespeichert';
            header('Location: ../standingOrder.php');
            exit();
        }

    } else {
        // Rückmeldung und Weiterleitung
        $_SESSION['response']['alert']['alertType'] = 'warning';
        $_SESSION['response']['message']['message'] = 'Keine Eingabe erfolgt';
        header('Location: ../standingOrder.php');
        exit();
    }
} else {
    http_response_code(405);
    header ('Allow: POST');
}
?>