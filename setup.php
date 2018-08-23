<?php
session_start();

// Konfiguration einbinden
require_once('config.php');

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
        <p>Willkommen bei der Datenbank basierten Buchhaltung. Um mit dem Einrichten zu beginnen, benötigen wir den Benutzernamen:</p>
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
        $dataSetup['input']['password'] = password_hash($dataSetup['input']['password'], PASSWORD_DEFAULT);

        unset($dataSetup['input']['password1']);
        unset($dataSetup['input']['password2']);

        $dataSetup['input']['activation'] = 1;

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

        break;
}
// HTML Footer
?>
</body>
</html>