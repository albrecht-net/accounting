<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {
    // Leere Felder aus Eingabe Array entfernen
    $dataInput['input'] = array_diff($dataInput['input'], array(NULL, '', 0));
    $dataInput = array_diff($dataInput, array(NULL, '', 0));

    // Prüfen ob Eingabe vorhanden
    if (count($dataInput['input']) > 0) {
        switch ($dataInput['templateType']) {
            case (1): // Speichern in Applikation
                unset($dataInput['templateType']);

                $dataFunctions = array(
                    'created' => 'NOW()'
                );

                $dataInput = array_merge($dataInput, $dataInput['input']);
                unset($dataInput['input']);

                // SQL-Query bereitstellen
                $columns = "`" . implode("`, `", array_keys($dataInput)) . "`, `" . implode("`, `", array_keys($dataFunctions)) . "`";
                $values = "'" . implode("', '", $dataInput) . "', " . implode(", ", $dataFunctions);
                $sqlquery = "INSERT INTO `template` (" . $columns . ") VALUES (" . $values . ")";

                // SQL-Query ausführen und überprüfen
                if (!mysqli_query($userLink, $sqlquery)) {
                    $_SESSION['response']['alert']['alertType'] = 'danger';
                    $_SESSION['response']['alert']['alertDismissible'] = true;
                    $_SESSION['response']['message']['message'] = '<strong>MySQL Error:</strong> ' . mysqli_error($userLink);
                    header('Location: ../entry.php');
                }
                $_SESSION['response']['alert']['alertType'] = 'primary';
                $_SESSION['response']['alert']['alertDismissible'] = true;
                $_SESSION['response']['message']['message'] = 'Vorlage erfolgreich gespeichert';
                header('Location: ../entry.php');
                break;
            case (2): // Als Link ausgeben
                $_SESSION['response']['alert']['alertType'] = 'primary';
                $_SESSION['response']['alert']['alertDismissible'] = true;
                $_SESSION['response']['message']['messageTitle'] = 'Vorlage als Lesezeichen';
                $_SESSION['response']['message']['message'] = "<p>Um Fehler zu vermeiden, sollte diese Vorlage nur mit der aktuell gewählten Zieldatenbank genutzt werden. Der untenstehende Link kann nun als Lesezeichen dem Browser hinzugefügt werden:</p><hr><a href=\"entry.php?" . http_build_query($dataInput['input']) . "\" class=\"alert-link\">" . (!empty($msg['templateURL']['name']) ? $msg['templateURL']['name'] : 'Buchungs-Vorlage') . "</a>";
                header('Location: ../entry.php');
                break;
        }
    } else {
        $_SESSION['response']['alert']['alertType'] = 'warning';
        $_SESSION['response']['alert']['alertDismissible'] = true;
        $_SESSION['response']['message']['message'] = 'Bitte wählen Sie mindestens 1 Feld aus, welches in der Vorlage gespeichert werden soll.';
        header('Location: ../entry.php');
    }
} else {
    http_response_code(204);
}
?>