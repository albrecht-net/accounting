<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__FILE__, 1) . DIRECTORY_SEPARATOR);
}

require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

// Versionsinformation einbinden
include_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'version.php';

// Array GET-Variablen
$dataInputGet = $_GET;

// Alert
if ($dataInputGet['loggedout']) {
    $response = array(
        'alert' => array(
            'alertType' => 'primary',
            'alertDismissible' => false
        ),
        'message' => array(
            'message' => 'Benutzer erfolgreich abgemeldet'
        ),
        'values' => array()
    );
} elseif ($dataInputGet['passwordchanged']) {
    $response = array(
        'alert' => array(
            'alertType' => 'primary',
            'alertDismissible' => false
        ),
        'message' => array(
            'message' => 'Das Passwort wurde erfolgreich geändert. Bitte erneut Anmelden.'
        ),
        'values' => array()
    );
}
?>

<!DOCTYPE html>
<html lang="<?php echo config::get('defaultLang'); ?>">
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

        <?php include_once 'includes/alertProvider.inc.php'; // Alert Provider ?>

        <!-- Login Formular -->
        <?php if (empty($dataInputGet)): ?>
        <form action="includes/login.inc.php" method="POST">
        <?php else: ?>
        <form action="includes/login.inc.php?<?php echo http_build_query($dataInputGet); ?>" method="POST">
        <?php endif; ?>
            <div class="form-group">
                <input type="text" class="form-control" name="inputUsername" id="inputUsername" placeholder="Benutzername" required>
                <input type="password" class="form-control" name="inputPassword" id="inputPassword" placeholder="Passwort" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block" name="submit">Anmelden</button>
        </form>
        <div class="mt-5 mb-3">
            <small class="text-muted"><?php echo $accountingVersion; ?></small>
            <p class="text-muted">© 2019 albrecht-net</p>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>