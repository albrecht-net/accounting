<?php
// Array Eingabe
$dataInput = array(
    'input' => array(
        'empfänger' => intval($_POST['empfänger']),
        'reNummer' => mysqli_real_escape_string($userLink, trim($_POST['reNummer'])),
        'buchungstext' => mysqli_real_escape_string($userLink, trim($_POST['buchungstext'])),
        'totalbetrag' => floatval($_POST['totalbetrag']),
        'kontoSoll' => intval($_POST['kontoSoll']),
        'kontoHaben' => intval($_POST['kontoHaben']),
        'periode' => intval($_POST['periode']),
        'klassifikation1' => intval($_POST['klassifikation1']),
        'klassifikation2' => intval($_POST['klassifikation2']),
        'klassifikation3' => intval($_POST['klassifikation3'])
    ),
    'userID' => intval($_SESSION['userID']),
    'dbID' => intval($_SESSION['userDb']['dbID']),
    'name' => mysqli_real_escape_string($userLink, trim($_POST['nameTemplate']))
);

// Leere Felder aus Eingabe Array entfernen
$dataInput['input'] = array_diff($dataInput['input'], array(NULL, '', 0));
$dataInput = array_diff($dataInput, array(NULL, '', 0));

// Prüfen ob Eingabe vorhanden
if (count($dataInput['input']) > 0) {
    switch (intval($_POST['radioTemplate'])) {
        case (1): // Speichern in Applikation
            $dataFunctions = array(
                'datumErstellt' => 'NOW()'
            );

            // Input zu JSON
            $dataInput['value'] = json_encode($dataInput['input']);
            unset($dataInput['input']);

            var_dump($dataInput);


            // Datenbankangaben speichern
            $columns = "`" . implode("`, `", array_keys($dataInput)) . "`, `" . implode("`, `", array_keys($dataFunctions)) . "`";
            $values = "'" . implode("', '", $dataInput) . "', " . implode(", ", $dataFunctions);
            $sqlquery = "INSERT INTO `templates` (" . $columns . ") VALUES (" . $values . ")";

            // SQL-Query ausführen und überprüfen
            if (!mysqli_query($config['link'], $sqlquery)) {
                echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
                exit();
            }
            break;
        case (2): // Als Link ausgeben
            $msg['templateURL']['set'] = 1;
            $msg['templateURL']['name'] = $dataInput['name'];
            $msg['templateURL']['data'] = array_merge($dataInput['dbID'], $dataInput['input']);
            break;
    }
} else {
    $msg['noInput'] = 1;
}
?>