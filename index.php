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
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/datatables.min.css">

    <title>Buchhaltung</title>
</head>
<body>
    <?php
    // Navigation einbinden
    include_once 'core/navigation.php';
    ?>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="mt-3" id="standingOrder">Zuletzt erfasste Buchungen</h3>
                <hr class="mb-4">
                <div class="row">
                    <div class="col-12 mb-5">
                        <?php
                        // SQL-Query bereitstellen
                        $sqlquery = "SELECT * FROM viewJournal ORDER BY created DESC LIMIT 10";
                        $result = mysqli_query($userLink, $sqlquery);


                        // Resulat in 1 Array schreiben, sortiert nach Kategorie
                        $dataEntries = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $dataEntries[] = $row;
                        }
                        ?>

                        <table id="dTableLastEntries" class="table table-sm table-striped">
                            <?php
                            foreach ($dataEntries as $rowIndex => $row):
                                if ($rowIndex == 0): ?>
                                    <thead>
                                        <tr class="text-nowrap">
                                        <?php foreach ($row as $columnNameHead => $cellHead): ?>
                                            <th scope="col"><?php echo htmlspecialchars($columnNameHead, ENT_QUOTES, 'UTF-8'); ?></th>
                                        <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                <?php endif; ?>
                                <tr class="text-nowrap">

                                <?php foreach ($row as $columnNameBody => $cellBody): ?>
                                    <td><?php echo htmlspecialchars($cellBody, ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php endforeach; ?>

                                </tr>

                                <?php if ($rowIndex == count($dataEntries)-1): ?>
                                    </tbody>
                                <?php endif; 
                            endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <!-- /container -->
    </div>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.18/datatables.min.js"></script>
    <!-- Datatables-Helper -->
    <script src="js/datatablesHelper.js"></script>
    
</body>
</html>