<?php
session_start();

// Konfiguration einbinden
require_once 'config.php';

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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/login.css">

    <title>Login</title>
</head>
<body class="text-center">
    <div class="form-group-login">
        <h3>Bitte Anmelden</h3>
        <?php if ($msg == 'invalid'): ?>
        <div class="alert alert-danger" role="alert">
            Falsches Kennwort oder Benutzername
        </div>
        <?php elseif ($_GET['loggedout']): ?>
        <div class="alert alert-primary" role="alert">
            Benutzer erfolgreich abgemeldet
        </div>
        <?php elseif ($_GET['passwordchanged']): ?>
        <div class="alert alert-primary" role="alert">
            Das Passwort wurde erfolgreich geändert. Bitte erneut Anmelden.
        </div>
        <?php endif ?>
        <!-- Login Formular -->
        <?php if (empty($_GET['rd'])): ?>
        <form action="login.php" method="POST">
        <?php else: ?>
        <form action="login.php?rd=<?php echo urlencode($_GET['rd']); ?>" method="POST">
        <?php endif; ?>
            <div class="form-group">
                <input type="text" class="form-control" name="inputUsername" id="inputUsername" value="<?php echo $dataInput['username']; ?>" placeholder="Benutzername" required>
                <input type="password" class="form-control" name="inputPassword" id="inputPassword" placeholder="Passwort" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block" name="submit">Anmelden</button>
        </form>
        <p class="mt-5 mb-3 text-muted">© 2018 albrecht-net</p>
    </div>
    
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>