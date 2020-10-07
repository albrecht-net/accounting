<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
}

require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

// Konfiguration einbinden
require_once '../config.php';

// Prüfen ob Benutzer angemeldet
require '../includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: ../login.php?rd=' . urlencode('settings/database.php'));
    exit();
}

// Tabellen-Reihe löschen
if ($_POST['trValueDelete']) {
    if (!include '../includes/deleteDatabase.inc.php') {
        echo date('H:i:s') . ' Datei einbinden fehlgeschlagen';
        exit();
    }
}
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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/mainSite.css">

    <title>Datenbank</title>
</head>
<body>
    <?php
    // Navigation einbinden
    include_once '../core/navigation.php';
    ?>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Settings Navigation -->
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="account.php">Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="database.php">Datenbank</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sitemap.php">Sitemap</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mt-3">
                    <h5 class="card-header" id="newDatabaseNotes">Hinweise für eine neue Datenbank</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <p>Um die Funktion zu gewährleisten, muss sichergestellt werden, dass die Struktur der Datenbank den Vorgaben entspricht. Verwenden Sie deshalb die gegebenen Datenbankvorlagen welche von der Github Repository <a target="_blank" rel="noopener"  href="https://github.com/albrecht-net/accounting-database-journal/releases">albrecht-net.ch/accounting-database-journal</a> abgerufen werden können.</p>
                                <p class="mb-0">Bitte beachten Sie dabei die <a rel="noopener" href="https://raw.githubusercontent.com/albrecht-net/accounting/master/versionOverview.xlsx">Versionsübersicht</a>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card mt-3">    
                    <h5 class="card-header" id="addDatabase">Datenbank hinzufügen</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <?php include_once '../includes/alertProvider.inc.php'; // Alert Provider ?>
                                <form method="POST" action="../includes/addDatabase.inc.php">
                                    <div class="form-group">
                                        <label for="dbHost">Server IP oder Hostname</label>
                                        <input type="text" class="form-control" name="dbHost" id="dbHost" value="<?php echo htmlspecialchars($_GET['dbHost'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="dbPort">Port</label>
                                        <input type="number" class="form-control" name="dbPort" id="dbPort" min="0" value="<?php echo intval($_GET['dbPort']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="dbUsername">Benutzername</label>
                                        <input type="text" class="form-control" name="dbUsername" id="dbUsername" value="<?php echo htmlspecialchars($_GET['dbUsername'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="dbPassword">Passwort</label>
                                        <input type="password" class="form-control" name="dbPassword" id="dbPassword" required>
                                        <small id="dbPasswordHelp" class="form-text text-muted">Hinweis: Das Passwort wird in Klartext in der Datenbank gespeichert! Verwenden Sie einen Datenbank-Benutzer mit eingeschränkten Rechten.</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="dbName">Datenbankname</label>
                                        <input type="text" class="form-control" name="dbName" id="dbName" value="<?php echo htmlspecialchars($_GET['dbName'], ENT_QUOTES, 'UTF-8'); ?>" required>
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
                    <h5 class="card-header" id="linkedDatabase">Gespeicherte Datenbanken</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <?php
                                // SQL-Query bereitstellen
                                $sqlquery = "SELECT d.dbHost, d.dbPort, d.dbUsername, d.dbName, (SELECT CASE WHEN (SELECT userconfig.defaultDb FROM userconfig WHERE userconfig.userID = " . intval($_SESSION['userID']) . ") = dbID THEN 'Y' ELSE 'N' END) AS 'isDefaultDb' FROM `databases` d WHERE userID = " . intval($_SESSION['userID']);
                                $result = mysqli_query($config['link'], $sqlquery);
    
                                // Prüfen ob Datensätze vorhanden
                                if (mysqli_num_rows($result) >= 1): ?>
                                    <table id="dTableDatabase" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">IP / Hostname</th>
                                                <th scope="col">Port</th>
                                                <th scope="col">Benutzername</th>
                                                <th scope="col">Datenbankname</th>
                                                <th scope="col">Standard</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr id="Database-<?php echo intval($row['dbID']); ?>">
                                                <td><?php echo htmlspecialchars($row['dbHost'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo intval($row['dbPort']); ?></td>
                                                <td><?php echo htmlspecialchars($row['dbUsername'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($row['dbName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo ($row['isDefaultDb'] == 'N' ? 'Nein' : 'Ja'); ?></td>
                                                <td><button type="button" class="btn btn-tr btn-block btn-danger tr-delete" value="Database-<?php echo intval($row['dbID']); ?>">Löschen</button></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <p>Beim Löschen einer Datenbank wird nur deren Verknüpfung zu Applikation aufgehoben. Es werden keine Datensätze in der Zieldatenbank gelöscht!</p>
                                    <p>Sie können hier Ihre standard Datenbank ändern.
                                    <div class="row">
                                        <div class="col-6 col-md-3">
                                            <a class="btn btn-primary btn-block" href="../logout.php?forceDatabaseSelect=1" role="button">Ändern</a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p class="lead">Keine Datenbank gefunden</p>
                                    <p>Es wurde noch keine Datenbank mit Ihrem Benutzer verlinkt. Fügen Sie Ihre erste Ziel-Datenbank gleich <a href="#addDatabase">hier</a> hinzu.</p>
                                <?php endif; ?>
                            </div>
                        </div>
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
    <script src="../js/datatablesHelper.js"></script>
    <!-- Eintrag löschen -->
    <script src="../js/trValueDelete.js"></script>
</body>
</html>

                