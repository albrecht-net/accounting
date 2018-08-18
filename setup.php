<?php
// Konfiguration einbinden
require_once('config.php');

session_start();

// Array Eingabe
$dataSetup = array(
    'step' => $_GET['step'],
    'tables' => array(
        'users',
        'databases'
    )
);

// Prüfen ob Benutzer ohne aktivierung in Datenbank vorhanden
$sqlquery = "SELECT * FROM `users` WHERE `activation` = 0 AND `status` = 1";
if (mysqli_num_rows(mysqli_query($config['link'], $sqlquery)) < 1) {
    exit();
}

// HTML Header
?>
<!DOCTYPE html>
<html>
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
        <p>Willkommen bei der Datenbank basierten Buchhaltung. Um mit dem Einrichten zu beginnen, benötigen wir den Benutzernamen:</p>
        <form method="POST" action="setup.php?step=1">
            <div class="form-group">
                <?php
                if ($_GET['msg'] == 'unknownUser') {
                ?>
                <input type="text" class="form-control is-invalid" name="inputUsername">
                <div class="invalid-feedback">
                    Der eingegebene Benutzername ist unbekannt.
                </div>
                <?php
                } else {
                ?>
                <input type="text" class="form-control" name="inputUsername">
                <?php
                }
                ?>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="submit" value="Bestätigen">
            </div>
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
        $sqlquery = "SELECT `userID`, `username` FROM `users` WHERE `activation` = 0 AND `username` = '" . $dataSetup['input']['username'] . "'";
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
                <input type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="inputPassword1">Passwort</label>
                <input type="password" class="form-control" name="inputPassword1" id="inputPassword1" placeholder="Passwort">
            </div>
            <div class="form-group">
                <label for="inputPassword2">Passwort wiederholen</label>
                <input type="password" class="form-control" name="inputPassword2" id="inputPassword2" placeholder="Passwort wiederholen">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="submit" value="Bestätigen">
            </div>
        </form>
        <?php
        break;
    case (3): // Kontoinformationen und Passwort in DB speichern
        $dataSetup['input'] = array(
            'email' => mysqli_real_escape_string($config['link'], $_POST['inputEmail']),
            'password1' => $_POST['inputPassword1'],
            'password2' => $_POST['inputPassword2']
        );

        // Emailaddresse validieren
        if (!filter_var($dataSetup['input']['email'], FILTER_VALIDATE_EMAIL)) {
            header("Location: setup.php?step=2&msg=invalidEmail");
            exit();
        }

        // Passwortübereinstimmung prüfen
        if ($dataSetup['input']['password1'] !== $dataSetup['input']['password2']) {
            header("Location: setup.php?step=2&msg=noPasswordwMatch");
            exit();
        }
}

// HTML Footer
?>
</body>
</html>