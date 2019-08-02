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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/mainSite.css">

    <title>Buchhaltung</title>
</head>
<body>
    <?php
    // Navigation einbinden
    include_once 'core/navigation.php';
    ?>

    <div class="container container-wide">
        <?php if ($_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>
            <div class="row">
                <div class="col-12">
                    <div class="card mt-3">
                        <h5 class="card-header" id="lastEntries">Zuletzt erfasste Buchungen</h5>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-row">
                                        <div class="form-group col-md-5"> <!-- Konto Auswahl -->
                                            <label for="leSelAccount">Konto Auswahl</label>
                                            <?php
                                            // SQL-Query bereitstellen
                                            $sqlquery = "SELECT `account`.`accountID`, `account`.`label` AS `accountLabel`, `accountCategory`.`label` AS `categoryLabel` FROM `account` LEFT JOIN `accountCategory` ON `account`.`category` = `accountCategory`.`categoryID` WHERE `account`.`active` = 'Y' ORDER BY `accountCategory`.`label` ASC, `account`.`label` ASC";
                                            $result = mysqli_query($userLink, $sqlquery);
                        
                                            // Prüfen ob Datensätze vorhanden
                                            if (mysqli_num_rows($result) < 1): ?>
                                            <select class="form-control filter-input" id="leSelAccount" name="leSelAccount" required>
                                                <option disabled>Keine Datensätze vorhanden</option>
                                            <?php else: ?>
                                            <select class="form-control filter-input" id="leSelAccount" name="leSelAccount" required>
                                                <option value="0">Alle anzeigen</option>
                                                <?php
                                                // Resulat in 1 Array schreiben, sortiert nach Kategorie
                                                $valueArray = [];
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    if ($row['categoryLabel'] != $category) {
                                                        $i = 0;
                                                    }
                
                                                    $category = $row['categoryLabel'];
                
                                                    $valueArray[$category][$i] = $row;
                                                    $i++;
                                                }
                
                                                // Array in Dropdown ausgeben
                                                foreach ($valueArray as $key => $row1): ?>
                                                    <optgroup label="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php foreach ($row1 as $key => $row2): ?>
                                                        <option value="<?php echo intval($row2['accountID']); ?>"<?php echo ($_GET['filterAccount'] == $row2['accountID'] ? ' selected' : ''); ?>><?php echo intval($row2['accountID']) . ' ' . htmlspecialchars($row2['accountLabel'], ENT_QUOTES, 'UTF-8'); ?></option>
                                                    <?php endforeach;
                                                endforeach;
                                            endif; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3 col-xl-2"> <!-- Zeitraum -->
                                            <label for="leSelPeriodOfLE">Auswahl Zeitraum</label>
                                            <select class="form-control filter-input" id="leSelPeriodOfLE" name="leSelPeriodOfLE" required>
                                                <option value="1">Laufender Monat</option>
                                                <option value="2">Laufendes Quartal</option>
                                                <option value="4">Laufendes Jahr</option>
                                                <option value="8">Letzter Monat</option>
                                                <option disabled></option>
                                                <option value="16">Letzte 30 Tage</option>
                                                <option value="32">Letzte 90 Tage</option>
                                                <option value="64">Letzte 180 Tage</option>
                                                <option value="128">Letzte 360 Tage</option>
                                                <option disabled></option>
                                                <option value="256" selected>Letzte 10 Buchungen</option>
                                                <option value="512">Letzte 20 Buchungen</option>
                                                <option value="1024">Letzte 30 Buchungen</option>
                                                <option value="2048">Letzte 100 Buchungen</option>
                                                <option value="4096">Letzte 1000 Buchungen</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table id="dTableLastEntries" class="table table-sm table-striped">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th scope="col">#</th>
                                                <th scope="col">Erstelldatum</th>
                                                <th scope="col">Buchungsdatum</th>
                                                <th scope="col">Periode</th>
                                                <th scope="col">Empfänger</th>
                                                <th scope="col">Rechnungsnummer</th>
                                                <th scope="col">Beschreibung</th>
                                                <th scope="col">Konto Soll</th>
                                                <th scope="col">Konto Haben</th>
                                                <th scope="col">Betrag</th>
                                                <th scope="col">Klassifikation 1</th>
                                                <th scope="col">Klassifikation 2</th>
                                                <th scope="col">Klassifikation 3</th>
                                                <th scope="col">Buchungsreferenz</th>
                                                <th scope="col">Abgeglichen</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div class="card mt-3">
                        <h5 class="card-header" id="balanceOverview">Saldo Übersicht</h5>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <?php
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT accountID, accountLabel, balance FROM viewBalanceAL WHERE accountIsActive = 'Y'";
                                    $result = mysqli_query($userLink, $sqlquery);

                                    // Prüfen ob Datensätze vorhanden
                                    if (mysqli_num_rows($result) >= 1): ?>
                                        <table class="table table-borderless">
                                            <tbody>
                                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                    <tr>
                                                        <td class="p-0"><?php echo intval($row['accountID']) . ' ' . htmlspecialchars($row['accountLabel'], ENT_QUOTES, 'UTF-8') . ':'; ?></td>
                                                        <td class="p-0 text-nowrap"><?php echo 'CHF ' . number_format(floatval($row['balance']), 2, '.', '’'); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <p class="lead">Keine Einträge gefunden</p>
                                        <p>Sie haben für die ausgewählte Ziel-Datenbank noch keine Konten erfasst.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <p class="lead">Für die aktuelle Sitzung wurde keine Datenbank ausgewählt. Sie können eine <a href="settings/database.php">neue Datenbank hinzufügen</a> oder sich <a href="logout.php">abmelden</a></p>
        <?php endif ?>

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
    <script>
        $('.filter-input').change(function() {
            dTableLastEntries.ajax.reload();
        });
    </script>
    
</body>
</html>