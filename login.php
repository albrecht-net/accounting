<?php
session_start();

// Konfiguration einbinden
require_once('config.php');

// Überprüfen ob Submit geklickt wurde
if (isset($_POST['submit']) && !empty($_POST['inputUsername'])) {
    if (!include 'includes/login.inc.php') {
        echo date('H:i:s') . ' Datei einbinden fehlgeschlagen';
        exit();
    }
}
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

    <title>Login</title>
</head>
<body>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="inputUsername">Benutzername</label>
            <input type="text" class="form-control" name="inputUsername" id="inputUsername">
        </div>
        <div class="form-group">
            <label for="inputPassword">Passwort</label>
            <input type="password" class="form-control" name="password" id="inputPassword">
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Anmelden</button>
    </form>
</body>
</html>