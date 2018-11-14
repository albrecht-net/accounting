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
if (isset($_POST['submit']) && ($_POST['addAsTemplate'] == 0)) {
    if (!include 'includes/saveBuchung.inc.php') {
        echo date('H:i:s') . ' Datei einbinden fehlgeschlagen';
        exit();
    }
} elseif (isset($_POST['submit']) && ($_POST['addAsTemplate'] == 1)) {
    if (!include 'includes/addBuchungTemplate.inc.php') {
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
                    <a class="nav-link" href="buchung.php">Neue Buchung <span class="sr-only">(current)</span></a>
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
                        <a class="dropdown-item" href="settings/account.php">Einstellungen</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Abmelden</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h3 class="mt-3" id="newEntry">Neue Buchung erfassen</h3>
        <hr class="mb-4">
        <?php if ($_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>        
        <div class="row">
            <div class="col-12 mb-5">
                <?php if ($msg['success']): ?>
                <div class="alert alert-primary alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    Eintrag erfolgreich gespeichert
                </div>
                <?php elseif ($msg['sqlInsertError']): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>MySQL Error:</strong> <?php echo mysqli_error($userLink); ?>
                </div>
                <?php elseif ($msg['sqlUpdateError']): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    Die neue Buchung konnte erfolgreich in der Datenbank gespeichert werden. Es trat jedoch ein Fehler beim Updaten der Abstimmung auf! <strong>MySQL Error:</strong> <?php echo mysqli_error($userLink); ?>
                </div>
                <?php elseif ($msg['templateURL']['set']): ?>
                <div class="alert alert-primary alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4>Vorlage als Lesezeichen</h4>
                    <p>Um Fehler zu vermeiden, sollte diese Vorlage nur mit der aktuell gewählten Zieldatenbank genutzt werden. Der untenstehende Link kann nun als Lesezeichen dem Browser hinzugefügt werden:</p>
                    <hr>
                    <a href="buchung.php?<?php echo http_build_query($msg['templateURL']['data']); ?>" class="alert-link"><?php echo (!empty($msg['templateURL']['name']) ? $msg['templateURL']['name'] : 'Buchungs-Vorlage'); ?></a>
                </div>
                <?php endif ?>         
                <form action="buchung.php" method="POST">
                    <div class="row">
                        <div class="form-group col-md-3"> <!-- Buchungsdatum -->
                            <label for="datum">Buchungsdatum</label>
                            <input class="form-control" type="date" id="datum" name="datum" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group col-md-2"> <!-- Periode -->
                            <label for="periode">Periode</label>
                            <?php
                            // SQL-Query bereitstellen
                            $sqlquery = "SELECT `periodeID`, `bezeichnung` FROM `periode` ORDER BY `bezeichnung` ASC";
                            $result = mysqli_query($userLink, $sqlquery);
        
                            // Prüfen ob Datensätze vorhanden
                            if (mysqli_num_rows($result) < 1): ?>
                            <select class="form-control" id="periode" name="periode">
                                <option disabled>Keine Datensätze vorhanden</option>
                            <?php else: ?>
                            <select class="form-control" id="periode" name="periode">
                                <option></option>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <option value="<?php echo $row['periodeID']; ?>"<?php echo ($_GET['periode'] == $row['periodeID'] ? ' selected' : ''); ?>><?php echo $row['bezeichnung']; ?></option>
                                <?php endwhile;
                            endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-7"> <!-- Empfänger -->
                            <label for="empfänger">Empfänger</label>
                            <?php
                            // SQL-Query bereitstellen
                            $sqlquery = "SELECT `empfängerID`, `bezeichnung` FROM `empfänger` WHERE `aktiv` = 'Y' ORDER BY `bezeichnung` ASC";
                            $result = mysqli_query($userLink, $sqlquery);

                            // Prüfen ob Datensätze vorhanden
                            if (mysqli_num_rows($result) < 1): ?>
                            <select class="form-control" id="empfänger" name="empfänger">
                                <option disabled>Keine Datensätze vorhanden</option>
                            <?php else: ?>
                            <select class="form-control" id="empfänger" name="empfänger">
                                <option></option>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <option value="<?php echo $row['empfängerID']; ?>"<?php echo ($_GET['empfänger'] == $row['empfängerID'] ? ' selected' : ''); ?>><?php echo $row['bezeichnung']; ?></option>
                                <?php endwhile;
                            endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-5"> <!-- Rechnungsummer -->
                            <label for="reNummer">Rechnungsnummer</label>
                            <input class="form-control" type="text" id="reNummer" name="reNummer">
                        </div>
                        <div class="form-group col-md-7"> <!-- Beschreibung -->
                            <label for="buchungstext">Beschreibung</label>
                            <input class="form-control" type="text" id="buchungstext" name="buchungstext" value="<?php echo $_GET['buchungstext']; ?>">
                        </div>  
                    </div>
                    <div class="row">
                        <div class="form-group col-md-5"> <!-- Konto Soll -->
                            <label for="kontoSoll">Konto Soll</label>
                            <?php
                            // SQL-Query bereitstellen
                            $sqlquery = "SELECT `kontoID`, `bezeichnung` FROM `konto`";
                            $result = mysqli_query($userLink, $sqlquery);
        
                            // Prüfen ob Datensätze vorhanden
                            if (mysqli_num_rows($result) < 1): ?>
                            <select class="form-control chk-toggle-req-slave" id="kontoSoll" name="kontoSoll" required>
                                <option disabled>Keine Datensätze vorhanden</option>
                            <?php else: ?>
                            <select class="form-control chk-toggle-req-slave" id="kontoSoll" name="kontoSoll" required>
                                <option></option>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <option value="<?php echo $row['kontoID']; ?>"<?php echo ($_GET['kontoSoll'] == $row['kontoID'] ? ' selected' : ''); ?>><?php echo str_pad($row['kontoID'], 5, ' ') . $row['bezeichnung']; ?></option>
                                <?php endwhile;
                            endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-5"> <!-- Konto Haben -->
                            <label for="kontoHaben">Konto Haben</label>
                            <?php
                            // SQL-Query bereitstellen
                            $sqlquery = "SELECT `kontoID`, `bezeichnung` FROM `konto`";
                            $result = mysqli_query($userLink, $sqlquery);

                            // Prüfen ob Datensätze vorhanden
                            if (mysqli_num_rows($result) < 1): ?>
                            <select class="form-control chk-toggle-req-slave" id="kontoHaben" name="kontoHaben" required>
                                <option disabled>Keine Datensätze vorhanden</option>
                            <?php else: ?>
                            <select class="form-control chk-toggle-req-slave" id="kontoHaben" name="kontoHaben" required>
                                <option></option>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <option value="<?php echo $row['kontoID']; ?>"<?php echo ($_GET['kontoHaben'] == $row['kontoID'] ? ' selected' : ''); ?>><?php echo str_pad($row['kontoID'], 5, ' ') . $row['bezeichnung']; ?></option>
                                <?php endwhile;
                            endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2"> <!-- Betrag -->
                            <label for="totalbetrag">Betrag</label>
                            <input class="form-control chk-toggle-req-slave" type="number" id="totalbetrag" name="totalbetrag" step="0.01" lang="en" value="<?php echo $_GET['totalbetrag']; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4"> <!-- Klassifikation 1 -->
                            <label for="klassifikation1">Klassifikation 1</label>
                            <?php
                            // SQL-Query bereitstellen
                            $sqlquery = "SELECT `klassifikationID`, `bezeichnung` FROM `klassifikation` ORDER BY `bezeichnung` ASC";
                            $result = mysqli_query($userLink, $sqlquery);

                            // Prüfen ob Datensätze vorhanden
                            if (mysqli_num_rows($result) < 1): ?>
                            <select class="form-control" id="klassifikation1" name="klassifikation1">
                                <option disabled>Keine Datensätze vorhanden</option>
                            <?php else: ?>
                            <select class="form-control" id="klassifikation1" name="klassifikation1">
                                <option></option>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <option value="<?php echo $row['klassifikationID']; ?>"<?php echo ($_GET['klassifikation1'] == $row['klassifikationID'] ? ' selected' : ''); ?>><?php echo $row['bezeichnung']; ?></option>
                                <?php endwhile;
                            endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4"> <!-- Klassifikation 2 -->
                            <label for="klassifikation2">Klassifikation 2</label>
                            <?php
                            // SQL-Query bereitstellen
                            $sqlquery = "SELECT `klassifikationID`, `bezeichnung` FROM `klassifikation` ORDER BY `bezeichnung` ASC";
                            $result = mysqli_query($userLink, $sqlquery);

                            // Prüfen ob Datensätze vorhanden
                            if (mysqli_num_rows($result) < 1): ?>
                            <select class="form-control" id="klassifikation2" name="klassifikation2">
                                <option disabled>Keine Datensätze vorhanden</option>
                            <?php else: ?>
                            <select class="form-control" id="klassifikation2" name="klassifikation2">
                                <option></option>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <option value="<?php echo $row['klassifikationID']; ?>"<?php echo ($_GET['klassifikation2'] == $row['klassifikationID'] ? ' selected' : ''); ?>><?php echo $row['bezeichnung']; ?></option>
                                <?php endwhile;
                            endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4"> <!-- Klassifikation 3 -->
                            <label for="klassifikation3">Klassifikation 3</label>
                            <?php
                            // SQL-Query bereitstellen
                            $sqlquery = "SELECT `klassifikationID`, `bezeichnung` FROM `klassifikation` ORDER BY `bezeichnung` ASC";
                            $result = mysqli_query($userLink, $sqlquery);

                            // Prüfen ob Datensätze vorhanden
                            if (mysqli_num_rows($result) < 1): ?>
                            <select class="form-control" id="klassifikation3" name="klassifikation3">
                                <option disabled>Keine Datensätze vorhanden</option>
                            <?php else: ?>
                            <select class="form-control" id="klassifikation3" name="klassifikation3">
                                <option></option>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <option value="<?php echo $row['klassifikationID']; ?>"<?php echo ($_GET['klassifikation3'] == $row['klassifikationID'] ? ' selected' : ''); ?>><?php echo $row['bezeichnung']; ?></option>
                                <?php endwhile;
                            endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12"> <!-- Buchungsreferenz -->
                            <label for="buchungsreferenz">Buchungsreferenz</label>
                            <?php
                            // SQL-Query bereitstellen
                            $sqlquery = "SELECT `journal`.`buchungID`, `journal`.`datum`, `empfänger`.`bezeichnung` AS `empfänger`, `journal`.`totalbetrag` FROM `journal` LEFT JOIN `empfänger` ON `journal`.`empfänger` = `empfänger`.`empfängerID` RIGHT JOIN `konto` AS `kontoHaben` ON `journal`.`kontoHaben` = `kontoHaben`.`kontoID` RIGHT JOIN `konto` AS `kontoSoll` ON `journal`.`kontoSoll` = `kontoSoll`.`kontoID` WHERE `journal`.`abstimmung` = 'N' AND (`kontoHaben`.`abstMöglich` = 'Y' OR `kontoSoll`.`abstMöglich` = 'Y') ORDER BY `journal`.`datumErstellt` ASC";
                            $result = mysqli_query($userLink, $sqlquery);

                            // Prüfen ob Datensätze vorhanden
                            if (mysqli_num_rows($result) < 1): ?>
                            <select class="form-control" id="buchungsreferenz" name="buchungsreferenz" multiple>
                                <option disabled>Keine Datensätze vorhanden</option>
                            <?php else: ?>
                            <select class="form-control" id="buchungsreferenz" name="buchungsreferenz[]" multiple>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <option value="<?php echo $row['buchungID']; ?>"><?php echo $row['datum'] . ', ' . $row['empfänger'] . ', CHF ' . $row['totalbetrag']; ?></option>
                                <?php endwhile;
                            endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group form-check"> <!-- Abstimmung -->
                        <input class="form-check-input" type="checkbox" id="abstimmung" name="abstimmung" value="1" disabled>
                        <label class="form-check-label" for="abstimmung">Abstimmung</label>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3">
                            <button type="submit" class="btn btn-primary btn-block" name="submit">Speichern</button>
                        </div>
                    </div>
            </div>
        </div>

        <h3 class="mt-3" id="addTemplate">Als Favorit speichern</h3>
        <hr class="mb-4">
        <div class="row">
            <div class="col-12 mb-5">
                <p>Beim Speichern einer Buchung als Favorit wird eine Vorlage erstellt, welche alle aktuell ausgefüllten Felder beinhaltet. Hinweis: Beim Speichern als Favorit wird keine Buchung erstellt!</p>
                <p>Es kann dabei zwischen zwei Arten ausgewählt werden:</p>
                <dl class="row">
                    <dt class="col-sm-3">Applikation</dt>
                    <dd class="col-sm-9">Beim Speichern in der Applikation wird die Vorlage gespeichert und kann über die Menuleiste der Applikation ausgewählt und geöffnet werden.</dd>
                    <dt class="col-sm-3">Lesezeichen</dt>
                    <dd class="col-sm-9">Beim Speichern als Lesezeichen wird eine URL erzeugt, welche als Lesezeichen verwenden werden kann. Die Vorlage wird nicht zusätzlich gespeichert.</dd>
                </dl>
                <div class="form-group form-check"> <!-- Als Vorlage -->
                    <input class="form-check-input chk-toggle-master" type="checkbox" id="addAsTemplate" name="addAsTemplate" value="1">
                    <label class="form-check-label" for="addAsTemplate">Als Vorlage hinzufügen</label>
                </div>
                <div class="row">
                    <div class="form-group col-12"> <!-- Beschreibung -->
                        <label for="nameTemplate">Beschreibung</label>
                        <input class="form-control chk-toggle-dis-slave" type="text" id="nameTemplate" name="nameTemplate" required disabled>
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input chk-toggle-dis-slave" type="radio" name="radioTemplate" id="radioTemplate1" value="1" checked disabled>
                    <label class="form-check-label" for="radioTemplate1">
                        In der Applikation
                    </label>
                </div>
                <div class="form-group form-check">
                    <input class="form-check-input chk-toggle-dis-slave" type="radio" name="radioTemplate" id="radioTemplate2" value="2" disabled>
                    <label class="form-check-label" for="radioTemplate2">
                        Als Lesezeichen
                    </label>
                </div>
                </form>
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
    <!-- Checkbox Toggle -->
    <script>
        $(".chk-toggle-master").on('click', function() {
            if($(this).prop('checked')) {
                $(".chk-toggle-dis-slave").prop('disabled', false);
                $(".chk-toggle-req-slave").prop('required', false);
            } else {
                $(".chk-toggle-dis-slave").prop('disabled', true);
                $(".chk-toggle-req-slave").prop('required', true);
            }
        })
    </script>
</body>
</html>