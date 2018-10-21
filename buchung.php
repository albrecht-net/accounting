<?php
// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php?rd=' . urlencode('buchung.php'));
    exit();
}

// Mit Ziel Datenbank verbinden
require_once 'includes/userDbConnect.inc.php';

// Überprüfen ob Submit geklickt wurde
if (isset($_POST['submit'])) {
    if (!include 'includes/saveBuchung.inc.php') {
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
    <form action="buchung.php" method="POST">
        <div class="form-group"> <!-- Buchungsdatum -->
            <label for="datum">Buchunsdatum</label>
            <input class="form-control" type="date" id="datum" name="datum" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group"> <!-- Empfänger -->
            <label for="empfänger">Empfänger</label>
            <?php
            // SQL-Query bereitstellen
            $sqlquery = "SELECT `empfängerID`, `bezeichnung` FROM `empfänger` WHERE `aktiv` = 'Y' ORDER BY `bezeichnung` ASC";
            $result = mysqli_query($userLink, $sqlquery);

            // Prüfen ob Datensätze vorhanden
            if (mysqli_num_rows($result) < 1): ?>
            <select class="form-control" id="empfänger" name="empfänger" disabled>
                <option>Keine Datensätze vorhanden</option>
            <?php else: ?>
            <select class="form-control" id="empfänger" name="empfänger">
                <option></option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?php echo $row['empfängerID']; ?>"><?php echo $row['bezeichnung']; ?></option>
                <?php endwhile;
            endif; ?>
            </select>
        </div>
        <div class="form-group"> <!-- Rechnungsummer -->
            <label for="reNummer">Rechnungsnummer</label>
            <input class="form-control" type="text" id="reNummer" name="reNummer">
        </div>
        <div class="form-group"> <!-- Beschreibung -->
            <label for="buchungstext">Beschreibung</label>
            <input class="form-control" type="text" id="buchungstext" name="buchungstext">
        </div>
        <div class="form-group"> <!-- Betrag -->
            <label for="totalbetrag">Betrag</label>
            <input class="form-control" type="number" id="totalbetrag" name="totalbetrag" step="0.01" lang="en">
        </div>
        <div class="form-group"> <!-- Konto Soll -->
            <label for="kontoSoll">Konto Soll</label>
            <?php
            // SQL-Query bereitstellen
            $sqlquery = "SELECT `kontoID`, `bezeichnung` FROM `konten`";
            $result = mysqli_query($userLink, $sqlquery);

            // Prüfen ob Datensätze vorhanden
            if (mysqli_num_rows($result) < 1): ?>
            <select class="form-control" id="kontoSoll" name="kontoSoll" disabled>
                <option>Keine Datensätze vorhanden</option>
            <?php else: ?>
            <select class="form-control" id="kontoSoll" name="kontoSoll">
                <option></option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?php echo $row['kontoID']; ?>"><?php echo str_pad($row['kontoID'], 5, ' ') . $row['bezeichnung']; ?></option>
                <?php endwhile;
            endif; ?>
            </select>
        </div>
        <div class="form-group">  <!-- Konto Haben -->
            <label for="kontoHaben">Konto Haben</label>
            <?php
            // SQL-Query bereitstellen
            $sqlquery = "SELECT `kontoID`, `bezeichnung` FROM `konten`";
            $result = mysqli_query($userLink, $sqlquery);

            // Prüfen ob Datensätze vorhanden
            if (mysqli_num_rows($result) < 1): ?>
            <select class="form-control" id="kontoHaben" name="kontoHaben" disabled>
                <option>Keine Datensätze vorhanden</option>
            <?php else: ?>
            <select class="form-control" id="kontoHaben" name="kontoHaben">
                <option></option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?php echo $row['kontoID']; ?>"><?php echo str_pad($row['kontoID'], 5, ' ') . $row['bezeichnung']; ?></option>
                <?php endwhile;
            endif; ?>
            </select>
        </div>
        <div class="form-group">  <!-- Periode -->
            <label for="periode">Periode</label>
            <?php
            // SQL-Query bereitstellen
            $sqlquery = "SELECT `periodeID`, `wert` FROM `periode` ORDER BY `wert` ASC";
            $result = mysqli_query($userLink, $sqlquery);

            // Prüfen ob Datensätze vorhanden
            if (mysqli_num_rows($result) < 1): ?>
            <select class="form-control" id="periode" name="periode" disabled>
                <option>Keine Datensätze vorhanden</option>
            <?php else: ?>
            <select class="form-control" id="periode" name="periode">
                <option></option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?php echo $row['periodeID']; ?>"><?php echo $row['bezeichnung']; ?></option>
                <?php endwhile;
            endif; ?>
            </select>
        </div>
        <div class="form-group">  <!-- Klassifikation 1 -->
            <label for="klassifikation1">Klassifikation 1</label>
            <?php
            // SQL-Query bereitstellen
            $sqlquery = "SELECT `klassifikationID`, `bezeichnung` FROM `klassifikation` ORDER BY `bezeichnung` ASC";
            $result = mysqli_query($userLink, $sqlquery);

            // Prüfen ob Datensätze vorhanden
            if (mysqli_num_rows($result) < 1): ?>
            <select class="form-control" id="klassifikation1" name="klassifikation1" disabled>
                <option>Keine Datensätze vorhanden</option>
            <?php else: ?>
            <select class="form-control" id="klassifikation1" name="klassifikation1">
                <option></option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?php echo $row['klassifikationID']; ?>"><?php echo $row['bezeichnung']; ?></option>
                <?php endwhile;
            endif; ?>
            </select>
        </div>
        <div class="form-group"> <!-- Klassifikation 2 -->
            <label for="klassifikation2">Klassifikation 2</label>
            <?php
            // SQL-Query bereitstellen
            $sqlquery = "SELECT `klassifikationID`, `bezeichnung` FROM `klassifikation` ORDER BY `bezeichnung` ASC";
            $result = mysqli_query($userLink, $sqlquery);

            // Prüfen ob Datensätze vorhanden
            if (mysqli_num_rows($result) < 1): ?>
            <select class="form-control" id="klassifikation2" name="klassifikation2" disabled>
                <option>Keine Datensätze vorhanden</option>
            <?php else: ?>
            <select class="form-control" id="klassifikation2" name="klassifikation2">
                <option></option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?php echo $row['klassifikationID']; ?>"><?php echo $row['bezeichnung']; ?></option>
                <?php endwhile;
            endif; ?>
            </select>
        </div>
        <div class="form-group"> <!-- Klassifikation 3 -->
            <label for="klassifikation3">Klassifikation 3</label>
            <?php
            // SQL-Query bereitstellen
            $sqlquery = "SELECT `klassifikationID`, `bezeichnung` FROM `klassifikation` ORDER BY `bezeichnung` ASC";
            $result = mysqli_query($userLink, $sqlquery);

            // Prüfen ob Datensätze vorhanden
            if (mysqli_num_rows($result) < 1): ?>
            <select class="form-control" id="klassifikation3" name="klassifikation3" disabled>
                <option>Keine Datensätze vorhanden</option>
            <?php else: ?>
            <select class="form-control" id="klassifikation3" name="klassifikation3">
                <option></option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?php echo $row['klassifikationID']; ?>"><?php echo $row['bezeichnung']; ?></option>
                <?php endwhile;
            endif; ?>
            </select>
        </div>
        <div class="form-check"> <!-- Abstimmung -->
            <input class="form-check-input" type="checkbox" id="abstimmung" name="abstimmung" value="1">
            <label class="form-check-label" for="abstimmung">Abstimmung</label>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Speichern</button>
    </form>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>