<?php
// Array Eingabe
$dataInput = array(
    'datum' => mysqli_real_escape_string($userLink, $_POST['datum']),
    'empfänger' => intval($_POST['empfänger']),
    'reNummer' => mysqli_real_escape_string($userLink, trim($_POST['reNummer'])),
    'buchungstext' => mysqli_real_escape_string($userLink, trim($_POST['buchungstext'])),
    'totalbetrag' => floatval($_POST['totalbetrag']),
    'kontoSoll' => intval($_POST['kontoSoll']),
    'kontoHaben' => intval($_POST['kontoHaben']),
    'periode' => intval($_POST['periode']),
    'klassifikation1' => intval($_POST['klassifikation1']),
    'klassifikation2' => intval($_POST['klassifikation2']),
    'klassifikation3' => intval($_POST['klassifikation3']),
    'abstimmung' => ($_POST['abstimmung'] == 1 ? 'Y' : 0)
);

$dataUpdateAbst = array(
    'buchungsreferenz' => (isset($_POST['buchungsreferenz']) ? array_map(intval, $_POST['buchungsreferenz']) : NULL)
);

// Leere Felder aus Eingabe Array entfernen
$dataInput = array_diff($dataInput, array(NULL, '', 0));

// Prüfen ob Eingabe vorhanden
if (count($dataInput) > 0) {
    $dataFunctions = array(
        'datumErstellt' => 'NOW()'
    );

    // SQL-Query bereitstellen
    $columns = "`" . implode("`, `", array_keys($dataInput)) . "`, `" . implode("`, `", array_keys($dataFunctions)) . "`";
    $values = "'" . implode("', '", $dataInput) . "', " . implode(", ", $dataFunctions);
    $sqlquery = "INSERT INTO `journal` (" . $columns . ") VALUES (" . $values . ")";

    // SQL-Query ausführen und überprüfen
    if (!mysqli_query($userLink, $sqlquery)) {
        $msg['sqlInsertError'] = 1;

    // Prüfen ob Abstimmung gewählt
    } elseif (count($dataUpdateAbst['buchungsreferenz']) > 0) {
        // ID der erstellten Buchung abrufen
        $refID = mysqli_insert_id($userLink);

        // SQL-Query bereitstellen
        $sqlquery = "UPDATE `journal` SET `journal`.`buchungsreferenz` = ". $refID .", `journal`.`abstimmung` = 'Y' WHERE `journal`.`buchungID` IN (" . implode(',', $dataUpdateAbst['buchungsreferenz']) . ") AND `journal`.`abstimmung` = 'N'";

        // SQL-Query ausführen und überprüfen
        if (!mysqli_query($userLink, $sqlquery)) {
            $msg['sqlUpdateError'] = 1;
        } else {
            $msg['success'] = 1;
        }
    } else {
        $msg['success'] = 1;
    }
} else {
    $msg['noInput'] = 1;
}
?>