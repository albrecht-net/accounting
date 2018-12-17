<?php
// Anzahl fälliger Daueraufträge ermitteln und in Cookie zwischenspeichern
if (!isset($_COOKIE['standingOrder']) || (json_decode($_COOKIE['standingOrder'], TRUE)['userID'] != intval($_SESSION['userID'])) || (json_decode($_COOKIE['standingOrder'], TRUE)['dbID'] != intval($_SESSION['userDb']['dbID']))) {
    $sqlquery = "SELECT `standingOrderID` FROM `standingOrder` WHERE `nextExecutionDate` <= NOW()";

    $values = array(
        'userID' => intval($_SESSION['userID']),
        'dbID' => intval($_SESSION['userDb']['dbID']),
        'count' => mysqli_num_rows(mysqli_query($userLink, $sqlquery))
    );

    $datePlus = strtotime('+8 hour');
    $dateTomorrow = strtotime('tomorrow');

    $json = json_encode($values, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);

    // Setzt das Abblaufdatum des Cokkie auf +8h, ausser es überschreitet 00:00 Uhr vom nächsten Tag
    if ($datePlus < $dateTomorrow) {
        setcookie('standingOrder', $json, $datePlus, '/');
    } else {
        setcookie('standingOrder', $json, $dateTomorrow, '/');
    }

    // Wert für Direktzugriff bereitstellen
    $_COOKIE['standingOrder'] = $json;
}
?>