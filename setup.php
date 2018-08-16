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

// Prüfen ob Benutzer ohne UserID
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
                if ($_GET['msg'] = 'unknownUser') {
                ?>
                
                <?php
                } else {
                ?>
            <input type="text" class="form-control" id="inputUsername">
                <?php
                }
                ?>
        </div>
        <button type="submit" class="btn btn-primary">Bestätigen</button>
    </form>
    <?php
        break;
    case (1): // Benutzername überprüfen, Kontoinformationen und Passwort festlegen
        if (!isset($_POST['submit'])) {
            header("Location: setup.php?step=0&msg=unknownUser");
            exit();
        }

        $dataSetup['input'] = array(
            'username' => mysqli_real_escape_string($config['link'], $_POST['inputUsername'])
        );

}

?>
</body>
</html>