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

// Überprüfen ob Submit geklickt wurde
if (isset($_POST['submit'])) {
    if (!include 'includes/addStandingOrder.inc.php') {
        echo date('H:i:s') . ' Datei einbinden fehlgeschlagen';
        exit();
    }
} elseif ($_POST['tableContent'] == 'standingOrder') {
    if (!include 'includes/deleteStandingOrder.inc.php') {
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

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/mainSite.css">

    <title>Dauerauftrag</title>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Buchhaltung</a>
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <?php if (intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']) > 0): ?>
                    <a class="nav-link" href="buchung.php">Neue Buchung <span class="badge badge-warning"><?php echo intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']); ?></span><span class="sr-only">pending booking</span></a>
                    <?php else: ?>
                    <a class="nav-link" href="buchung.php">Neue Buchung</a>
                    <?php endif; ?>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Weitere erfassen
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item disabled" href="#">Konto</a>
                        <a class="dropdown-item disabled" href="#">Empfänger</a>
                        <a class="dropdown-item disabled" href="#">Klassifikation</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="templates.php">Vorlagen</a>
                </li>
            </ul>
            <ul class="navbar-nav navbar-right">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_SESSION['username']; ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item disabled" href="#">Mein Profil</a>
                        <a class="dropdown-item" href="settings/account.php">Einstellungen</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Abmelden</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php if ($_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>
        <h3 class="mt-3" id="addStandingOrder">Dauerauftrag erfassen</h3>
        <hr class="mb-4">
        <div class="row">
            <div class="col-12 mb-5">
                <form action="standingOrder.php" method="POST">
                    <p>Daueraufträge basieren auf einer bereits erstellten <a href="templates.php#savedTemplates">Vorlage</a>.</p>
                    <div class="row">
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
                                <option value="<?php echo $row['templateID']; ?>"<?php echo ($_GET['template'] == $row['templateID'] ? ' selected' : ''); ?>><?php echo $row['label']; ?></option>
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
                            <div class="row">
                                <div class="col-3 col-md-2"> <!-- PeriodizitätValue -->
                                    <input class="form-control chk-toggle-req-slave" type="number" id="periodicityValue" name="periodicityValue" step="1" lang="en" min="1" value="1" required>
                                </div>
                                <div class="col-7 col-md-3"> <!-- PeriodizitätType -->
                                    <select class="form-control" id="periodicityType" name="periodicityType">
                                        <option value="1">Tag(e)</option>
                                        <option value="2">Woche(n)</option>
                                        <option value="4" selected>Monat(e)</option>
                                        <option value="8">Jahr(e)</option>
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
                                    <input class="form-control radio-toggle-2" type="date" id="validToValue" name="validToValue" min="<?php echo date_format(date_modify(date_create('now'), '+1 day'), 'Y-m-d'); ?>" required disabled>
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
                                    <input class="form-control radio-toggle-4" type="number" id="initialEvents" name="initialEvents" step="1" lang="en" min="1" required disabled>
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

        <h3 class="mt-3" id="registeredStandingOrder">Erfasste Daueraufträge</h3>
        <hr class="mb-4">
        <div class="row">
            <div class="col-12 mb-5">
                <?php
                // SQL-Query bereitstellen
                $sqlquery = "SELECT standingOrder.standingOrderID, standingOrder.label AS standingOrderLabel, standingOrder.template AS templateID, template.label AS templateLabel, standingOrder.periodicityType, standingOrder.periodicityValue, standingOrder.validToValue, standingOrder.handledEvents, standingOrder.remainingEvents, standingOrder.nextExecutionDate, standingOrder.closed FROM standingOrder LEFT JOIN template ON standingOrder.template = template.templateID";
                $result = mysqli_query($userLink, $sqlquery);

                // Prüfen ob Datensätze vorhanden
                if (mysqli_num_rows($result) >= 1):
                ?>
                <div class="table-responsive">                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Erstelldatum</th>
                                <th scope="col">Name</th>
                                <th scope="col">Vorlage</th>
                                <th scope="col">Wiederholung</th>
                                <th scope="col">Nächste Ausführung</th>
                                <th scope="col">Anzahl abgearbeitet</th>
                                <th scope="col">Anzahl verbleibend</th>
                                <th scope="col">Enddatum</th>
                                <th scope="col">Abgeschlossen</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)):
                                switch ($row['periodicityType']) {
                                    case '1':
                                        $row['periodicityType'] = 'Tage';
                                        break;
                                    case '2':
                                        $row['periodicityType'] = 'Wochen';
                                        break;
                                    case '4':
                                        $row['periodicityType'] = 'Monate';
                                        break;
                                    case '8':
                                        $row['periodicityType'] = 'Jahre';
                                        break;
                                }
                            ?>
                            <tr>
                                <td><?php echo date_format(date_create($row['created']), 'd.m.Y'); ?></td>
                                <td><?php echo $row['standingOrderLabel']; ?></td>
                                <td><a href="templates.php?template=<?php echo intval($row['templateID']); ?>"><?php echo $row['templateLabel']; ?></a></td>
                                <td><?php echo 'Alle ' . $row['periodicityValue'] . ' ' . $row['periodicityType']; ?></td>
                                <td><?php echo ($row['nextExecutionDate'] == NULL ? '-' : date_format(date_create($row['nextExecutionDate']), 'd.m.Y')); ?></td>
                                <td><?php echo $row['handledEvents']; ?></td>
                                <td><?php echo ($row['remainingEvents'] == NULL ? '-' : $row['remainingEvents']); ?></td>
                                <td><?php echo ($row['validToValue'] == NULL ? '-' : date_format(date_create($row['validToValue']), 'd.m.Y')); ?></td>
                                <td><?php echo ($row['closed'] == 'N' ? 'Nein' : 'Ja'); ?></td>
                                <td><button type="button" class="btn btn-tr btn-block btn-danger tr-delete" value="standingOrder-<?php echo $row['standingOrderID']; ?>">Löschen</button></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="lead">Keine Einträge gefunden</p>
                <p>Sie haben für die ausgewählte Ziel-Datenbank noch keinen Dauerauftrag erstellt. Erstellen Sie Ihren ersten Dauerauftrag gleich <a href="standingOrder.php#addStandingOrder">hier</a>.</p>
                <?php endif; ?>
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
    <!-- Radiobutton Toggle -->
    <script>
    $("input[name=validToType]").change(function() {
        switch ($(this).val()) {
            case '1':
                $(".radio-toggle-2").prop('disabled', true);
                $(".radio-toggle-4").prop('disabled', true);
                break;
            case '2':
                $(".radio-toggle-2").prop('disabled', false);
                $(".radio-toggle-4").prop('disabled', true);
                break;
            case '4':
                $(".radio-toggle-2").prop('disabled', true);
                $(".radio-toggle-4").prop('disabled', false);
                break;
        }
    })
    </script>
    <!-- Eintrag löschen -->
    <script src="js/trValueDelete.js"></script>
</body>
</html>