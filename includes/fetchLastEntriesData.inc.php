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
        'account' => intval($_POST['account']),
        'periodOfLE' => intval($_POST['periodOfLE'])
    );
    // Leere Felder aus Eingabe Array entfernen
    $dataInput = array_diff($dataInput, array(NULL, ''));

    // Prüfen ob Eingabe vorhanden
    if (count($dataInput) == 2) {
        // Zeitraum festlegen
        switch ($dataInput['periodOfLE']) {
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
                } elseif (in_array($curMonth, array('jul', 'sug', 'sep'))) {
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

        // Kontoauswahl
        if ($dataInput['account'] !== 0) {
            // SQL-Query bereitstellen
            if ($queryUseDate) {
                $sqlquery = "SELECT * FROM viewJournal j WHERE j.debitAccountID = " . $dataInput['account'] . " OR j.creditAccountID = " . $dataInput['account'] . " AND j.date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' ORDER BY j.created DESC";
            } elseif ($queryUseLimit) {
                $sqlquery ="SELECT * FROM viewJournal j WHERE j.debitAccountID = " . $dataInput['account'] . " OR j.creditAccountID = " . $dataInput['account'] . " ORDER BY j.created DESC LIMIT " . $limit;
            }
        } else {
            // SQL-Query bereitstellen
            if ($queryUseDate) {
                $sqlquery = "SELECT * FROM viewJournal j WHERE j.date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' ORDER BY j.created DESC";
            } elseif ($queryUseLimit) {
                $sqlquery ="SELECT * FROM viewJournal j ORDER BY j.created DESC LIMIT " . $limit;
            }
        }
        $result = mysqli_query($userLink, $sqlquery);

        // Prüfen ob Datensätze vorhanden
        if (mysqli_num_rows($result) >= 1) {
            // Abfrage erfolgreich
            $jsResponse['success'] = true;
            $jsResponse['result'] = true;

            // Resulat in  Array schreiben
            while ($row = mysqli_fetch_assoc($result)) {
                // Timestamp für Sortierung Erstelldatum
                $tmpCreatedVal = $row['created'];
                $row['created'] = [];
                $row['created']['display'] = date_format(date_create($tmpCreatedVal), 'd.m.Y H:i:s');
                $row['created']['value'] = $tmpCreatedVal;
                $row['created']['timestamp'] = strtotime($tmpCreatedVal);

                // Timestamp für Sortierung Buchungsdatum
                $tmpDateVal = $row['date'];
                $row['date'] = [];
                $row['date']['display'] = date_format(date_create($tmpDateVal), 'd.m.Y');
                $row['date']['value'] = $tmpDateVal;
                $row['date']['timestamp'] = strtotime($tmpDateVal);

                // Display für DebitAccount
                $tmpDAVal = $row['debitAccount'];
                $row['debitAccount'] = [];
                $row['debitAccount']['display'] = $row['debitAccountID'] . ' ' . $tmpDAVal;

                // Display für CreditAccount
                $tmpCAVal = $row['creditAccount'];
                $row['creditAccount'] = [];
                $row['creditAccount']['display'] = $row['creditAccountID'] . ' ' . $tmpCAVal;

                // Display für GrandTotal
                $tmpGTVal = $row['grandTotal'];
                $row['grandTotal'] = [];
                $row['grandTotal']['display'] = 'CHF ' . numfmt_format($fmtD, $tmpGTVal);
                $row['grandTotal']['value'] = numfmt_format($fmtV, $tmpGTVal);

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