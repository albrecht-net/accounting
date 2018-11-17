<?php
// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php?rd=' . urlencode('templates.php'));
    exit();
}

// Überprüfen ob Submit geklickt wurde
if ($_POST['tableContent'] == 'templates') {
    if (!include 'includes/deleteTemplate.inc.php') {
        echo date('H:i:s') . ' Datei einbinden fehlgeschlagen';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

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
                    <a class="nav-link" href="buchung.php">Neue Buchung</a>
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
                <li class="nav-item active">
                    <a class="nav-link" href="templates.php">Vorlagen<span class="sr-only">(current)</span></a>
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
        <h3 class="mt-3" id="newEntry">Gespeicherte Vorlagen</h3>
        <hr class="mb-4">
        <?php if ($_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>        
        <div class="row">
            <div class="col-12 mb-5">
            <?php
                // SQL-Query bereitstellen
                $sqlquery = "SELECT `templateID`, `datumErstellt`, `name`, `value` FROM `templates` WHERE `userID` = " . intval($_SESSION['userID']) . " AND `dbID` = " . intval($_SESSION['userDb']['dbID']);
                $result = mysqli_query($config['link'], $sqlquery);

                // Prüfen ob Datensätze vorhanden
                if (mysqli_num_rows($result) >= 1):
                $i = 1; 
                ?>
                <div class="table-responsive">                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Erstelldatum</th>
                                <th scope="col">Name</th>
                                <th scope="col">Definierte Werte</th>
                                <th scope="cold"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)):
                                
                            // Decode json value
                            $valueDecoded = json_decode($row['value'], TRUE); ?>
                            <tr>
                                <th scope="row"><?php echo $i; ?></th>
                                <td><?php echo date_format(date_create($row['datumErstellt']), 'd.m.Y'); ?></td>
                                <td><a href="buchung.php?<?php echo http_build_query($valueDecoded); ?>"><?php echo $row['name']; ?></a></td>
                                <td><?php echo implode(', ', array_keys($valueDecoded)); ?></td>
                                <td><button type="button" class="btn btn-secondary tr-delete" value="templates-<?php echo $row['templateID']; ?>">Löschen</button></td>
                                <?php $i++; ?>
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