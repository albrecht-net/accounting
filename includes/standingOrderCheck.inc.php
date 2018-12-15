<?php
$date = date('Y-m-d H');

// Anzahl fälliger Daueraufträge ermitteln und in Session zwischenspeichern
if ($_SESSION['standingOrder']['updated'] < $date) {
    $sqlquery = "SELECT `standingOrderID` FROM `standingOrder` WHERE `nextExecutionDate` <= NOW()";
    $_SESSION['standingOrder']['count'] = mysqli_num_rows(mysqli_query($userLink, $sqlquery));
    $_SESSION['standingOrder']['updated'] = $date;
}
?>