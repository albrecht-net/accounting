<?php
// Array Eingabe
$dataInput = array(
    'validDb' => intval($_SESSION['userDb']['dbID']),
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
    'favoriteName' => trim($_POST['nameFavorite'])
);

switch (intval($_POST['radioFavorite'])) {

    case (1): // Speichern in Applikation
        var_dump($dataInput);
        echo '<br>';
        $json = json_encode($dataInput);
        var_dump($json);
        echo '<br>';
        var_dump(json_decode($json, TRUE));
        exit;

    case (2): // Als Link ausgeben
    ?>
    <a href="buchung.php?<?php echo(http_build_query($dataInput)); ?>">Link</a>
    <?php
        
    break;
}
exit();
?>