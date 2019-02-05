<?php
// Mit Ziel Datenbank verbinden
require_once 'userDbConnect.inc.php';

// Leere Felder aus Eingabe Array entfernen
$dataInput = array_diff($dataInput, array(NULL, '', 0));

// Prüfen ob Eingabe vorhanden
if (count($dataInput) > 0) {
    // 
    // Dauerauftrag prüfen und speichern
    // 

    // Prüfen ob Dauerauftrag aktiv
    if (intval($_SESSION['standingOrder']['standingOrderSet']) == 1) {
        
        // SQL-Query bereitstellen
        $sqlquery = "SELECT `validFromType`, `periodicityType`, `periodicityValue`, `validToType`, `validToValue`, `remainingEvents`, `nextExecutionDate` FROM `standingOrder` WHERE `standingOrderID` = " . intval($_SESSION['standingOrder']['standingOrderID']) . " AND `nextExecutionDate` <= NOW() AND `closed` = 'N'";
        $result = mysqli_query($userLink, $sqlquery);

        // Prüfen ob Datensätze vorhanden
        if (mysqli_num_rows($result) < 1) {
            unset($_SESSION['standingOrder']);
            $_SESSION['response']['alert']['alertType'] = 'warning';
            $_SESSION['response']['alert']['alertDismissible'] = true;
            $_SESSION['response']['message']['message'] = 'Dieser Dauerauftrag ist nicht gültig. Die Buchung wurde nicht gespeichert. <strong>MySQL Error:</strong>' . mysqli_error($userLink);
            header('Location: ../buchung.php');
            exit();
        } else {
            // Abfrage in Array schreiben
            $dataDb = mysqli_fetch_assoc($result);
    
            // Leere Felder aus valueTemplate Array entfernen
            $dataDb = array_diff($dataDb, array(NULL, '', 0));

            $dataUpdateFunctions['updated'] = 'NOW()';

            // Nächstes Ausführdatum festlegen
            switch ($dataDb['periodicityType']) {
                case 1: // Tag
                    $dataUpdate['nextExecutionDate'] = date_format(date_modify(date_create($dataDb['nextExecutionDate']), $dataDb['periodicityValue'] . ' day'), 'Y-m-d');
                    break;
                case 2: // Woche
                    $dataUpdate['nextExecutionDate'] = date_format(date_modify(date_create($dataDb['nextExecutionDate']), $dataDb['periodicityValue'] . ' week'), 'Y-m-d');
                    break;
                case 4: // Monat
                    if ($dataDb['validFromType'] == 1) {
                        $dataUpdate['nextExecutionDate'] = date_format(date_modify(date_create($dataDb['nextExecutionDate']), $dataDb['periodicityValue'] . ' month'), 'Y-m-d');
                    } elseif ($dataDb['validFromType'] == 2) {
                        $dataUpdate['nextExecutionDate'] = date_format(date_modify(date_create(date_format(date_create($dataDb['nextExecutionDate']), 'Y-m')), $dataDb['periodicityValue'] . ' month'), 'Y-m-t');
                    }
                    break;
                case 8: // Jahr
                    if ($dataDb['validFromType'] == 1) {
                        $dataUpdate['nextExecutionDate'] = date_format(date_modify(date_create($dataDb['nextExecutionDate']), $dataDb['periodicityValue'] . ' year'), 'Y-m-d');
                    } elseif ($dataDb['validFromType'] == 2) {
                        $dataUpdate['nextExecutionDate'] = date_format(date_modify(date_create(date_format(date_create($dataDb['nextExecutionDate']), 'Y-m')), $dataDb['periodicityValue'] . ' year'), 'Y-m-t');
                    }
                    break;
            }
            
            // Auf Abschluss prüfen, wenn gültig bis Widerruf dann keine Überprüfung
            if ($dataDb['validToType'] == 1) { // Kein Enddatum
                // Event Counter
                $dataUpdateFunctions['handledEvents'] = 'handledEvents +1';
            } elseif ($dataDb['validToType'] == 2) { // Enddatum
                // Event Counter
                $dataUpdateFunctions['handledEvents'] = 'handledEvents +1';

                if ($dataDb['validToValue'] < $dataDb['nextExecutionDate']) {
                    unset($dataUpdate['nextExecutionDate']);
                    $dataUpdateFunctions['nextExecutionDate'] = 'NULL';
                    $dataUpdate['closed'] = 'Y';
                }
            } elseif ($dataDb['validToType'] == 4) { // Anzahl Wiederholungen
                // Event Counter
                $dataUpdateFunctions['handledEvents'] = 'handledEvents +1';
                $dataUpdate['remainingEvents'] = $dataDb['remainingEvents'] - 1;

                if ($dataUpdate['remainingEvents'] == 0) {
                    unset($dataUpdate['nextExecutionDate']);
                    $dataUpdateFunctions['nextExecutionDate'] = 'NULL';
                    $dataUpdate['closed'] = 'Y';
                }
            }

            // SQL-Query bereitstellen
            $set = [];
            foreach ($dataUpdate as $column => $value) {
                $set[] = "`" . $column . "` = '" . $value . "'";
            }
            foreach ($dataUpdateFunctions as $column => $value) {
                $set[] = "`" . $column . "` = " . $value;
            }
            $sqlquery = "UPDATE `standingOrder` SET " . implode(", ", $set) . " WHERE `standingOrderID` = " . intval($_SESSION['standingOrder']['standingOrderID']) . " AND `nextExecutionDate` <= NOW() AND `closed` = 'N'";

            // SQL-Query ausführen und überprüfen
            if (!mysqli_query($userLink, $sqlquery)) {
                unset($_SESSION['standingOrder']);
                $_SESSION['response']['alert']['alertType'] = 'danger';
                $_SESSION['response']['alert']['alertDismissible'] = true;
                $_SESSION['response']['message']['message'] = 'Es trat ein Fehler beim Verarbeiten des Dauerauftrags auf. Die Buchung wurde nicht gespeichert. <strong>MySQL Error:</strong>' . mysqli_error($userLink);
                header('Location: ../buchung.php');
                exit();
            } else {
                unset($_SESSION['standingOrder']);
                // Dauerauftrag erfolgreich gespeichert
            }
        }
    }
    
    // 
    // Buchung speichern
    // 

    $dataFunctions = array(
        'created' => 'NOW()'
    );

    // SQL-Query bereitstellen
    $columns = "`" . implode("`, `", array_keys($dataInput)) . "`, `" . implode("`, `", array_keys($dataFunctions)) . "`";
    $values = "'" . implode("', '", $dataInput) . "', " . implode(", ", $dataFunctions);
    $sqlquery = "INSERT INTO `journal` (" . $columns . ") VALUES (" . $values . ")";

    // SQL-Query ausführen und überprüfen
    if (!mysqli_query($userLink, $sqlquery)) {
        $_SESSION['response']['alert']['alertType'] = 'danger';
        $_SESSION['response']['alert']['alertDismissible'] = true;
        $_SESSION['response']['message']['message'] = '<strong>MySQL Error:</strong>' . mysqli_error($userLink);
        header('Location: ../buchung.php');
        exit();

    // Prüfen ob Abstimmung gewählt
    } elseif (count($dataUpdateAbst['entryReference']) > 0) {
        // ID der erstellten Buchung abrufen
        $refID = mysqli_insert_id($userLink);

        // SQL-Query bereitstellen
        $sqlquery = "UPDATE `journal` SET `journal`.`entryReference` = ". $refID .", `journal`.`reconcilation` = 'Y' WHERE `journal`.`entryID` IN (" . implode(',', $dataUpdateAbst['entryReference']) . ") AND `journal`.`reconcilation` = 'N'";

        // SQL-Query ausführen und überprüfen
        if (!mysqli_query($userLink, $sqlquery)) {
            $_SESSION['response']['alert']['alertType'] = 'danger';
            $_SESSION['response']['alert']['alertDismissible'] = true;
            $_SESSION['response']['message']['message'] = 'Die neue Buchung konnte erfolgreich in der Datenbank gespeichert werden. Es trat jedoch ein Fehler beim Updaten der Abstimmung auf! <strong>MySQL Error:</strong>' . mysqli_error($userLink);
            header('Location: ../buchung.php');
            exit();
        } else {
            $_SESSION['response']['alert']['alertType'] = 'primary';
            $_SESSION['response']['alert']['alertDismissible'] = true;
            $_SESSION['response']['message']['message'] = 'Eintrag erfolgreich gespeichert';
            header('Location: ../buchung.php');
            exit();
        }
    } else {
        $_SESSION['response']['alert']['alertType'] = 'primary';
        $_SESSION['response']['alert']['alertDismissible'] = true;
        $_SESSION['response']['message']['message'] = 'Eintrag erfolgreich gespeichert';
        header('Location: ../buchung.php');
        exit();
    }

} else {
    $_SESSION['response']['alert']['alertType'] = 'warning';
    $_SESSION['response']['alert']['alertDismissible'] = true;
    $_SESSION['response']['message']['message'] = 'Bitte Pflichtfelder ausfüllen!';
    header('Location: ../buchung.php');
    exit();
}
?>