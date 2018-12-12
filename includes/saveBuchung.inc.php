<?php
// Array Eingabe
$dataInput = array(
    'date' => mysqli_real_escape_string($userLink, $_POST['date']),
    'recipient' => intval($_POST['recipient']),
    'invoiceNo' => mysqli_real_escape_string($userLink, trim($_POST['invoiceNo'])),
    'entryText' => mysqli_real_escape_string($userLink, trim($_POST['entryText'])),
    'grandTotal' => floatval($_POST['grandTotal']),
    'debitAccount' => intval($_POST['debitAccount']),
    'creditAccount' => intval($_POST['creditAccount']),
    'period' => intval($_POST['period']),
    'classification1' => intval($_POST['classification1']),
    'classification2' => intval($_POST['classification2']),
    'classification3' => intval($_POST['classification3']),
    'reconcilation' => ($_POST['reconcilation'] == 1 ? 'Y' : 0)
);

$dataUpdateAbst = array(
    'entryReference' => (isset($_POST['entryReference']) ? array_map(intval, $_POST['entryReference']) : NULL)
);

// Leere Felder aus Eingabe Array entfernen
$dataInput = array_diff($dataInput, array(NULL, '', 0));

// Prüfen ob Eingabe vorhanden
if (count($dataInput) > 0) {
    $dataFunctions = array(
        'created' => 'NOW()'
    );

    // SQL-Query bereitstellen
    $columns = "`" . implode("`, `", array_keys($dataInput)) . "`, `" . implode("`, `", array_keys($dataFunctions)) . "`";
    $values = "'" . implode("', '", $dataInput) . "', " . implode(", ", $dataFunctions);
    $sqlquery = "INSERT INTO `journal` (" . $columns . ") VALUES (" . $values . ")";

    // SQL-Query ausführen und überprüfen
    if (!mysqli_query($userLink, $sqlquery)) {
        $msg['sqlInsertError'] = 1;

    // Prüfen ob Abstimmung gewählt
    } elseif (count($dataUpdateAbst['entryReference']) > 0) {
        // ID der erstellten Buchung abrufen
        $refID = mysqli_insert_id($userLink);

        // SQL-Query bereitstellen
        $sqlquery = "UPDATE `journal` SET `journal`.`entryReference` = ". $refID .", `journal`.`reconcilation` = 'Y' WHERE `journal`.`entryID` IN (" . implode(',', $dataUpdateAbst['entryReference']) . ") AND `journal`.`reconcilation` = 'N'";

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