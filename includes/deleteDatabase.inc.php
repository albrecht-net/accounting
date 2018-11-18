<?php
if (empty($_POST['delID'])) {
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
}
exit();
?>