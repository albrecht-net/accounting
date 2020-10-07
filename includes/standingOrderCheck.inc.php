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

    $forceReload = in_array(basename($_SERVER['PHP_SELF']), $sites);

    // Anzahl fälliger Daueraufträge ermitteln und in Cookie zwischenspeichern
    if (session::get('userDbSet') == 1 && (!isset($_COOKIE['standingOrder']) || $forceReload || (json_decode($_COOKIE['standingOrder'], TRUE)['userID'] != intval(session::get('userID'))) || (json_decode($_COOKIE['standingOrder'], TRUE)['dbID'] != intval(session::get('userDbID'))))) {
        $sqlquery = "SELECT `standingOrderID` FROM `standingOrder` WHERE `nextExecutionDate` <= NOW() AND `closed` = 'N'";

        db::init(2)->query($sqlquery); 

        $values = array(
            'userID' => intval(session::get('userID')),
            'dbID' => intval(session::get('userDbID')),
            'count' => db::init(2)->count()
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