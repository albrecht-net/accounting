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

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/mainSite.css">

    <title>Gespeicherte Vorlagen</title>
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
                        <a class="dropdown-item" href="standingOrder.php">Dauerauftrag</a>
                        <a class="dropdown-item disabled" href="#">Konto</a>
                        <a class="dropdown-item" href="recipient.php">Empfänger</a>
                        <a class="dropdown-item" href="classification.php">Klassifikation</a>
                    </div>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="templates.php">Vorlagen<span class="sr-only">(current)</span></a>
                </li>
            </ul>
            <ul class="navbar-nav navbar-right">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>
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
        <h3 class="mt-3" id="savedTemplates">Gespeicherte Vorlagen</h3>
        <hr class="mb-4">
        <div class="row">
            <div class="col-12 mb-5">
            <?php
                // SQL-Query bereitstellen
                $sqlquery = "SELECT `templateID`, `created`, `label`, `recipient`, `invoiceNo`, `entryText`, `grandTotal`, `debitAccount`, `creditAccount`, `period`, `classification1`, `classification2`, `classification3` FROM `template` ORDER BY `label` ASC";
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
                            <tr class="table-warning">
                            <?php else: ?>
                            <tr>
                            <?php endif; ?>
                                <td><?php echo date_format(date_create($row['created']), 'd.m.Y'); ?></td>
                                <td><a href="buchung.php?<?php echo http_build_query($valueTemplate); ?>"><?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                                <td><?php echo implode(', ', array_keys($valueTemplate)); ?></td>
                                <td><button type="button" class="btn btn-tr btn-block btn-danger tr-delete" value="Template-<?php echo intval($row['templateID']); ?>">Löschen</button></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="lead">Keine Vorlage gefunden</p>
                <p>Sie haben für die ausgewählte Ziel-Datenbank noch keine Vorlage erstellt. Erstellen Sie Ihre erste Vorlage gleich <a href="buchung.php#addTemplate">hier</a>.</p>
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
    <!-- Eintrag löschen -->
    <script src="js/trValueDelete.js"></script>
</body>
</html>