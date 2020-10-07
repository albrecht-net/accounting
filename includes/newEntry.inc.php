<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
    }
    
    require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

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

    // Array Response
    $_SESSION['response'] = array(
        'alert' => array(
            'alertType' => NULL,
            'alertDismissible' => true
        ),
        'message' => array(
            'messageTitle' => NULL,
            'message' => NULL
        ),
        'values' => array()
    );

    // Neue Buchung speichern
    if ($_POST['chkAddTemplate'] == 0) {
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
            'entryReference' => (isset($_POST['entryReference']) ? array_map('intval', $_POST['entryReference']) : NULL)
        );

        // Buchung speichern
        if (!include 'saveEntry.inc.php') {
            echo date('H:i:s') . ' Datei einbinden fehlgeschlagen';
            exit();
        }

    // Neue Buchung als Vorlage
    } elseif ($_POST['chkAddTemplate'] == 1) {
        // Array Eingabe
        $dataInput = array(
            'input' => array(
                'recipient' => intval($_POST['recipient']),
                'invoiceNo' => mysqli_real_escape_string($userLink, trim($_POST['invoiceNo'])),
                'entryText' => mysqli_real_escape_string($userLink, trim($_POST['entryText'])),
                'grandTotal' => floatval($_POST['grandTotal']),
                'debitAccount' => intval($_POST['debitAccount']),
                'creditAccount' => intval($_POST['creditAccount']),
                'period' => intval($_POST['period']),
                'classification1' => intval($_POST['classification1']),
                'classification2' => intval($_POST['classification2']),
                'classification3' => intval($_POST['classification3'])
            ),
            'label' => mysqli_real_escape_string($userLink, trim($_POST['nameTemplate'])),
            'templateType' => intval($_POST['radioTemplate'])
        );

        // Buchungsvorlage speichern
        if (!include 'saveEntryTemplate.inc.php') {
            echo date('H:i:s') . ' Datei einbinden fehlgeschlagen';
            exit();
        }
    }
} else {
    http_response_code(405);
    header ('Allow: POST');
}
?>