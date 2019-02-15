<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Array Eingabe
    $dataInput = array(
        'tableContent' => $_POST['tableContent'],
        'delID' => intval($_POST['delID'])
    );

    if ($dataInput['tableContent'] != 'StandingOrder' && empty($dataInput['delID'])) {
        echo 0;
    } else {
        // SQL-Query bereitstellen
        $sqlquery = "DELETE FROM `standingOrder` WHERE `standingOrderID` = " . intval($_POST['delID']);

        $result = mysqli_query($userLink, $sqlquery);

        // Prüfen ob 1 Resultat
        if (!$result) {
            echo 0;
        } else {
            echo 1;
        }
        exit();
    }
} else {
    http_response_code(405);
    header ('Allow: POST');
}
?>