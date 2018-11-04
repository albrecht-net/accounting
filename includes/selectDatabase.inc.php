<?php
// Array Eingabe
$dataInput = array(
    'dbID' => intval($_POST['dbID']),
    'saveDbSelection' => boolval($_POST['saveDbSelection'])
);

// SQL-Query bereitstellen
$sqlquery = "SELECT `dbID` FROM `databases` WHERE `dbID` = " . $dataInput['dbID'] . " AND `userID` = " . intval($_SESSION['userID']);
$result = mysqli_query($config['link'], $sqlquery);

// Prüfen ob nur 1 Resultat
if (mysqli_num_rows($result) != 1) {
    $dataInput['invalid'] = 1;
} else {
    // Abfrage in Array schreiben
    $dataDb = mysqli_fetch_assoc($result);

    // Datenbank ID in Session schreiben
    $_SESSION['userDb']['dbID'] = $dataDb['dbID'];

    $_SESSION['userDb']['userDbSet'] = 1;

    // Datenbankauswahl speichern
    if ($dataInput['saveDbSelection']) {
        $sqlquery ="UPDATE `userconfig` SET `defaultDb` = " . $dataDb['dbID'] . " WHERE `userID` = " . intval($_SESSION['userID']);
        
        // SQL-Query ausführen und überprüfen
        if (!mysqli_query($config['link'], $sqlquery)) {
            echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
            exit();
        }
    }

    // Weiterleitung
    if (empty($_GET['rd'])) {
        header('Location: index.php');
    } else {
        $rd = $_GET['rd'];
        header('Location: ' . $rd);
    }
    exit();
}
?>