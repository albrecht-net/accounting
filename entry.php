<?php
// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php?rd=' . urlencode('entry.php'));
    exit();
}

// Mit Ziel Datenbank verbinden
require_once 'includes/userDbConnect.inc.php';

// Dauerauftrag prüfen und bereitstellen
if (isset($_GET['standingOrder'])) {
    // StandingOrderID temp in Session speichern
    $_SESSION['standingOrder']['standingOrderSet'] = 1;
    $_SESSION['standingOrder']['standingOrderID'] = intval($_GET['standingOrder']);

    // Prüfen ob Dauerauftrag vor Fälligkeitsdatum gewählt
    if ($_GET['beforeDueDate'] == 1) {
        $_SESSION['standingOrder']['standingOrderBeforeDueDate'] = 1;
        // SQL-Query bereitstellen
        $sqlquery = "SELECT `template`.`recipient`, `template`.`invoiceNo`, `template`.`entryText`, `template`.`grandTotal`, `template`.`debitAccount`, `template`.`creditAccount`, `template`.`period`, `template`.`classification1`, `template`.`classification2`, `template`.`classification3` FROM `template` RIGHT JOIN `standingOrder` ON `template`.`templateID` = `standingOrder`.`template` WHERE `standingOrder`.`standingOrderID` = " . intval($_SESSION['standingOrder']['standingOrderID']) . " AND `closed` = 'N'";
    } else {
        $_SESSION['standingOrder']['standingOrderBeforeDueDate'] = 0;
        // SQL-Query bereitstellen
        $sqlquery = "SELECT `template`.`recipient`, `template`.`invoiceNo`, `template`.`entryText`, `template`.`grandTotal`, `template`.`debitAccount`, `template`.`creditAccount`, `template`.`period`, `template`.`classification1`, `template`.`classification2`, `template`.`classification3` FROM `template` RIGHT JOIN `standingOrder` ON `template`.`templateID` = `standingOrder`.`template` WHERE `standingOrder`.`standingOrderID` = " . intval($_SESSION['standingOrder']['standingOrderID']) . " AND `standingOrder`.`nextExecutionDate` <= NOW() AND `closed` = 'N'";
    }

    $result = mysqli_query($userLink, $sqlquery);

    // Prüfen ob Datensätze vorhanden
    if (mysqli_num_rows($result) != 1) {
        unset($_SESSION['standingOrder']);
        $msg['invalidStandingOrder'] = 1;
    } else {
        // Abfrage in Array schreiben
        $dataDb = mysqli_fetch_assoc($result);

        // Leere Felder aus valueTemplate Array entfernen
        $dataDb = array_diff($dataDb, array(NULL, '', 0, '0.00'));

        // Vorlage-Werte GET-Variable zuweisen
        foreach ($dataDb as $key => $row) {
            $_GET[$key] = $row;
        }
    }
} else {
    unset($_SESSION['standingOrder']);
    // Fällige Daueraufträge prüfen
    include 'includes/standingOrderCheck.inc.php';
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
    <?php
    // Navigation einbinden
    include_once 'core/navigation.php';
    ?>

    <div class="container">
        <?php if ($_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>
            <div class="row">
                <div class="col-md-4 order-md-2">
                    <div class="card mt-3">
                        <h5 class="card-header" id="standingOrder">Daueraufträge</h5>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <?php
                                    // Prüfen ob Datensätze vorhanden
                                    if (intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']) < 1): ?>
                                    <p>Keine anstehende Buchung.</p>
                    
                                    <?php else: 
                                    // SQL-Query bereitstellen
                                    $sqlquery = "SELECT `standingOrderID`, `label` AS `standingOrderLabel`, `nextExecutionDate` FROM `standingOrder` WHERE `nextExecutionDate` <= NOW() AND `closed` = 'N'";
                                    $result = mysqli_query($userLink, $sqlquery);
                                    ?>
                                    <div class="accordion" id="accordionStandingOrder">
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <!-- Card for standingOrder: <?php echo intval($row['standingOrderID']); ?> -->
                                        <div class="card<?php echo (intval($_SESSION['standingOrder']['standingOrderID']) == $row['standingOrderID'] ? ' bg-light' : ''); ?>">
                                            <div class="card-body">
                                                <h6 class="card-title m-0">
                                                    <button class="btn btn-link btn-block text-left p-0" type="button" data-toggle="collapse" data-target="#collapse<?php echo intval($row['standingOrderID']); ?>" aria-expanded="true" aria-controls="collapse<?php echo intval($row['standingOrderID']); ?>">
                                                        <?php echo htmlspecialchars($row['standingOrderLabel'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </button>
                                                </h6>
                                                <small class="card-text">Fällig seit: <span id="dueDate<?php echo intval($row['standingOrderID']); ?>"><?php echo date_format(date_create($row['nextExecutionDate']), 'd.m.Y'); ?></span></small>
                                            </div>
        
                                            <div id="collapse<?php echo intval($row['standingOrderID']); ?>" class="collapse<?php echo (intval($_SESSION['standingOrder']['standingOrderID']) == $row['standingOrderID'] ? ' show' : ''); ?>" data-parent="#accordionStandingOrder">
                                                <div class="card-body pt-0">
                                                    <a class="btn btn-primary btn-block<?php echo (intval($_SESSION['standingOrder']['standingOrderID']) == $row['standingOrderID'] ? ' disabled' : ''); ?>" id="chStOrBtn<?php echo intval($row['standingOrderID']); ?>" href="entry.php?standingOrder=<?php echo intval($row['standingOrderID']); ?>#newEntry" role="button">Dauerauftrag auswählen</a>
                                                    <button type="button" class="btn btn-secondary btn-sm btn-block skip-standingOrder" value="<?php echo intval($row['standingOrderID']); ?>">Einmalig überspringen</button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 order-md-1">
                    <div class="card mt-3">
                        <h5 class="card-header" id="newEntry">Neue Buchung erfassen</h5>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <?php include_once 'includes/alertProvider.inc.php'; // Alert Provider ?>
                                    <form action="includes/newEntry.inc.php" method="POST" id=formNewEntry>
                                        <div class="form-row">
                                            <div class="form-group col-md-3"> <!-- Buchungsdatum -->
                                                <label for="date">Buchungsdatum</label>
                                                <input class="form-control chk-toggle-dis-invert-slave" type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                            <div class="form-group col-md-2"> <!-- Periode -->
                                                <label for="period">Periode</label>
                                                <?php
                                                // SQL-Query bereitstellen
                                                $sqlquery = "SELECT p.periodID, p.label, p.active, (CASE WHEN p.periodID = p._current THEN 'Y' ELSE 'N' END) AS current FROM (SELECT period.*, (SELECT period.periodID FROM period WHERE period.active = 'Y' ORDER BY period.periodID ASC LIMIT 1 ) AS _current FROM period) p WHERE p.active = 'Y' ORDER BY p.label ASC";
                                                $result = mysqli_query($userLink, $sqlquery);
                            
                                                // Prüfen ob Datensätze vorhanden
                                                if (mysqli_num_rows($result) < 1): ?>
                                                <select class="form-control" id="period" name="period">
                                                    <option disabled>Keine Datensätze vorhanden</option>
                                                <?php else: ?>
                                                <select class="form-control" id="period" name="period">
                                                    <option></option>
                                                    <?php while ($row = mysqli_fetch_assoc($result)):
        
                                                        if (isset($_GET['period'])) {
                                                            if ($_GET['period'] == $row['periodID']) {
                                                                $isSelected = true;
                                                            } else {
                                                                $isSelected = false;
                                                            }
                                                        } elseif ($row['current'] == 'Y') {
                                                            $isSelected = true;
                                                        } else {
                                                            $isSelected = false;
                                                        } ?>
        
                                                    <option value="<?php echo intval($row['periodID']); ?>"<?php echo ($isSelected ? ' selected' : ''); ?>><?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></option>
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
                                                    <option value="<?php echo intval($row['recipientID']); ?>"<?php echo ($_GET['recipient'] == $row['recipientID'] ? ' selected' : ''); ?>><?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></option>
                                                    <?php endwhile;
                                                endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-5"> <!-- Rechnungsummer -->
                                                <label for="invoiceNo">Rechnungsnummer</label>
                                                <input class="form-control" type="text" id="invoiceNo" name="invoiceNo" value="<?php echo htmlspecialchars($_GET['invoiceNo'], ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="form-group col-md-7"> <!-- Beschreibung -->
                                                <label for="entryText">Beschreibung</label>
                                                <input class="form-control" type="text" id="entryText" name="entryText" value="<?php echo htmlspecialchars($_GET['entryText'], ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>  
                                        </div>
                                        <div class="form-row">
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
                                                        <optgroup label="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?php foreach ($row1 as $key => $row2): ?>
                                                            <option value="<?php echo intval($row2['accountID']); ?>"<?php echo ($_GET['debitAccount'] == $row2['accountID'] ? ' selected' : ''); ?>><?php echo intval($row2['accountID']) . ' ' . htmlspecialchars($row2['accountLabel'], ENT_QUOTES, 'UTF-8'); ?></option>
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
                                                        <optgroup label="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?php foreach ($row1 as $key => $row2): ?>
                                                            <option value="<?php echo intval($row2['accountID']); ?>"<?php echo ($_GET['creditAccount'] == $row2['accountID'] ? ' selected' : ''); ?>><?php echo intval($row2['accountID']) . ' ' . htmlspecialchars($row2['accountLabel'], ENT_QUOTES, 'UTF-8'); ?></option>
                                                        <?php endforeach;
                                                    endforeach;
                                                endif; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2"> <!-- Betrag -->
                                                <label for="grandTotal">Betrag</label>
                                                <input class="form-control chk-toggle-req-slave" type="number" id="grandTotal" name="grandTotal" step="0.01" lang="en" value="<?php echo floatval($_GET['grandTotal']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4"> <!-- Klassifikation 1 -->
                                                <label for="classification1">Klassifikation 1</label>
                                                <?php
                                                // SQL-Query bereitstellen
                                                $sqlquery = "SELECT `classificationID`, `label` FROM `classification` WHERE `active` = 'Y' ORDER BY `label` ASC";
                                                $result = mysqli_query($userLink, $sqlquery);
                    
                                                // Prüfen ob Datensätze vorhanden
                                                if (mysqli_num_rows($result) < 1): ?>
                                                <select class="form-control" id="classification1" name="classification1">
                                                    <option disabled>Keine Datensätze vorhanden</option>
                                                <?php else: ?>
                                                <select class="form-control" id="classification1" name="classification1">
                                                    <option></option>
                                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                    <option value="<?php echo intval($row['classificationID']); ?>"<?php echo ($_GET['classification1'] == $row['classificationID'] ? ' selected' : ''); ?>><?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></option>
                                                    <?php endwhile;
                                                endif; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4"> <!-- Klassifikation 2 -->
                                                <label for="classification2">Klassifikation 2</label>
                                                <?php
                                                // SQL-Query bereitstellen
                                                $sqlquery = "SELECT `classificationID`, `label` FROM `classification` WHERE `active` = 'Y' ORDER BY `label` ASC";
                                                $result = mysqli_query($userLink, $sqlquery);
                    
                                                // Prüfen ob Datensätze vorhanden
                                                if (mysqli_num_rows($result) < 1): ?>
                                                <select class="form-control" id="classification2" name="classification2">
                                                    <option disabled>Keine Datensätze vorhanden</option>
                                                <?php else: ?>
                                                <select class="form-control" id="classification2" name="classification2">
                                                    <option></option>
                                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                    <option value="<?php echo intval($row['classificationID']); ?>"<?php echo ($_GET['classification2'] == $row['classificationID'] ? ' selected' : ''); ?>><?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></option>
                                                    <?php endwhile;
                                                endif; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4"> <!-- Klassifikation 3 -->
                                                <label for="classification3">Klassifikation 3</label>
                                                <?php
                                                // SQL-Query bereitstellen
                                                $sqlquery = "SELECT `classificationID`, `label` FROM `classification` WHERE `active` = 'Y' ORDER BY `label` ASC";
                                                $result = mysqli_query($userLink, $sqlquery);
                    
                                                // Prüfen ob Datensätze vorhanden
                                                if (mysqli_num_rows($result) < 1): ?>
                                                <select class="form-control" id="classification3" name="classification3">
                                                    <option disabled>Keine Datensätze vorhanden</option>
                                                <?php else: ?>
                                                <select class="form-control" id="classification3" name="classification3">
                                                    <option></option>
                                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                    <option value="<?php echo intval($row['classificationID']); ?>"<?php echo ($_GET['classification3'] == $row['classificationID'] ? ' selected' : ''); ?>><?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></option>
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
                                                $sqlquery = "SELECT `journal`.`entryID`, `journal`.`date`, `recipient`.`label` AS `recipient`, `journal`.`grandTotal` FROM `journal` LEFT JOIN `recipient` ON `journal`.`recipient` = `recipient`.`recipientID` RIGHT JOIN `account` AS `creditAccount` ON `journal`.`creditAccount` = `creditAccount`.`accountID` RIGHT JOIN `account` AS `debitAccount` ON `journal`.`debitAccount` = `debitAccount`.`accountID` WHERE `journal`.`reconcilation` = 'N' AND `creditAccount`.`reconcilationAllow` = 'Y' ORDER BY `journal`.`date` ASC";
                                                $result = mysqli_query($userLink, $sqlquery);
                    
                                                // Prüfen ob Datensätze vorhanden
                                                if (mysqli_num_rows($result) < 1): ?>
                                                <select class="form-control chk-toggle-dis-invert-slave" id="entryReference" name="entryReference" multiple>
                                                    <option disabled>Keine Datensätze vorhanden</option>
                                                <?php else: ?>
                                                <select class="form-control chk-toggle-dis-invert-slave" id="entryReference" name="entryReference[]" multiple>
                                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                    <option value="<?php echo intval($row['entryID']); ?>" data-grandTotal="<?php echo floatval($row['grandTotal']); ?>"><?php echo htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($row['recipient'], ENT_QUOTES, 'UTF-8') . ', CHF ' . floatval($row['grandTotal']); ?></option>
                                                    <?php endwhile;
                                                endif; ?>
                                                </select>
                                                <small id="entryReferenceHelp" class="form-text text-muted">
                                                    Zwischentotal der Auswahl: CHF <span id="entryReferenceSubtotal"></span> / Differenz zum erfassten Betrag: <span id="entryReferenceDifference"></span>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-3">
                                                <button type="submit" class="btn btn-primary btn-block" name="submit">Speichern</button>
                                            </div>
                                            <?php if ($_SESSION['standingOrder']['standingOrderSet'] == 1 && $_SESSION['standingOrder']['standingOrderBeforeDueDate'] == 1): ?>
                                                <div class="col-12">
                                                    <small>Der aktuell ausgewählte Dauerauftrag ist noch nicht fällig und wird hiermit vorgezogen.</small>
                                                </div>
                                            <?php elseif ($_SESSION['standingOrder']['standingOrderSet'] == 1): ?>
                                                <div class="col-12">
                                                    <small>Der aktuell ausgewählte <a href="#standingOrder">Dauerauftrag</a> wird gespeichert und abgeschlossen.</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <div class="card mt-3">
                        <h5 class="card-header" id="addTemplate">Als Vorlage speichern</h5>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <p>Beim Speichern einer Buchung wird eine Vorlage erstellt, welche alle aktuell ausgefüllten Felder beinhaltet. Hinweis: Beim Speichern als Vorlage wird keine Buchung erstellt!</p>
                                    <p>Es kann dabei zwischen zwei Arten ausgewählt werden:</p>
                                    <dl class="row">
                                        <dt class="col-sm-3">Applikation</dt>
                                        <dd class="col-sm-9">Beim Speichern in der Applikation wird die Vorlage gespeichert und kann über die Menuleiste der Applikation ausgewählt und geöffnet werden.</dd>
                                        <dt class="col-sm-3">Lesezeichen</dt>
                                        <dd class="col-sm-9">Beim Speichern als Lesezeichen wird eine URL erzeugt, welche als Lesezeichen verwenden werden kann. Die Vorlage wird nicht zusätzlich gespeichert.</dd>
                                    </dl>
                                    <div class="form-group form-check"> <!-- Als Vorlage -->
                                        <?php if ($_SESSION['standingOrder']['standingOrderSet'] == 1): ?>
                                        <input class="form-check-input" type="checkbox" id="chkAddTemplate" name="chkAddTemplate" value="1" disabled>
                                        <?php else: ?>
                                        <input class="form-check-input" type="checkbox" id="chkAddTemplate" name="chkAddTemplate" value="1">
                                        <?php endif; ?>
                                        <label class="form-check-label" for="chkAddTemplate">Als Vorlage hinzufügen</label>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-12"> <!-- Beschreibung -->
                                            <label for="nameTemplate">Beschreibung</label>
                                            <input class="form-control chk-toggle-dis-slave" type="text" id="nameTemplate" name="nameTemplate">
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input chk-toggle-dis-slave" type="radio" name="radioTemplate" id="radioTemplate1" value="1" checked>
                                        <label class="form-check-label" for="radioTemplate1">
                                            In der Applikation
                                        </label>
                                    </div>
                                    <div class="form-group form-check">
                                        <input class="form-check-input chk-toggle-dis-slave" type="radio" name="radioTemplate" id="radioTemplate2" value="2">
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
    <!-- BuchungHelper -->
    <script src="js/buchungHelper.js"></script>
</body>
</html>