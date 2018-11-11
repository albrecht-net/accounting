<?php
// Array Eingabe
$dataInput = array(
    'empfänger' => intval($_POST['empfänger']),
    'reNummer' => trim($_POST['reNummer']),
    'buchungstext' => trim($_POST['buchungstext']),
    'totalbetrag' => floatval($_POST['totalbetrag']),
    'kontoSoll' => intval($_POST['kontoSoll']),
    'kontoHaben' => intval($_POST['kontoHaben']),
    'periode' => intval($_POST['periode']),
    'klassifikation1' => intval($_POST['klassifikation1']),
    'klassifikation2' => intval($_POST['klassifikation2']),
    'klassifikation3' => intval($_POST['klassifikation3'])
);

$dataFavorite = array(
    'templateUserdDb' => intval($_SESSION['userDb']['dbID']),
    'favoriteName' => trim($_POST['nameFavorite'])
);

// Leere Felder aus Eingabe Array entfernen
$dataInput = array_diff($dataInput, array(NULL, '', 0));
$dataFavorite = array_diff($dataFavorite, array(NULL, '', 0));

// Prüfen ob Eingabe vorhanden
if (count($dataInput) > 0) {
    switch (intval($_POST['radioFavorite'])) {
        case (1): // Speichern in Applikation
            var_dump($dataInput);
            echo '<br>';
            $json = json_encode($dataInput);
            var_dump($json);
            echo '<br>';
            var_dump(json_decode($json, TRUE));
            exit;
            break;
        case (2): // Als Link ausgeben
            $msg['favoriteURL']['set'] = 1;
            $msg['favoriteURL']['name'] = $dataFavorite['favoriteName'];
            $msg['favoriteURL']['data'] = array_merge($dataFavorite['templateUserDb'], $dataInput);
            break;
    }
} else {
    $msg['noInput'] = 1;
}
?>