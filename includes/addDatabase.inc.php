<?php
// Array Eingabe
$dataInput = array(
    'dbHost' => mysqli_real_escape_string($config['link'], trim(strtolower($_POST['dbHost']))),
    'dbPort' => intval($_POST['dbPort']),
    'dbUsername' => mysqli_real_escape_string($config['link'], trim(strtolower($_POST['dbUsername']))),
    'dbPassword' => $_POST['dbPassword'],
    'dbName' => mysqli_real_escape_string($config['link'], trim($_POST['dbName'])),
    'userID' => intval($_SESSION['userID'])
);

// Mit der Datenbank verbinden
$tempLink = mysqli_connect($dataInput['dbHost'] . ':' . $dataInput['dbPort'], $dataInput['dbUsername'], $dataInput['dbPassword'], $dataInput['dbName']);

// Verbindung überprüfen
if (!$tempLink) {
    $msg['tempLinkError'] = 1;
} else {
    $dataFunctions = array(
        'datumErstellt' => 'NOW()'
    );

    // Datenbankangaben speichern
    $columns = "`" . implode("`, `", array_keys($dataInput)) . "`, `" . implode("`, `", array_keys($dataFunctions)) . "`";
    $values = "'" . implode("', '", $dataInput) . "', " . implode(", ", $dataFunctions);
    $sqlquery = "INSERT INTO `databases` (" . $columns . ") VALUES (" . $values . ")";

    // SQL-Query ausführen und überprüfen
    if (!mysqli_query($config['link'], $sqlquery)) {
        echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
        exit();
    }

    // Temporäre Datenbankverbindung schliessen
    mysqli_close($tempLink);

    $msg['successAddDb'] = 1;
}
?>