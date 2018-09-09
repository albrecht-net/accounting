<?php
session_start();

// Konfiguration einbinden
require_once 'config.php';

// Array Eingabe
$dataSetup = array(
    'step' => $_GET['step'],
    'tables' => array(
        'users',
        'databases'
    )
);

// HTML Header
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" async></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" async></script>

    <title>Benutzer Konfiguration</title>
</head>
<body>
<?php

switch ($dataSetup['step']) {
    case (0): // Willkommensseite
        ?>
        <h2>Willkommen bei der Datenbank basierten Buchhaltung</h2>
        <p>Der Setup-Assistent ist in zwei Schritte unterteilt: <b>Benutzer</b>- und <b>Datenbank</b>-Konfiguration</p>
        <p>Um mit der Benutzereinrichtung zu beginnen, benötigen wir den Benutzernamen:</p>
        <form method="POST" action="setup.php?step=1">
            <div class="form-group">
                <?php if ($_GET['msg'] == 'unknownUser'): ?>
                    <input type="text" class="form-control is-invalid" name="inputUsername" required>
                    <div class="invalid-feedback">
                        Der eingegebene Benutzername ist unbekannt.
                    </div>
                <?php else: ?>
                    <input type="text" class="form-control" name="inputUsername" required>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Bestätigen</button>
        </form>
        <?php
        break;
    case (1): // Benutzername überprüfen
        if (!isset($_POST['submit'])) {
            header("Location: setup.php?step=0");
            exit();
        }

        $dataSetup['input'] = array(
            'username' => mysqli_real_escape_string($config['link'], $_POST['inputUsername'])
        );

        // Benutzername überprüfen
        $sqlquery = "SELECT `userID`, `username` FROM `users` WHERE `activation` = 'N' AND `username` = '" . $dataSetup['input']['username'] . "'";
        $result = mysqli_query($config['link'], $sqlquery);
        if (mysqli_num_rows($result) != 1) {
            header("Location: setup.php?step=0&msg=unknownUser");
            exit();
        }

        // Datensatz in Sessioncookie schreiben
        $_SESSION['setup'] = mysqli_fetch_assoc($result);

    case (2): // Kontoinformationen und Passwort festlegen
        if (!isset($_SESSION['setup'])) {
            header("Location: setup.php?step=0");
            exit();
        }
        ?>
        <form method="POST" action="setup.php?step=3">
            <div class="form-group">
                <label for="inputEmail">Email Addresse (optional)</label>
                <?php if ($_GET['msg'] == 'invalidEmail'): ?>
                    <input type="email" class="form-control is-invalid" name="inputEmail" id="inputEmail" placeholder="Email">
                    <div class="invalid-feedback">
                        Die eingegebene Emailaddresse hat ein ungültiges Format.
                    </div>
                <?php else: ?>
                    <input type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="Email" value="<?php echo $_GET['email']; ?>">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="inputPassword1">Passwort eingeben</label>
                <?php if ($_GET['msg'] == 'noPasswordMatch'): ?>
                    <input type="password" class="form-control is-invalid" name="inputPassword1" id="inputPassword1" placeholder="Passwort" required>
                <?php else: ?>
                    <input type="password" class="form-control" name="inputPassword1" id="inputPassword1" placeholder="Passwort" required>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="inputPassword2">Passwort wiederholen</label>
                <?php if ($_GET['msg'] == 'noPasswordMatch'): ?>
                    <input type="password" class="form-control is-invalid" name="inputPassword2" id="inputPassword2" placeholder="Passwort wiederholen" required>
                    <div class="invalid-feedback">
                        Passwörter stimmen nicht überein.
                    </div>
                <?php else: ?>
                    <input type="password" class="form-control" name="inputPassword2" id="inputPassword2" placeholder="Passwort wiederholen" required>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Bestätigen</button>
        </form>
        <?php
        break;
    case (3): // Kontoinformationen und Passwort in DB speichern
        if (!isset($_SESSION['setup'])) {
            header("Location: setup.php?step=0");
            exit();
        }
        
        $dataSetup['input'] = array(
            'email' => mysqli_real_escape_string($config['link'], strtolower($_POST['inputEmail'])),
            'password1' => $_POST['inputPassword1'],
            'password2' => $_POST['inputPassword2']
        );

        // Emailaddresse validieren
        if (!empty($dataSetup['input']['email']) && !filter_var($dataSetup['input']['email'], FILTER_VALIDATE_EMAIL)) {
            header("Location: setup.php?step=2&msg=invalidEmail");
            exit();
        }

        // Passwortübereinstimmung prüfen
        if ($dataSetup['input']['password1'] !== $dataSetup['input']['password2']) {
            header("Location: setup.php?step=2&msg=noPasswordMatch&email=" . $dataSetup['input']['email']);
            exit();
        }

        // Passwort Hash
        $dataSetup['input']['password'] = password_hash($dataSetup['input']['password1'], PASSWORD_DEFAULT);

        unset($dataSetup['input']['password1']);
        unset($dataSetup['input']['password2']);

        $dataSetup['input']['activation'] = 'Y';

        // SQL-Query bereitstellen
        $set = [];
        foreach ($dataSetup['input'] as $column => $value) {
            $set[] = "`" . $column . "` = '" . $value . "'";
        }
        $sqlquery = "UPDATE `users` SET " . implode(", ", $set) . " WHERE `users`.`userID` = '" . $_SESSION['setup']['userID'] . "'";

        // SQL-Query ausführen und überprüfen
        if (!mysqli_query($config['link'], $sqlquery)) {
            echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
            exit();
        }

        // Setup Session schliessen
        session_destroy();

    case (4): // Aufforderung zum Anmelden
        ?>
        <h2>Datenbankverbindung einrichten</h2>
        <p>Bitte melden Sie sich mit Ihrem Benutzerkonto an</p>
        <a href="<?php echo 'login.php?rd=' . urlencode('setup.php?step=5'); ?>" class="btn btn-primary btn-lg btn-block" role="button">Anmelden</a>
        <?php
        break;
    case (5): // Formular für Datenbankangaben
        // Prüfen ob Benutzer angemeldet
        require 'includes/loginSessionCheck.inc.php';
        if ($lsc == FALSE) {
            header('Location: login.php?rd=' . urlencode('setup.php?step=5'));
        }

        if ($_GET['msg'] == 'mysqlError'): ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Verbindung fehlgeschlagen!</h4>
                <p>Es wurde vergeblich versucht eine Temporäre Verbindung zur angegebenen Datenbank aufzubauen. Bitte überprüfen Sie die Angaben.</p>
                <hr>
                <p class="mb-0">Folgender Fehler wurde von MySQL ausgegeben: <i><?php echo $_GET['mysqliError']; ?></i></p>
            </div>
        <?php endif; ?>


        <form method="POST" action="setup.php?step=6">
            <div class="form-group">
                <label for="dbHost">Server IP oder Hostname</label>
                <input type="text" class="form-control" name="dbHost" id="dbHost" placeholder="IP / Hostname" value="<?php echo $_GET['dbHost']; ?>" required>
            </div>
            <div class="form-group">
                <label for="dbPort">Port</label>
                <input type="number" class="form-control" name="dbPort" id="dbPort" placeholder="Port" value="<?php echo $_GET['dbPort']; ?>" required>
            </div>
            <div class="form-group">
                <label for="dbUsername">Benutzername</label>
                <input type="text" class="form-control" name="dbUsername" id="dbUsername" placeholder="Benutzername" value="<?php echo $_GET['dbUsername']; ?>" required>
            </div>
            <div class="form-group">
                <label for="dbPassword">Passwort</label>
                <input type="password" class="form-control" name="dbPassword" id="dbPassword" placeholder="Passwort" required>
                <small id="dbPassword" class="form-text text-muted">Hinweis: Das Passwort wird in Klartext in der Datenbank gespeichert! Verwenden Sie einen Datenbank-Benutzer mit eingeschränkten Rechten.</small>
            </div>
            <div class="form-group">
                <label for="dbName">Datenbankname</label>
                <input type="text" class="form-control" name="dbName" id="dbName" placeholder="Datenbankname" value="<?php echo $_GET['dbName']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Bestätigen</button>
        </form>
        <?php
        break;
    case (6): // Datenbankangaben in DB speichern
        // Prüfen ob Benutzer angemeldet
        require 'includes/loginSessionCheck.inc.php';
        if ($lsc == FALSE) {
            header('Location: login.php?rd=' . urlencode('setup.php?step=5'));
        }

        $dataSetup['input'] = array(
            'dbHost' => mysqli_real_escape_string($config['link'], strtolower($_POST['dbHost'])),
            'dbPort' => $_POST['dbPort'],
            'dbUsername' => mysqli_real_escape_string($config['link'], strtolower($_POST['dbUsername'])),
            'dbPassword' => $_POST['dbPassword'],
            'dbName' => mysqli_real_escape_string($config['link'], $_POST['dbName'])
        );

        // Port Datentyp auf int festlegen
        settype($dataSetup['input']['dbPort'], 'int');

        // Mit der Datenbank verbinden
        $tempLink = mysqli_connect($dataSetup['input']['dbHost'] . ':' . $dataSetup['input']['dbPort'], $dataSetup['input']['dbUsername'], $dataSetup['input']['dbPassword'], $dataSetup['input']['dbName']);

        // Verbindung überprüfen
        if (!$tempLink) {
            header("Location: setup.php?step=5&msg=mysqlError&mysqliError=" . mysqli_connect_error() . "&dbHost=" . $dataSetup['input']['dbHost'] . "&dbPort=" . $dataSetup['input']['dbPort'] . "&dbUsername=" . $dataSetup['input']['dbUsername'] . "&dbName=" . $dataSetup['input']['dbName']);
            exit();
        } else {
            // Datenbankangaben speichern
            $columns = "`userID`, `" . implode("`, `", array_keys($dataSetup['input'])) . "`";
            $values = "'" . $_SESSION['userID'] . "', '" . implode("', '", $dataSetup['input']) . "'";
            $sqlquery = "INSERT INTO `databases` (" . $columns . ") VALUES (" . $values . ")";

            // SQL-Query ausführen und überprüfen
            if (!mysqli_query($config['link'], $sqlquery)) {
                echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
                exit();
            }
            // Bestätigung
            ?>

            <h2>Einrichtung abgeschlossen</h2>
            <p>Es wurde erfolgreich eine Temporäre Verbindung zur angegebenen Datenbank aufgebaut. Es werden noch bereits vorhandene Tabellen aufgelistet:</p>
            
            <?php
            // Auf vorhandene Tabellen abfragen
            $sqlquery = "SHOW TABLES";
            $result = mysqli_query($tempLink, $sqlquery);
            ?>

            <ul class="list-group"></ul>
            <?php if (mysqli_num_rows($result) >= 1): ?>
                <?php while ($row = mysqli_fetch_row($result)): ?>
                    <li class="list-group-item"><?php echo $row[0]; ?></li>
                <?php endwhile; ?>
            <?php else: ?>
                <li class="list-group-item"><i>Keine Tabellen erkannt</i></li>
            <?php endif; ?>
            </ul>
            <?php
        }
        break;

    default: // Definiert Aktion bei einem undefiniertem Schritt
        header("Location: setup.php?step=0");
}
// HTML Footer
?>
</body>
</html>