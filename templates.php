<?php
// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php?rd=' . urlencode('templates.php'));
    exit();
}

// Mit Ziel Datenbank verbinden
require_once 'includes/userDbConnect.inc.php';

// Tabellen-Reihe löschen
if ($_POST['trValueDelete']) {
    if (!include 'includes/deleteTemplate.inc.php') {
        echo date('H:i:s') . ' Datei einbinden fehlgeschlagen';
        exit();
    }
}

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

    <title>Gespeicherte Vorlagen</title>
</head>
<body>
    <?php
    // Navigation einbinden
    include_once 'core/navigation.php';
    ?>

    <div class="container">
        <?php if ($_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>
            <h3 class="mt-3" id="savedTemplates">Gespeicherte Vorlagen</h3>
            <hr class="mb-4">
            <div class="row">
                <div class="col-12 mb-5">
                    <?php
                    // SQL-Query bereitstellen
                    $sqlquery = "SELECT `templateID`, `created`, `label`, `recipient`, `invoiceNo`, `entryText`, `grandTotal`, `debitAccount`, `creditAccount`, `period`, `classification1`, `classification2`, `classification3` FROM `template` ORDER BY `label` ASC";
                    $result = mysqli_query($userLink, $sqlquery);

                    // Prüfen ob Datensätze vorhanden
                    if (mysqli_num_rows($result) >= 1): ?>                
                        <table id="dTableTemplates" class="table table-striped">
                            <thead>
                                <tr class="text-nowrap">
                                    <th scope="col">Erstelldatum</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Definierte Werte</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)):
                                    // Vorlage-Werte in neues Array schreiben
                                    $valueTemplate = array_slice($row, 3);
                                    
                                    // Leere Felder aus valueTemplate Array entfernen
                                    $valueTemplate = array_diff($valueTemplate, array(NULL, '', 0, '0.00'));

                                    if (intval($_GET['template']) == $row['templateID']): ?>
                                        <tr id="Template-<?php echo intval($row['templateID']); ?>" class="text-nowrap table-warning">
                                    <?php else: ?>
                                        <tr id="Template-<?php echo intval($row['templateID']); ?>" class="text-nowrap">
                                    <?php endif; ?>
                                        <td data-order="<?php echo strtotime($row['created']); ?>"><?php echo date_format(date_create($row['created']), 'd.m.Y'); ?></td>
                                        <td><a href="entry.php?<?php echo http_build_query($valueTemplate); ?>"><?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                                        <td><?php echo implode(', ', array_keys($valueTemplate)); ?></td>
                                        <td><button type="button" class="btn btn-tr btn-block btn-danger tr-delete" value="Template-<?php echo intval($row['templateID']); ?>">Löschen</button></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="lead">Keine Vorlage gefunden</p>
                        <p>Sie haben für die ausgewählte Ziel-Datenbank noch keine Vorlage erstellt. Erstellen Sie Ihre erste Vorlage gleich <a href="entry.php#addTemplate">hier</a>.</p>
                    <?php endif; ?>
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
    <!-- Eintrag löschen -->
    <script src="js/trValueDelete.js"></script>
</body>
</html>