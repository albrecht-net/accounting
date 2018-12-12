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
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <title>Dauerauftrag</title>
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
                    <a class="nav-link" href="buchung.php">Neue Buchung<span class="sr-only">(current)</span></a>
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
        <h3 class="mt-3" id="addStandingOrder">Dauerauftrag erfassen</h3>
        <hr class="mb-4">
        <?php if ($_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>        
        <div class="row">
            <div class="col-12 mb-5">
                <form action="standingOrder.php" method="POST">
                    <div class="row">
                        <div class="col-md-7">
                            <p>Daueraufträge basieren auf einer bereits erstellten <a href="templates.php#savedTemplates">Vorlage</a>.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-5"> <!-- Buchungsvorlage auswählen -->
                            <label for="template">Buchungsvorlage auswählen</label>
                            <?php
                            // SQL-Query bereitstellen
                            $sqlquery = "SELECT `templateID`, `name` FROM `template` ORDER BY `name` ASC";
                            $result = mysqli_query($userLink, $sqlquery);

                            // Prüfen ob Datensätze vorhanden
                            if (mysqli_num_rows($result) < 1): ?>
                            <select class="form-control" id="template" name="template">
                                <option disabled>Keine Datensätze vorhanden</option>
                            <?php else: ?>
                            <select class="form-control" id="template" name="template">
                                <option></option>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <option value="<?php echo $row['templateID']; ?>"<?php echo ($_GET['template'] == $row['templateID'] ? ' selected' : ''); ?>><?php echo $row['name']; ?></option>
                                <?php endwhile;
                            endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-7"> <!-- Beschreibung -->
                            <label for="nameTemplate">Beschreibung</label>
                            <input class="form-control chk-toggle-dis-slave" type="text" id="nameTemplate" name="nameTemplate" required>
                        </div>
                        <div class="form-group col-md-3"> <!-- Startdatum -->
                            <label for="startdatum">Startdatum</label>
                            <input class="form-control chk-toggle-dis-invert-slave" type="date" id="startdatum" name="startdatum" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-check"> <!-- Nutze Startdatum -->
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="1" checked>
                            <label class="form-check-label" for="exampleRadios1">
                                Nutze Startdatum
                            </label>
                        </div>
                        <div class="form-check"> <!-- Nutze Monatsende -->
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="2">
                            <label class="form-check-label" for="exampleRadios2">
                                Nutze Monatsende
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-2"> <!-- PeriodizitätValue -->
                            <label for="periodizitätValue">PeriodizitätValue</label>
                            <input class="form-control chk-toggle-req-slave" type="number" id="periodizitätValue" name="periodizitätValue" step="1" lang="en" min="1" required>
                        </div>
                        <div class="form-group col-md-8"> <!-- Periodizität -->
                            <label for="periodizitätType">Periodizität</label>
                            <select class="form-control" id="periodizitätType" name="periodizitätType">
                                <option></option>
                                <option value="1">Tag(e)</option>
                                <option value="2">Woche(n)</option>
                                <option value="4">Monat(e)</option>
                                <option value="8">Jahr(e)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-check"> <!-- Gültig bis Widerruf -->
                            <input class="form-check-input" type="radio" name="exampleRadios2" id="exampleRadios11" value="1" checked>
                            <label class="form-check-label" for="exampleRadios11">
                                Auf Widerruf
                            </label>
                        </div>
                        <div class="form-check"> <!-- Gültig bis Enddatum -->
                            <input class="form-check-input" type="radio" name="exampleRadios2" id="exampleRadios12" value="2">
                            <label class="form-check-label" for="exampleRadios12">
                                Bis Enddatum
                            </label>
                        </div>
                        <div class="form-check"> <!-- Gültig n mal -->
                            <input class="form-check-input" type="radio" name="exampleRadios2" id="exampleRadios13" value="4">
                            <label class="form-check-label" for="exampleRadios13">
                                Gültig n mal
                            </label>
                        </div>
                        <div class="form-group col-md-3"> <!-- Enddatum -->
                            <label for="enddatum">Enddatum</label>
                            <input class="form-control chk-toggle-dis-invert-slave" type="date" id="enddatum" name="enddatum" min="<?php echo date_format(date_modify(date_create('now'), '+1 day'), 'Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group col-md-2"> <!-- GültigValue -->
                            <label for="GültigValue">PeriodizitätValue</label>
                            <input class="form-control chk-toggle-req-slave" type="number" id="GültigValue" name="GültigValue" step="1" lang="en" min="1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3">
                            <button type="submit" class="btn btn-primary btn-block" name="submit">Speichern</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h3 class="mt-3" id="addTemplate">Als Vorlage speichern</h3>
        <hr class="mb-4">
        <div class="row">
            <div class="col-12 mb-5">
                
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
</body>
</html>