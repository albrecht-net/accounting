<?php
if (empty($_POST['delID'])) {
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
}
exit();
?>