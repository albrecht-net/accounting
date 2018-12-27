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
if (isset($_POST['submit']) && ($_POST['chkAddTemplate'] == 0)) {
    if (!include 'includes/saveBuchung.inc.php') {
        echo date('H:i:s') . ' Datei einbinden fehlgeschlagen';
        exit();
    }
} elseif (isset($_POST['submit']) && ($_POST['chkAddTemplate'] == 1)) {
    if (!include 'includes/addBuchungTemplate.inc.php') {
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
                    <?php if (intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']) > 0): ?>
                    <a class="nav-link" href="buchung.php">Neue Buchung <span class="badge badge-warning"><?php echo intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']); ?></span><span class="sr-only">pending booking</span><span class="sr-only">(current)</span></a>
                    <?php else: ?>
                    <a class="nav-link" href="buchung.php">Neue Buchung<span class="sr-only">(current)</span></a>
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
        <div class="row">
            <div class="col-md-4 order-md-2">
                <h3 class="mt-3" id="standingOrder">Daueraufträge</h3>
                <hr class="mb-4">

                <?php
                // Prüfen ob Datensätze vorhanden
                if (intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']) < 1): ?>
                <p>Keine anstehende Buchung.</p>

                <?php else: 
                // SQL-Query bereitstellen
                $sqlquery = "SELECT `standingOrderID`, `label` AS `standingOrderLabel`, `nextExecutionDate` FROM `standingOrder` WHERE `nextExecutionDate` <= NOW()";
                $result = mysqli_query($userLink, $sqlquery);
                ?>
                <div class="list-group">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <a href="buchung.php?standingOrder=<?php echo intval($row['standingOrderID']); ?>#newEntry" class="list-group-item list-group-item-action<?php echo ($_GET['standingOrder'] == $row['standingOrderID'] ? ' active' : ''); ?>">
                        <h6 class="mb-0"><?php echo htmlspecialchars($row['standingOrderLabel']); ?></h6>
                        <small>Fällig am: <?php echo date_format(date_create($row['nextExecutionDate']), 'd.m.Y'); ?></small>
                    </a>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-md-8 order-md-1">
                <h3 class="mt-3" id="newEntry">Neue Buchung erfassen</h3>
                <hr class="mb-4">
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
                        <?php elseif ($msg['noTemplateInput']): ?>
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            Bitte wählen Sie mindestens 1 Feld aus, welches in der Vorlage gespeichert werden soll
                        </div>
                        <?php endif ?>         
                        <form action="buchung.php" method="POST">
                            <div class="row">
                                <div class="form-group col-md-3"> <!-- Buchungsdatum -->
                                    <label for="date">Buchungsdatum</label>
                                    <input class="form-control chk-toggle-dis-invert-slave" type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="form-group col-md-2"> <!-- Periode -->
                                    <label for="period">Periode</label>
                                    <?php
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT `periodID`, `label` FROM `period` ORDER BY `label` ASC";
                                    $result = mysqli_query($userLink, $sqlquery);
                
                                    // Prüfen ob Datensätze vorhanden
                                    if (mysqli_num_rows($result) < 1): ?>
                                    <select class="form-control" id="period" name="period">
                                        <option disabled>Keine Datensätze vorhanden</option>
                                    <?php else: ?>
                                    <select class="form-control" id="period" name="period">
                                        <option></option>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <option value="<?php echo $row['periodID']; ?>"<?php echo ($_GET['period'] == $row['periodID'] ? ' selected' : ''); ?>><?php echo $row['label']; ?></option>
                                        <?php endwhile;
                                    endif; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-7"> <!-- Empfänger -->
                                    <label for="recipient">Empfänger</label>
                                    <?php
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT `recipientID`, `label` FROM `recipient` WHERE `active` = 'Y' ORDER BY `label` ASC";
                                    $result = mysqli_query($userLink, $sqlquery);
        
                                    // Prüfen ob Datensätze vorhanden
                                    if (mysqli_num_rows($result) < 1): ?>
                                    <select class="form-control" id="recipient" name="recipient">
                                        <option disabled>Keine Datensätze vorhanden</option>
                                    <?php else: ?>
                                    <select class="form-control" id="recipient" name="recipient">
                                        <option></option>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <option value="<?php echo $row['recipientID']; ?>"<?php echo ($_GET['recipient'] == $row['recipientID'] ? ' selected' : ''); ?>><?php echo $row['label']; ?></option>
                                        <?php endwhile;
                                    endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-5"> <!-- Rechnungsummer -->
                                    <label for="invoiceNo">Rechnungsnummer</label>
                                    <input class="form-control" type="text" id="invoiceNo" name="invoiceNo">
                                </div>
                                <div class="form-group col-md-7"> <!-- Beschreibung -->
                                    <label for="entryText">Beschreibung</label>
                                    <input class="form-control" type="text" id="entryText" name="entryText" value="<?php echo $_GET['entryText']; ?>">
                                </div>  
                            </div>
                            <div class="row">
                                <div class="form-group col-md-5"> <!-- Konto Soll -->
                                    <label for="debitAccount">Konto Soll</label>
                                    <?php
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT `account`.`accountID`, `account`.`label` AS `accountLabel`, `accountCategory`.`label` AS `categoryLabel` FROM `account` LEFT JOIN `accountCategory` ON `account`.`category` = `accountCategory`.`categoryID` WHERE `account`.`active` = 'Y' ORDER BY `accountCategory`.`label` ASC, `account`.`label` ASC";
                                    $result = mysqli_query($userLink, $sqlquery);
                
                                    // Prüfen ob Datensätze vorhanden
                                    if (mysqli_num_rows($result) < 1): ?>
                                    <select class="form-control chk-toggle-req-slave" id="debitAccount" name="debitAccount" required>
                                        <option disabled>Keine Datensätze vorhanden</option>
                                    <?php else: ?>
                                    <select class="form-control chk-toggle-req-slave" id="debitAccount" name="debitAccount" required>
                                        <option></option>
                                        <?php
                                        // Resulat in 1 Array schreiben, sortiert nach Kategorie
                                        $valueArray = [];
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            if ($row['categoryLabel'] != $category) {
                                                $i = 0;
                                            }
        
                                            $category = $row['categoryLabel'];
        
                                            $valueArray[$category][$i] = $row;
                                            $i++;
                                        }
        
                                        // Array in Dropdown ausgeben
                                        foreach ($valueArray as $key => $row1): ?>
                                            <optgroup label="<?php echo $key; ?>">
                                            <?php foreach ($row1 as $key => $row2): ?>
                                                <option value="<?php echo $row2['accountID']; ?>"<?php echo ($_GET['debitAccount'] == $row2['accountID'] ? ' selected' : ''); ?>><?php echo $row2['accountID'] . ' ' . $row2['accountLabel']; ?></option>
                                            <?php endforeach;
                                        endforeach;
                                    endif; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-5"> <!-- Konto Haben -->
                                    <label for="creditAccount">Konto Haben</label>
                                    <?php
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT `account`.`accountID`, `account`.`label` AS `accountLabel`, `accountCategory`.`label` AS `categoryLabel` FROM `account` LEFT JOIN `accountCategory` ON `account`.`category` = `accountCategory`.`categoryID` WHERE `account`.`active` = 'Y' ORDER BY `accountCategory`.`label` ASC, `account`.`label` ASC";
                                    $result = mysqli_query($userLink, $sqlquery);
        
                                    // Prüfen ob Datensätze vorhanden
                                    if (mysqli_num_rows($result) < 1): ?>
                                    <select class="form-control chk-toggle-req-slave" id="creditAccount" name="creditAccount" required>
                                        <option disabled>Keine Datensätze vorhanden</option>
                                    <?php else: ?>
                                    <select class="form-control chk-toggle-req-slave" id="creditAccount" name="creditAccount" required>
                                        <option></option>
                                        <?php
                                        // Resulat in 1 Array schreiben, sortiert nach Kategorie
                                        $valueArray = [];
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            if ($row['categoryLabel'] != $category) {
                                                $i = 0;
                                            }
        
                                            $category = $row['categoryLabel'];
        
                                            $valueArray[$category][$i] = $row;
                                            $i++;
                                        }
        
                                        // Array in Dropdown ausgeben
                                        foreach ($valueArray as $key => $row1): ?>
                                            <optgroup label="<?php echo $key; ?>">
                                            <?php foreach ($row1 as $key => $row2): ?>
                                                <option value="<?php echo $row2['accountID']; ?>"<?php echo ($_GET['creditAccount'] == $row2['accountID'] ? ' selected' : ''); ?>><?php echo $row2['accountID'] . ' ' . $row2['accountLabel']; ?></option>
                                            <?php endforeach;
                                        endforeach;
                                    endif; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-2"> <!-- Betrag -->
                                    <label for="grandTotal">Betrag</label>
                                    <input class="form-control chk-toggle-req-slave" type="number" id="grandTotal" name="grandTotal" step="0.01" lang="en" value="<?php echo $_GET['grandTotal']; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4"> <!-- Klassifikation 1 -->
                                    <label for="classification1">Klassifikation 1</label>
                                    <?php
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT `classificationID`, `label` FROM `classification` ORDER BY `label` ASC";
                                    $result = mysqli_query($userLink, $sqlquery);
        
                                    // Prüfen ob Datensätze vorhanden
                                    if (mysqli_num_rows($result) < 1): ?>
                                    <select class="form-control" id="classification1" name="classification1">
                                        <option disabled>Keine Datensätze vorhanden</option>
                                    <?php else: ?>
                                    <select class="form-control" id="classification1" name="classification1">
                                        <option></option>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <option value="<?php echo $row['classificationID']; ?>"<?php echo ($_GET['classification1'] == $row['classificationID'] ? ' selected' : ''); ?>><?php echo $row['label']; ?></option>
                                        <?php endwhile;
                                    endif; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4"> <!-- Klassifikation 2 -->
                                    <label for="classification2">Klassifikation 2</label>
                                    <?php
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT `classificationID`, `label` FROM `classification` ORDER BY `label` ASC";
                                    $result = mysqli_query($userLink, $sqlquery);
        
                                    // Prüfen ob Datensätze vorhanden
                                    if (mysqli_num_rows($result) < 1): ?>
                                    <select class="form-control" id="classification2" name="classification2">
                                        <option disabled>Keine Datensätze vorhanden</option>
                                    <?php else: ?>
                                    <select class="form-control" id="2" name="classification2">
                                        <option></option>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <option value="<?php echo $row['classificationID']; ?>"<?php echo ($_GET['classification2'] == $row['classificationID'] ? ' selected' : ''); ?>><?php echo $row['label']; ?></option>
                                        <?php endwhile;
                                    endif; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4"> <!-- Klassifikation 3 -->
                                    <label for="classification3">Klassifikation 3</label>
                                    <?php
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT `classificationID`, `label` FROM `classification` ORDER BY `label` ASC";
                                    $result = mysqli_query($userLink, $sqlquery);
        
                                    // Prüfen ob Datensätze vorhanden
                                    if (mysqli_num_rows($result) < 1): ?>
                                    <select class="form-control" id="classification3" name="classification3">
                                        <option disabled>Keine Datensätze vorhanden</option>
                                    <?php else: ?>
                                    <select class="form-control" id="classification3" name="classification3">
                                        <option></option>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <option value="<?php echo $row['classificationID']; ?>"<?php echo ($_GET['classification3'] == $row['classificationID'] ? ' selected' : ''); ?>><?php echo $row['label']; ?></option>
                                        <?php endwhile;
                                    endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12"> <!-- Buchungsreferenz -->
                                    <label for="entryReference">Buchungsreferenz</label>
                                    <?php
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT `journal`.`entryID`, `journal`.`date`, `recipient`.`label` AS `recipient`, `journal`.`grandTotal` FROM `journal` LEFT JOIN `recipient` ON `journal`.`recipient` = `recipient`.`recipientID` RIGHT JOIN `account` AS `creditAccount` ON `journal`.`creditAccount` = `creditAccount`.`accountID` RIGHT JOIN `account` AS `debitAccount` ON `journal`.`debitAccount` = `debitAccount`.`accountID` WHERE `journal`.`reconcilation` = 'N' AND (`creditAccount`.`reconcilationAllow` = 'Y' OR `debitAccount`.`reconcilationAllow` = 'Y') ORDER BY `journal`.`created` ASC";
                                    $result = mysqli_query($userLink, $sqlquery);
        
                                    // Prüfen ob Datensätze vorhanden
                                    if (mysqli_num_rows($result) < 1): ?>
                                    <select class="form-control chk-toggle-dis-invert-slave" id="entryReference" name="entryReference" multiple>
                                        <option disabled>Keine Datensätze vorhanden</option>
                                    <?php else: ?>
                                    <select class="form-control chk-toggle-dis-invert-slave" id="entryReference" name="entryReference[]" multiple>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <option value="<?php echo $row['entryID']; ?>"><?php echo $row['date'] . ', ' . $row['recipient'] . ', CHF ' . $row['grandTotal']; ?></option>
                                        <?php endwhile;
                                    endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-check"> <!-- Abstimmung -->
                                <input class="form-check-input" type="checkbox" id="reconcilation" name="reconcilation" value="1" disabled>
                                <label class="form-check-label" for="reconcilation">Abstimmung</label>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-3">
                                    <button type="submit" class="btn btn-primary btn-block" name="submit">Speichern</button>
                                </div>
                            </div>
                    </div>
                </div>
        
                <h3 class="mt-3" id="addTemplate">Als Vorlage speichern</h3>
                <hr class="mb-4">
                <div class="row">
                    <div class="col-12 mb-5">
                        <p>Beim Speichern einer Buchung wird eine Vorlage erstellt, welche alle aktuell ausgefüllten Felder beinhaltet. Hinweis: Beim Speichern als Vorlage wird keine Buchung erstellt!</p>
                        <p>Es kann dabei zwischen zwei Arten ausgewählt werden:</p>
                        <dl class="row">
                            <dt class="col-sm-3">Applikation</dt>
                            <dd class="col-sm-9">Beim Speichern in der Applikation wird die Vorlage gespeichert und kann über die Menuleiste der Applikation ausgewählt und geöffnet werden.</dd>
                            <dt class="col-sm-3">Lesezeichen</dt>
                            <dd class="col-sm-9">Beim Speichern als Lesezeichen wird eine URL erzeugt, welche als Lesezeichen verwenden werden kann. Die Vorlage wird nicht zusätzlich gespeichert.</dd>
                        </dl>
                        <div class="form-group form-check"> <!-- Als Vorlage -->
                            <input class="form-check-input chk-toggle-master" type="checkbox" id="chkAddTemplate" name="chkAddTemplate" value="1">
                            <label class="form-check-label" for="chkAddTemplate">Als Vorlage hinzufügen</label>
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

                        <!-- /form -->
                        </form>

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
    <!-- Checkbox Toggle -->
    <script>
        $(".chk-toggle-master").on('click', function() {
            if($(this).prop('checked')) {
                $(".chk-toggle-dis-slave").prop('disabled', false);
                $(".chk-toggle-dis-invert-slave").prop('disabled', true);
                $(".chk-toggle-req-slave").prop('required', false);
            } else {
                $(".chk-toggle-dis-slave").prop('disabled', true);
                $(".chk-toggle-dis-invert-slave").prop('disabled', false);
                $(".chk-toggle-req-slave").prop('required', true);
            }
        })
    </script>
</body>
</html>