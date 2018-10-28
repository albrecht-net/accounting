<?php
// Konfiguration einbinden
require_once '../config.php';

// Prüfen ob Benutzer angemeldet
require '../includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: ../login.php?rd=' . urlencode('settings/account.php'));
    exit();
}

// Überprüfen ob Submit geklickt wurde
if (isset($_POST['submitChangePassword']) && !empty($_POST['inputOldPassword'])) {
    if (!include '../includes/changePassword.inc.php') {
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

    <title>Account</title>
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
                        <a class="dropdown-item" href="../logout.php">Abmelden</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <h3>Passwort ändern</h3>
    <form method="POST" action="account.php">
        <div class="form-group">
            <label for="inputOldPassword">Bisheriges Passwort</label>
            <?php if ($msg['oldPasswordInvalid']): ?>
                <input type="password" class="form-control is-invalid" name="inputOldPassword" id="inputOldPassword" placeholder="Passwort wiederholen" required>
                <div class="invalid-feedback">
                    Falsches Kennwort
                </div>
            <?php else: ?>
                <input type="password" class="form-control" name="inputOldPassword" id="inputOldPassword" placeholder="Passwort wiederholen" required>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="inputPassword1">Neues Passwort</label>
            <?php if ($msg['noPasswordMatch']): ?>
                <input type="password" class="form-control is-invalid" name="inputPassword1" id="inputPassword1" placeholder="Passwort" required>
            <?php else: ?>
                <input type="password" class="form-control" name="inputPassword1" id="inputPassword1" placeholder="Passwort" required>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="inputPassword2">Neues Passwort bestätigen</label>
            <?php if ($msg['noPasswordMatch']): ?>
                <input type="password" class="form-control is-invalid" name="inputPassword2" id="inputPassword2" placeholder="Passwort wiederholen" required>
                <div class="invalid-feedback">
                    Passwörter stimmen nicht überein.
                </div>
            <?php else: ?>
                <input type="password" class="form-control" name="inputPassword2" id="inputPassword2" placeholder="Passwort wiederholen" required>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary" name="submitChangePassword">Passwort ändern</button>
    </form>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>

                