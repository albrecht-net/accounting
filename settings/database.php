<?php
// Konfiguration einbinden
require_once '../config.php';

// Prüfen ob Benutzer angemeldet
require '../includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: ../login.php?rd=' . urlencode('settings/database.php'));
    exit();
}

// Überprüfen ob Submit geklickt wurde
if (isset($_POST['submitAddDb'])) {
    if (!include '../includes/addDatabase.inc.php') {
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

    <title>Datenbank</title>
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
                    <a class="nav-link" href="../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../buchung.php">Neue Buchung</a>
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
            </ul>
            <ul class="navbar-nav navbar-right">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_SESSION['username']; ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item disabled" href="#">Mein Profil</a>
                        <a class="dropdown-item" href="account.php">Einstellungen</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="./logout.php">Abmelden</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <h3>Datenbank hinzufügen</h3>

    <!-- Statusmeldungen -->
    <?php if ($msg['successAddDb']): ?>
    <div class="alert alert-primary" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>        
        <h4>Neue Datenbank erfolgreich gespeichert</h4>
        <?php if (mysqli_num_rows($result) >= 1): ?>
        <p>Es wurden folgende Tabellen in der Datenbank gefunden:<p>
        <ul class="list-unstyled">
            <?php while ($row = mysqli_fetch_row($result)): ?>
            <li><?php echo $row[0]; ?></li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <p><i>In dieser Datenbank wurden keine Tabellen erkannt</i></p>
        <?php endif; ?>
    </div>
    <?php elseif ($msg['tempLinkError']): ?>
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Verbindung fehlgeschlagen!</h4>
        <p>Es wurde vergeblich versucht eine temporäre Verbindung zur angegebenen Datenbank aufzubauen. Bitte überprüfen Sie die Angaben.</p>
        <hr>
        <p class="mb-0">Folgender Fehler wurde von MySQL ausgegeben: <i><?php echo mysqli_connect_error($tempLink); ?></i></p>
    </div>
    <?php endif; ?>

    <form method="POST" action="database.php">
        <div class="form-group">
            <label for="dbHost">Server IP oder Hostname</label>
            <input type="text" class="form-control" name="dbHost" id="dbHost" value="<?php echo $_GET['dbHost']; ?>" required>
        </div>
        <div class="form-group">
            <label for="dbPort">Port</label>
            <input type="number" class="form-control" name="dbPort" id="dbPort" min="0" value="<?php echo $_GET['dbPort']; ?>" required>
        </div>
        <div class="form-group">
            <label for="dbUsername">Benutzername</label>
            <input type="text" class="form-control" name="dbUsername" id="dbUsername" value="<?php echo $_GET['dbUsername']; ?>" required>
        </div>
        <div class="form-group">
            <label for="dbPassword">Passwort</label>
            <input type="password" class="form-control" name="dbPassword" id="dbPassword" required>
            <small id="dbPassword" class="form-text text-muted">Hinweis: Das Passwort wird in Klartext in der Datenbank gespeichert! Verwenden Sie einen Datenbank-Benutzer mit eingeschränkten Rechten.</small>
        </div>
        <div class="form-group">
            <label for="dbName">Datenbankname</label>
            <input type="text" class="form-control" name="dbName" id="dbName" value="<?php echo $_GET['dbName']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary" name="submitAddDb">Hinzufügen</button>
    </form>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>

                