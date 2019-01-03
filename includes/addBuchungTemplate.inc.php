<?php
// Array Eingabe
$dataInput = array(
    'input' => array(
        'recipient' => intval($_POST['recipient']),
        'invoiceNo' => mysqli_real_escape_string($userLink, trim($_POST['invoiceNo'])),
        'entryText' => mysqli_real_escape_string($userLink, trim($_POST['entryText'])),
        'grandTotal' => floatval($_POST['grandTotal']),
        'debitAccount' => intval($_POST['debitAccount']),
        'creditAccount' => intval($_POST['creditAccount']),
        'period' => intval($_POST['period']),
        'classification1' => intval($_POST['classification1']),
        'classification2' => intval($_POST['classification2']),
        'classification3' => intval($_POST['classification3'])
    ),
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
                $msg['sqlInsertError'] = 1;
            }
            $msg['templateSuccess'] = 1;
            break;
        case (2): // Als Link ausgeben
            $msg['templateURL']['set'] = 1;
            $msg['templateURL']['name'] = $dataInput['name'];
            $msg['templateURL']['data'] = $dataInput['input'];
            $msg['templateURL']['data']['dbID'] = $_SESSION['userDb']['dbID'];
            break;
    }
} else {
    $msg['noTemplateInput'] = 1;
}
?>