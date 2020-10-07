<?php
session_start();

// Konfiguration einbinden
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="<?php echo config::get('defaultLang'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <title>Benutzer Konfiguration</title>
</head>
<body>
    <div class="container">
        <?php
        switch (intval($_GET['step'])) {
            case (0): // Willkommensseite
                ?>
                <h3 class="mt-3">Willkommen bei der Datenbank basierten Buchhaltung</h2>
                <hr class="mb-4">
                <div class="row">
                    <div class="col-12 mb-5">
                        <p>Der Setup-Assistent ist in zwei Schritte unterteilt: <strong>Benutzer</strong>- und <strong>Datenbank</strong>-Konfiguration</p>
                        <p>Um mit der Benutzereinrichtung zu beginnen, benötigen wir den Benutzernamen:</p>
                        <form method="POST" action="setup.php?step=1">
                            <div class="row">
                                <div class="form-group col-12">
                                    <?php if ($_GET['msg'] == 'unknownUser'): ?>
                                        <input type="text" class="form-control is-invalid" name="inputUsername" required>
                                        <div class="invalid-feedback">
                                            Der eingegebene Benutzername ist unbekannt.
                                        </div>
                                    <?php else: ?>
                                        <input type="text" class="form-control" name="inputUsername" required>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-3">
                                    <button type="submit" class="btn btn-primary btn-block" name="submit">Bestätigen</button>
                                </div>
                            <div>
                        </form>
                    </div>
                </div>
                <?php
                break;
            case (1): // Benutzername überprüfen
                if (!isset($_POST['submit'])) {
                    header("Location: setup.php?step=0");
                    exit();
                }

                $dataSetup['input'] = array(
                    'username' => mysqli_real_escape_string($config['link'], trim($_POST['inputUsername']))
                );

                // Benutzername überprüfen
                $sqlquery = "SELECT `userID` FROM `users` WHERE `activation` = 'N' AND `status` = 'Y' AND `username` = '" . $dataSetup['input']['username'] . "'";
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
                <h3 class="mt-3">Passwort und Kontoinformationen</h2>
                <hr class="mb-4">
                <div class="row">
                    <div class="col-12 mb-5">
                        <form method="POST" action="setup.php?step=3">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="inputEmail">Email Addresse (optional)</label>
                                    <?php if ($_GET['msg'] == 'invalidEmail'): ?>
                                        <input type="email" class="form-control is-invalid" name="inputEmail" id="inputEmail" disabled>
                                        <div class="invalid-feedback">
                                            Die eingegebene Emailaddresse hat ein ungültiges Format.
                                        </div>
                                    <?php else: ?>
                                        <input type="email" class="form-control" name="inputEmail" id="inputEmail" value="<?php echo $_GET['email']; ?>" disabled>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="inputPassword1">Passwort eingeben</label>
                                    <?php if ($_GET['msg'] == 'noPasswordMatch'): ?>
                                        <input type="password" class="form-control is-invalid" name="inputPassword1" id="inputPassword1" required>
                                    <?php else: ?>
                                        <input type="password" class="form-control" name="inputPassword1" id="inputPassword1" required>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="inputPassword2">Passwort wiederholen</label>
                                    <?php if ($_GET['msg'] == 'noPasswordMatch'): ?>
                                        <input type="password" class="form-control is-invalid" name="inputPassword2" id="inputPassword2" required>
                                        <div class="invalid-feedback">
                                            Passwörter stimmen nicht überein.
                                        </div>
                                    <?php else: ?>
                                        <input type="password" class="form-control" name="inputPassword2" id="inputPassword2" required>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-3">
                                    <button type="submit" class="btn btn-primary btn-block" name="submit">Bestätigen</button>
                                </div>
                            <div>
                        </form>
                <?php
                break;
            case (3): // Kontoinformationen und Passwort in DB speichern
                if (!isset($_SESSION['setup'])) {
                    header("Location: setup.php?step=0");
                    exit();
                }
                
                $dataSetup['input'] = array(
                    'email' => mysqli_real_escape_string($config['link'], trim(strtolower($_POST['inputEmail']))),
                    'password1' => $_POST['inputPassword1'],
                    'password2' => $_POST['inputPassword2']
                );

                $dataSetup['setup'] = array(
                    'userID' => intval($_SESSION['setup']['userID'])
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
                $sqlquery = "UPDATE `users` SET " . implode(", ", $set) . " WHERE `users`.`userID` = " . $dataSetup['setup']['userID'];

                // SQL-Query ausführen und überprüfen
                if (!mysqli_query($config['link'], $sqlquery)) {
                    echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
                    exit();
                }

                // Benutzer in der userconfig registrieren
                $sqlquery = "INSERT IGNORE INTO `userconfig` (`userID`) VALUES (" . $dataSetup['setup']['userID'] . ")";

                // SQL-Query ausführen und überprüfen
                if (!mysqli_query($config['link'], $sqlquery)) {
                    echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
                    exit();
                }

                // Setup Session schliessen
                session_destroy();

                // Aufforderung zum Anmelden
                ?>

                <h3 class="mt-3">Datenbankverbindung einrichten</h3>
                <hr class="mb-4">
                <div class="row">
                    <div class="col-12 mb-5">
                        <p>Bitte melden Sie sich mit Ihrem Benutzerkonto an. Um die Einrichtung abzuschliessen, können Sie anschliessend noch Ihre Datenbank hinterlegen.</p>
                        <div class="row">
                            <div class="col-6 col-md-3">
                                <a href="<?php echo 'login.php?rd=' . urlencode('settings/database.php'); ?>" class="btn btn-primary btn-lg btn-block" role="button">Anmelden</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                break;

            default: // Aktion bei einem undefiniertem Schritt
                header("Location: setup.php?step=0");
        }
        // HTML Footer
        ?>

    <!-- /container -->
    </div>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>