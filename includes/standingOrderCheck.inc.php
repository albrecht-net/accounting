<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {
    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
    }
    
    require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

    // Cookie Reload auf folgenden Seiten erzwingen
    $sites = array(
        'entry.php'
    );

    $forceReload = in_array(end(explode('/', $_SERVER['PHP_SELF'])), $sites);

    // Anzahl fälliger Daueraufträge ermitteln und in Cookie zwischenspeichern
    if ($_SESSION['userDb']['userDbSet'] == 1 && (!isset($_COOKIE['standingOrder']) || $forceReload || (json_decode($_COOKIE['standingOrder'], TRUE)['userID'] != intval($_SESSION['userID'])) || (json_decode($_COOKIE['standingOrder'], TRUE)['dbID'] != intval($_SESSION['userDb']['dbID'])))) {
        $sqlquery = "SELECT `standingOrderID` FROM `standingOrder` WHERE `nextExecutionDate` <= NOW() AND `closed` = 'N'";

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
} else {
    http_response_code(204);
}
?>