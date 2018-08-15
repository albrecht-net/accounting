<?php
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

// Prüfen ob Benutzer ohne UserID
$sqlquery = "SELECT `userID` FROM `users` WHERE `userID` = ''";
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
            <input type="text" class="form-control" id="inputUsername">
        </div>
        <button type="submit" class="btn btn-primary">Bestätigen</button>
    </form>
    <?php
}

?>
</body>
</html>