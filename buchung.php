<?php
// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php?rd=' . urlencode('buchung.php'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" async></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" async></script>

    <title>Buchung erfassen</title>
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
                <li class="nav-item active">
                    <a class="nav-link" href="#">Neue Buchung <span class="sr-only">(current)</span></a>
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
                        <a class="dropdown-item disabled" href="#">Einstellungen</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Abmelden</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <form>
        <div class="form-group">
            <label for="datum">Buchunsdatum</label>
            <input class="form-control" type="date" id="datum" name="datum" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group">
            <label for="empfänger">Empfänger</label>
            <select class="form-control" id="empfänger" name="empfänger">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="reNummer">Rechnungsnummer</label>
            <input class="form-control" type="text" id="reNummer" name="reNummer">
        </div>
        <div class="form-group">
            <label for="buchungstext">Beschreibung</label>
            <input class="form-control" type="text" id="buchungstext" name="buchungstext">
        </div>
        <div class="form-group">
            <label for="totalbetrag">Betrag</label>
            <input class="form-control" type="number" id="totalbetrag" name="totalbetrag">
        </div>
        <div class="form-group">
            <label for="kontoSoll">Konto Soll</label>
            <select class="form-control" id="kontoSoll" name="kontoSoll">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="kontoHaben">Konto Haben</label>
            <select class="form-control" id="kontoHaben" name="kontoHaben">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="periode">Periode</label>
            <select class="form-control" id="periode" name="periode">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="klassifikation1">Klassifikation 1</label>
            <select class="form-control" id="klassifikation1" name="klassifikation1">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="klassifikation2">Klassifikation 2</label>
            <select class="form-control" id="klassifikation2" name="klassifikation2">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="klassifikation3">Klassifikation 3</label>
            <select class="form-control" id="klassifikation3" name="klassifikation3">
                <option>1</option>
            </select>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="abstimmung" name="abstimmung" value="1">
            <label class="form-check-label" for="abstimmung">Absstimmung</label>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Anmelden</button>
    </form>
</body>
</html>