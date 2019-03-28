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
                <h3 class="mt-3" id="lastEntries">Zuletzt erfasste Buchungen</h3>
                <hr class="mb-4">
                <div class="row">
                    <div class="col-12 mb-5">
                        <?php
                        // SQL-Query bereitstellen
                        $sqlquery = "SELECT * FROM viewJournal ORDER BY created DESC LIMIT 10";
                        $result = mysqli_query($userLink, $sqlquery);

                        // Prüfen ob Datensätze vorhanden
                        if (mysqli_num_rows($result) >= 1): ?>
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
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="text-nowrap">
                                            <td><?php echo intval($row['entryID']); ?></td>
                                            <td><?php echo date_format(date_create($row['created']), 'd.m.Y H:i:s'); ?></td>
                                            <td><?php echo date_format(date_create($row['date']), 'd.m.Y'); ?></td>
                                            <td><?php echo htmlspecialchars($row['period'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($row['recipient'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($row['invoiceNo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($row['entryText'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo intval($row['debitAccountID']) . ' ' . htmlspecialchars($row['debitAccount'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo intval($row['creditAccountID']) . ' ' . htmlspecialchars($row['creditAccount'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-right"><?php echo 'CHF ' . floatval($row['grandTotal']); ?></td>
                                            <td><?php echo htmlspecialchars($row['classification1'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($row['classification2'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($row['classification3'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo intval($row['entryReference']); ?></td>
                                            <td><?php echo htmlspecialchars($row['reconcilation'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="lead">Keine Einträge gefunden</p>
                            <p>Sie haben für die ausgewählte Ziel-Datenbank noch keine Buchungen erfasst. Erfassen Sie Ihre erste Buchung gleich <a href="entry.php#newEntry">hier</a>.</p>
                        <?php endif; ?>
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