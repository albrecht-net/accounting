<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
    }
    
    require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

    // Array Eingabe
    $dataInput = array(
        'tableContent' => $_POST['tableContent'],
        'delID' => intval($_POST['delID'])
    );

    if ($dataInput['tableContent'] != 'Database' && empty($dataInput['delID'])) {
        echo 0;
    } else {
        // SQL-Query bereitstellen
        $sqlquery = "DELETE FROM `databases` WHERE `dbID` = " . intval($_POST['delID']) . " AND `userID` = " . intval($_SESSION['userID']);

        $result = mysqli_query($config['link'], $sqlquery);

        // Prüfen ob 1 Resultat
        if (!$result) {
            echo 0;
        } else {
            // Falls zu löschende DB aktive DB ist, diese aus Session entfernen
            if ($_POST['delID'] == $_SESSION['userDb']['dbID']) {
                unset($_SESSION['userDb']['dbID']);
                $_SESSION['userDb']['userDbSet'] = 0;
            }
            echo 1;
        }
        exit();
    }
} else {
http_response_code(405);
header ('Allow: POST');
}
?>