<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Konfiguration einbinden
    require_once '../config.php';

    // Prüfen ob Benutzer angemeldet
    require 'loginSessionCheck.inc.php';
    if (!$lsc) {
        http_response_code(403);
        exit();
    }

    // Mit Ziel Datenbank verbinden
    require_once 'userDbConnect.inc.php';

    // Nummer-Formatierung
    require_once 'numberFormatter.inc.php';

    // Response-header
    header('Content-Type: application/json');

    // Array Response
    $jsResponse = array(
        'success' => false,
        'result' => false,
        'data' => array()
    );

    // Array Eingabe
    $dataInput = array(
        // Type 3: Loss, Type 4: Profit
        'type' => intval($_POST['type']),
        'period' => intval($_POST['period'])
    );
    // Leere Felder aus Eingabe Array entfernen
    $dataInput = array_diff($dataInput, array(NULL, '', 0));

    // Prüfen ob Eingabe vorhanden
    if (count($dataInput) == 2) {
        // Vordefinierter Zeitraum wenn Wert >0, sonst wenn <0 aus DB
        if ($dataInput['period'] > 0) {
            // Zeitraum festlegen
            switch ($dataInput['period']) {
                case 1: // Laufender Monat
                    $queryUseDate = true;
                    $dateFrom = date('Y-m-01', strtotime('now'));
                    $dateTo = date('Y-m-t', strtotime('now'));
                    break;
                case 2: // Laufendes Quartal
                    $queryUseDate = true;
                    $curMonth = strtolower(date('M', strtotime('now')));
                    if (in_array($curMonth, array('jan', 'feb', 'mar'))) {
                        $dateFrom = date('Y-m-01', strtotime('jan'));
                        $dateTo = date('Y-m-t', strtotime('mar'));
                        break;
                    } elseif (in_array($curMonth, array('apr', 'may', 'jun'))) {
                        $dateFrom = date('Y-m-01', strtotime('apr'));
                        $dateTo = date('Y-m-t', strtotime('jun'));
                        break;
                    } elseif (in_array($curMonth, array('jul', 'aug', 'sep'))) {
                        $dateFrom = date('Y-m-01', strtotime('jul'));
                        $dateTo = date('Y-m-t', strtotime('sep'));
                        break;
                    } elseif (in_array($curMonth, array('oct', 'nov', 'dec'))) {
                        $dateFrom = date('Y-m-01', strtotime('oct'));
                        $dateTo = date('Y-m-t', strtotime('dec'));
                        break;
                    }
                    break;
                case 4: // Laufendes Jahr
                    $queryUseDate = true;
                    $dateFrom = date('Y-m-01', strtotime('jan'));
                    $dateTo = date('Y-m-t', strtotime('dec'));
                    break;
                case 8: // Letzter Monat
                    $queryUseDate = true;
                    $dateFrom = date('Y-m-01', strtotime('last month'));
                    $dateTo = date('Y-m-t', strtotime('last month'));
                    break;
                case 16: // Letzte 30 Tage
                    $queryUseDate = true;
                    $dateFrom = date_format(date_modify(date_create('now'), '-30 day'), 'Y-m-d');
                    $dateTo = date('Y-m-d', strtotime('now'));
                    break;
                case 32: // Letzte 90 Tage
                    $queryUseDate = true;
                    $dateFrom = date_format(date_modify(date_create('now'), '-90 day'), 'Y-m-d');
                    $dateTo = date('Y-m-d', strtotime('now'));
                    break;
                case 64: // Letzte 180 Tage
                    $queryUseDate = true;
                    $dateFrom = date_format(date_modify(date_create('now'), '-180 day'), 'Y-m-d');
                    $dateTo = date('Y-m-d', strtotime('now'));
                    break;
                case 128: // Letzte 360 Tage
                    $queryUseDate = true;
                    $dateFrom = date_format(date_modify(date_create('now'), '-360 day'), 'Y-m-d');
                    $dateTo = date('Y-m-d', strtotime('now'));
                    break;
                case 256: // Letzte 10 Buchungen
                    $queryUseLimit = true;
                    $limit = 10;
                    break;
                case 512: // Letzte 20 Buchungen
                    $queryUseLimit = true;
                    $limit = 20;
                    break;
                case 1024: // Letzte 30 Buchungen
                    $queryUseLimit = true;
                    $limit = 30;
                    break;
                case 2048: // Letzte 100 Buchungen
                    $queryUseLimit = true;
                    $limit = 100;
                    break;
                case 4096: // Letzte 1000 Buchungen
                    $queryUseLimit = true;
                    $limit = 1000;
                    break;
                default:
                    echo json_encode($jsResponse);
                    exit;
            }

            // SQL-Query bereitstellen
            if ($queryUseDate) {
                $sqlquery = "SELECT a.categoryID, a.categoryLabel, SUM(e.grandTotal) * a.classSign AS balance FROM viewAccount a LEFT JOIN viewEntries e ON e.accountID = a.accountID WHERE a.classID IN (" . $dataInput['type'] . ") AND a.accountIsActive = 'Y' AND e.date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' GROUP BY a.categoryID ORDER BY a.accountID ASC";
            } elseif ($queryUseLimit) {
                $sqlquery ="SELECT a.categoryID, a.categoryLabel, SUM(e.grandTotal) * a.classSign AS balance FROM viewAccount a LEFT JOIN (SELECT * FROM _viewEntries _e ORDER BY _e.created DESC LIMIT " . $limit * 2 . ") e ON e.accountID = a.accountID WHERE a.classID IN (" . $dataInput['type'] . ") AND a.accountIsActive = 'Y' GROUP BY a.categoryID HAVING balance IS NOT NULL ORDER BY a.accountID ASC";
            }
        } else {
            $periodID = $dataInput['period'] * -1;
            // SQL-Query bereitstellen
            $sqlquery = "SELECT a.categoryID, a.categoryLabel, SUM(e.grandTotal) * a.classSign AS balance FROM viewAccount a LEFT JOIN viewEntries e ON e.accountID = a.accountID WHERE a.classID IN (" . $dataInput['type'] . ") AND a.accountIsActive = 'Y' AND e.periodID = " . $periodID . " GROUP BY a.categoryID ORDER BY a.accountID ASC";
        }

        $result = mysqli_query($userLink, $sqlquery);

        // Prüfen ob Datensätze vorhanden
        if (mysqli_num_rows($result) >= 1) {
            // Abfrage erfolgreich
            $jsResponse['success'] = true;
            $jsResponse['result'] = true;

            // Resulat in  Array schreiben
            while ($row = mysqli_fetch_assoc($result)) {
                // Display für Category
                $tmpCVal = $row['categoryLabel'];
                $row['categoryLabel'] = [];
                $row['categoryLabel']['display'] = $row['categoryID'] . ' ' . $tmpCVal;

                // Display für Balance
                $tmpBVal = $row['balance'];
                $row['balance'] = [];
                $row['balance']['display'] = 'CHF ' . numfmt_format($fmtD, $tmpBVal);
                $row['balance']['value'] = floatval($tmpBVal);
    
                // Datensatz im JSON-Format vorbereiten
                $jsResponse['data'][] = $row;
            }
            echo json_encode($jsResponse);
            exit();
        } else {
            // Abfrage erfolgreich, leeres Resultat
            $jsResponse['success'] = true;
            echo json_encode($jsResponse);
            exit();
        }

    } else {
        echo json_encode($jsResponse);
        exit();
    }

} else {
    http_response_code(405);
    header ('Allow: POST');
}
?>