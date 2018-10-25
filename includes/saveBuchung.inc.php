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
    'buchunsreferenz' => intval($_POST['buchungsreferenz']),
    'abstimmung' => boolval($_POST['abstimmung'])
);

// Leere Felder aus Array entfernen
$dataInput = array_diff($dataInput, array(NULL, '', 0));

// Prüfen ob Eingabe vorhanden
if (count($dataInput) > 0) {
    $dataFunctions = array(
        'datumErstellt' => 'NOW()'
    );

    // SQL-Query bereitstellen
    $columns = "`" . implode("`, `", array_keys($dataInput)) . "`, `" . implode("`, `", array_keys($dataFunctions)) . "`";
    $values = "'" . implode("', '", $dataInput) . "', " . implode(", ", $dataFunctions);
    $sqlquery = "INSERT INTO `buchungen` (" . $columns . ") VALUES (" . $values . ")";

    // SQL-Query ausführen und überprüfen
    if (!mysqli_query($userLink, $sqlquery)) {
        echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($userLink);
        $msg['sqlError'] = 1;
        exit();
    } else {
        $msg['success'] = 1;
    }
} else {
    $msg['noInput'] = 1;
}
?>