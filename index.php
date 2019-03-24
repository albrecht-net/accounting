<?php
// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php');
    exit();
}

// Mit Ziel Datenbank verbinden
require_once 'includes/userDbConnect.inc.php';

// Fällige Daueraufträge prüfen
include 'includes/standingOrderCheck.inc.php';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <title>Buchhaltung</title>
</head>
<body>
    <?php
    // Navigation einbinden
    include_once 'core/navigation.php';
    ?>

    <div class="container">
        <div class="col-12">
            <?php
            // SQL-Query bereitstellen
            $sqlquery = "SELECT * FROM viewJournal ORDER BY created DESC LIMIT 10";
            $result = mysqli_query($userLink, $sqlquery);


            // Resulat in 1 Array schreiben, sortiert nach Kategorie
            $dataEntries = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $dataEntries[] = $row;
            }


            echo "<div class=\"table-responsive\"><table class=\"table table-striped\">";
            foreach ($dataEntries as $rowIndex => $row) {
                if ($rowIndex == 0) {
                    echo "<thead><tr>";
                    foreach ($row as $columnNameHead => $cellHead) {
                        echo "<th scope=\"col\">$columnNameHead</th>";
                    }
                    echo "</tr></thead><tbody>";
                }

                echo "<tr>";
                foreach ($row as $columnNameBody => $cellBody) {
                    echo "<td>$cellBody</td>";
                }
                echo "</tr>";

                if ($rowIndex == count($dataEntries)-1) {
                    echo "</tbody>";
                }

            }
            echo "</table></div>";
            ?>
        </div>

    <!-- /container -->
    </div>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>