<?php
// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php?rd=' . urlencode('standingOrder.php'));
    exit();
}

// Mit Ziel Datenbank verbinden
require_once 'includes/userDbConnect.inc.php';

// Tabellen-Reihe löschen
if ($_POST['trValueDelete']) {
    if (!include 'includes/deleteStandingOrder.inc.php') {
        echo date('H:i:s') . ' Datei einbinden fehlgeschlagen';
        exit();
    }
}

// Fällige Daueraufträge prüfen
include 'includes/standingOrderCheck.inc.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $config['lang']; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/datatables.min.css">
    <!-- Datatables FixedColumns CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.bootstrap4.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/mainSite.css">

    <title>Dauerauftrag</title>
</head>
<body>
    <?php
    // Navigation einbinden
    include_once 'core/navigation.php';
    ?>

    <div class="container">
        <?php if ($_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>
            <div class="row">
                <div class="col-12">
                    <div class="card mt-3">
                        <h5 class="card-header" id="addStandingOrder">Dauerauftrag erfassen</h5>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <?php include_once 'includes/alertProvider.inc.php'; // Alert Provider ?>
                                    <form action="includes/addStandingOrder.inc.php" method="POST">
                                        <p>Daueraufträge basieren auf einer bereits erstellten <a href="templates.php#savedTemplates">Vorlage</a>.</p>
                                        <div class="form-row">
                                            <div class="form-group col-md-5"> <!-- Buchungsvorlage auswählen -->
                                                <label for="template">Buchungsvorlage auswählen</label>
                                                <?php
                                                // SQL-Query bereitstellen
                                                $sqlquery = "SELECT `templateID`, `label` FROM `template` ORDER BY `label` ASC";
                                                $result = mysqli_query($userLink, $sqlquery);
                
                                                // Prüfen ob Datensätze vorhanden
                                                if (mysqli_num_rows($result) < 1): ?>
                                                <select class="form-control" id="template" name="template" required>
                                                    <option disabled>Keine Datensätze vorhanden</option>
                                                <?php else: ?>
                                                <select class="form-control" id="template" name="template" required>
                                                    <option></option>
                                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                    <option value="<?php echo intval($row['templateID']); ?>"<?php echo ($_GET['template'] == $row['templateID'] ? ' selected' : ''); ?>><?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></option>
                                                    <?php endwhile;
                                                endif; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-7"> <!-- Beschreibung -->
                                                <label for="label">Beschreibung</label>
                                                <input class="form-control chk-toggle-dis-slave" type="text" id="label" name="label" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-3"> <!-- Startdatum -->
                                                <label for="validFromValue">Startdatum</label>
                                                <input class="form-control" type="date" id="validFromValue" name="validFromValue" min="<?php echo date('Y-m-d'); ?>" required>
                                                <small id="validFromValueHelp" class="form-text text-muted">Das Startdatum muss immer ausgewählt werden. Das Monatsende wird auf den letzten des ausgewählten Monats festgelegt.</small>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-12">
                                                <div class="form-check"> <!-- Nutze Startdatum -->
                                                    <input class="form-check-input" type="radio" name="validFromType" id="validFromType1" value="1" checked>
                                                    <label class="form-check-label" for="validFromType1">
                                                        Nutze Startdatum
                                                    </label>
                                                </div>
                                                <div class="form-check"> <!-- Nutze Monatsende -->
                                                    <input class="form-check-input" type="radio" name="validFromType" id="validFromType2" value="2">
                                                    <label class="form-check-label" for="validFromType2">
                                                        Nutze Monatsende
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-12"> <!-- Periodizität -->
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label for="periodicityValue">Wiederholen alle:</label>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-3 col-md-2"> <!-- PeriodizitätValue -->
                                                        <input class="form-control chk-toggle-req-slave" type="number" id="periodicityValue" name="periodicityValue" step="1" lang="en" min="1" value="1" required>
                                                    </div>
                                                    <div class="col-7 col-md-3"> <!-- PeriodizitätType -->
                                                        <select class="form-control" id="periodicityType" name="periodicityType">
                                                            <option value="1">Tag(e)</option>
                                                            <option value="2">Woche(n)</option>
                                                            <option value="4" selected>Monat(e)</option>
                                                            <option value="8">Jahr(e)</option>
                                                            <option value="16">Jeden Montag bis Freitag</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group form-row"> <!-- Gültig bis Widerruf -->
                                                    <div class="col-5 align-self-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="validToType" id="validToType1" value="1" checked>
                                                            <label class="form-check-label" for="validToType1">
                                                                Auf Widerruf
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-7">
                                                        <input class="form-control-plaintext" type="text" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group form-row"> <!-- Gültig bis Enddatum -->
                                                    <div class="col-3 col-sm-2 col-lg-1 align-self-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="validToType" id="validToType2" value="2">
                                                            <label class="form-check-label" for="validToType2">
                                                                Am:
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-7 col-md-3"> <!-- Enddatum -->
                                                        <input class="form-control radio-toggle-2" type="date" id="validToValue" name="validToValue" min="<?php echo date_format(date_modify(date_create('now'), '+1 day'), 'Y-m-d'); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group form-row"> <!-- Gültig n mal -->
                                                    <div class="col-3 col-sm-2 col-lg-1 align-self-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="validToType" id="validToType3" value="4">
                                                            <label class="form-check-label" for="validToType3">
                                                                Nach:
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 col-md-2"> <!-- Value n -->
                                                        <input class="form-control radio-toggle-4" type="number" id="initialEvents" name="initialEvents" step="1" lang="en" min="1" required>
                                                    </div>
                                                    <div class="col-4 pl-0">
                                                        <input class="form-control-plaintext" type="text" disabled value="Termin(en)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-3">
                                                <button type="submit" class="btn btn-primary btn-block" name="submit">Speichern</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card mt-3">
                        <h5 class="card-header" id="savedStandingOrder">Erfasste Daueraufträge</h3>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <?php
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT standingOrder.standingOrderID, standingOrder.created, standingOrder.label AS standingOrderLabel, standingOrder.template AS templateID, template.label AS templateLabel, standingOrder.periodicityType, standingOrder.periodicityValue, standingOrder.validToValue, standingOrder.handledEvents, standingOrder.remainingEvents, standingOrder.nextExecutionDate, IF(standingOrder.nextExecutionDate <= NOW(), 'Y', 'N') AS dateIsDue, standingOrder.closed FROM standingOrder LEFT JOIN template ON standingOrder.template = template.templateID";
                                    $result = mysqli_query($userLink, $sqlquery);
                
                                    // Prüfen ob Datensätze vorhanden
                                    if (mysqli_num_rows($result) >= 1): ?>                
                                        <table id="dTableSavedSo" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Erstelldatum</th>
                                                    <th scope="col">Vorlage</th>
                                                    <th scope="col">Wiederholung</th>
                                                    <th scope="col">Nächste Ausführung</th>
                                                    <th scope="col">Anzahl abgearbeitet</th>
                                                    <th scope="col">Anzahl verbleibend</th>
                                                    <th scope="col">Enddatum</th>
                                                    <th scope="col">Abgeschlossen</th>
                                                    <th scope="col"></th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = mysqli_fetch_assoc($result)):
                                                    switch (intval($row['periodicityType'])) {
                                                        case 1:
                                                            $row['periodicityType'] = 'Tage';
                                                            break;
                                                        case 2:
                                                            $row['periodicityType'] = 'Wochen';
                                                            break;
                                                        case 4:
                                                            $row['periodicityType'] = 'Monate';
                                                            break;
                                                        case 8:
                                                            $row['periodicityType'] = 'Jahre';
                                                            break;
                                                        case 16:
                                                            $row['periodicityType'] = 'Arbeitstage';
                                                    }
                
                                                    // StandingOrder URL
                                                    if ($row['closed'] == 'Y') {
                                                        $soURL = '';
                                                    } elseif ($row['dateIsDue'] == 'Y') {
                                                        $soURL = 'entry.php?standingOrder=' . intval($row['standingOrderID']) . '#newEntry';
                                                    } elseif ($row['dateIsDue'] == 'N') {
                                                        $soURL = 'entry.php?standingOrder=' . intval($row['standingOrderID']) . '&beforeDueDate=1#newEntry';
                                                    }
                                                    ?>
                
                                                    <tr id="StandingOrder-<?php echo intval($row['standingOrderID']); ?>">
                                                        <td><?php echo htmlspecialchars($row['standingOrderLabel'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td data-order="<?php echo strtotime($row['created']); ?>"><?php echo date_format(date_create($row['created']), 'd.m.Y'); ?></td>
                                                        <td><a href="templates.php?template=<?php echo intval($row['templateID']); ?>"><?php echo htmlspecialchars($row['templateLabel'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                                                        <td><?php echo 'Alle ' . intval($row['periodicityValue']) . ' ' . $row['periodicityType']; ?></td>
                                                        <td data-order="<?php echo ($row['nextExecutionDate'] == NULL ? '0' : strtotime($row['nextExecutionDate'])); ?>"><?php echo ($row['nextExecutionDate'] == NULL ? '-' : date_format(date_create($row['nextExecutionDate']), 'd.m.Y')); ?></td>
                                                        <td><?php echo intval($row['handledEvents']); ?></td>
                                                        <td><?php echo ($row['remainingEvents'] == NULL ? '-' : intval($row['remainingEvents'])); ?></td>
                                                        <td data-order="<?php echo ($row['validToValue'] == NULL ? '0' : strtotime($row['validToValue'])); ?>"><?php echo ($row['validToValue'] == NULL ? '-' : date_format(date_create($row['validToValue']), 'd.m.Y')); ?></td>
                                                        <td><?php echo ($row['closed'] == 'N' ? 'Nein' : 'Ja'); ?></td>
                                                        <td><a class="btn btn-tr btn-block btn-primary<?php echo ($row['closed'] == 'Y' ? ' disabled' : ''); ?>" href="<?php echo $soURL; ?>" role="button">Jetzt ausführen</a></td>
                                                        <td><button type="button" class="btn btn-tr btn-block btn-danger tr-delete" value="StandingOrder-<?php echo intval($row['standingOrderID']); ?>">Löschen</button></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <p class="lead">Keine Einträge gefunden</p>
                                        <p>Sie haben für die ausgewählte Ziel-Datenbank noch keinen Dauerauftrag erstellt. Erstellen Sie Ihren ersten Dauerauftrag gleich <a href="standingOrder.php#addStandingOrder">hier</a>.</p>
                                    <?php endif; ?>
                                    </div>
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
    <!-- Datatables FixedColumns JS -->
    <script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js"></script>
    <!-- Datatables-Helper -->
    <script src="js/datatablesHelper.js"></script>
    <!-- StandingOrderHelper -->
    <script src="js/standingOrderHelper.js"></script>
    <!-- Eintrag löschen -->
    <script src="js/trValueDelete.js"></script>
</body>
</html>