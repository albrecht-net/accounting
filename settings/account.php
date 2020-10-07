<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
}

require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

// Konfiguration einbinden
require_once '../config.php';

// Prüfen ob Benutzer angemeldet
require '../includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: ../login.php?rd=' . urlencode('settings/account.php'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $config['lang']; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/mainSite.css">

    <title>Account</title>
</head>
<body>
    <?php
    // Navigation einbinden
    include_once '../core/navigation.php';
    ?>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Settings Navigation -->
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="account.php">Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="database.php">Datenbank</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sitemap.php">Sitemap</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mt-3">
                    <h5 class="card-header" id="changePassword">Passwort ändern</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <?php include_once '../includes/alertProvider.inc.php'; // Alert Provider ?>
                                <form method="POST" action="../includes/changePassword.inc.php">
                                    <div class="form-group">
                                        <label for="inputOldPassword">Bisheriges Passwort</label>
                                        <input type="password" class="form-control" name="inputOldPassword" id="inputOldPassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword1">Neues Passwort</label>
                                        <input type="password" class="form-control" name="inputPassword1" id="inputPassword1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword2">Neues Passwort bestätigen</label>
                                        <input type="password" class="form-control" name="inputPassword2" id="inputPassword2" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-3">
                                            <button type="submit" class="btn btn-primary btn-block" name="submitChangePassword">Passwort ändern</button>
                                        </div>
                                    <div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- /container -->
    </div>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>

                