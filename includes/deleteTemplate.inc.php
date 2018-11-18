<?php
if (empty($_POST['delID'])) {
    echo 0;
} else {
    // SQL-Query bereitstellen
    $sqlquery = "DELETE FROM `templates` WHERE `templateID` = " . intval($_POST['delID']) . " AND `userID` = " . intval($_SESSION['userID']) . " AND `dbID` = " . intval($_SESSION['userDb']['dbID']);

    $result = mysqli_query($config['link'], $sqlquery);

    // Prüfen ob 1 Resultat
    if (!$result) {
        echo 0;
    } else {
        echo 1;
    }
}
exit();
?>