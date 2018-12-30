<?php
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

    do {
        // Erstes (nächstes) Ausführdatum festlegen
        if ($dataInput['validFromType'] == 1) { // Nutze Startdatum
            $dataInput['nextExecutionDate'] = $dataInput['validFromValue'];
        } elseif ($dataInput['validFromType'] == 2) { // Nutze Monatsende
            $dataInput['nextExecutionDate'] = date_format(date_create($dataInput['validFromValue']), 'Y-m-t');
        } else {
            $msg['inputError'] = 1;
            break;
        }

        // Enddatum festlegen
        if ($dataInput['validToType'] == 1) { // Kein Enddatum
            unset($dataInput['validToValue']);
        } elseif ($dataInput['validToType'] == 2) { // Nutze Enddatum
            if ($dataInput['validToValue'] <= $dataInput['nextExecutionDate']) {
                break;
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
                    $dataInput['validToValue'] = date_format(date_modify(date_create($dataInput['nextExecutionDate']), $dataInput['periodicityValue'] * ($dataInput['initialEvents'] - 1) . ' month'), 'Y-m-d');
                    break;
                case 8: // Jahr
                    $dataInput['validToValue'] = date_format(date_modify(date_create($dataInput['nextExecutionDate']), $dataInput['periodicityValue'] * ($dataInput['initialEvents'] - 1) . ' year'), 'Y-m-d');
                    break;
                default:
                    $msg['inputError'];
                    break 2;
            }
        } else {
            $msg['inputError'] = 1;
            break;
        }

        // SQL-Query bereitstellen
        $columns = "`" . implode("`, `", array_keys($dataInput)) . "`, `" . implode("`, `", array_keys($dataFunctions)) . "`";
        $values = "'" . implode("', '", $dataInput) . "', " . implode(", ", $dataFunctions);
        $sqlquery = "INSERT INTO `standingOrder` (" . $columns . ") VALUES (" . $values . ")";

        // SQL-Query ausführen und überprüfen
        if (!mysqli_query($userLink, $sqlquery)) {
            $msg['sqlInsertError'] = 1;

        } else {
            $msg['success'] = 1;
        }
    } while (0);
} else {
    $msg['noInput'] = 1;
}
?>