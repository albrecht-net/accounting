<?php
// Konfiguration einbinden
require_once '../config.php';

// Prüfen ob Benutzer angemeldet
require '../includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: ../login.php?rd=' . urlencode('settings/sitemap.php'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <title>Account</title>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Buchhaltung</a>
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <?php if (intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']) > 0): ?>
                    <a class="nav-link" href="../buchung.php">Neue Buchung <span class="badge badge-warning"><?php echo intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']); ?></span><span class="sr-only">pending booking</span></a>
                    <?php else: ?>
                    <a class="nav-link" href="../buchung.php">Neue Buchung</a>
                    <?php endif; ?>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Weitere erfassen
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="../standingOrder.php">Dauerauftrag</a>
                        <a class="dropdown-item disabled" href="#">Konto</a>
                        <a class="dropdown-item" href="../recipient.php">Empfänger</a>
                        <a class="dropdown-item" href="../classification.php">Klassifikation</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../templates.php">Vorlagen</a>
                </li>
            </ul>
            <ul class="navbar-nav navbar-right">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item disabled" href="#">Mein Profil</a>
                        <a class="dropdown-item" href="account.php">Einstellungen</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="../logout.php">Abmelden</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <!-- Settings Navigation -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="account.php">Account</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="database.php">Datenbank</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="sitemap.php">Sitemap</a>
            </li>
        </ul>

        <h3 class="mt-3" id="sitemap">Sitemap</h3>
        <hr class="mb-4">
        <div class="row">
            <div class="col-12 mb-5">
            <pre lang="text">
Accounting/
├── <a href="../index.php">Index</a>
├── <a href="../buchung.php">Neue Buchung/</a>
|   ├── <a href="../buchung.php#newEntry">Neue Buchung erfassen</a>
│   └── <a href="../buchung.php#addTemplate">Als Vorlage speichern</a>
├── Weitere erfassen/
|   ├── <a href="../standingOrder.php">Dauerauftrag/</a>
│   │   ├── <a href="../standingOrder.php#addStandingOrder">Dauerauftrag erfassen</a>
│   │   └── <a href="../standingOrder.php#savedStandingOrder">Erfasste Daueraufträge</a>
│   ├── <del>Konto</del>
|   ├── <a href="../recipient.php">Empfänger/</a>
│   │   ├── <a href="../recipient.php#addRecipient">Empfänger erfassen</a>
│   │   └── <a href="../recipient.php#savedRecipient">Erfasste Empfänger</a>
|   ├── <a href="../classification.php">Klassifikation/</a>
│   │   ├── <a href="../classification.php#addClassification">Klassifikation erfassen</a>
│   │   └── <a href="../classification.php#savedClassification">Erfasste Klassifikationen</a>
├── <a href="../templates.php">Vorlagen/</a>
│   └── <a href="../templates.php#savedTemplates">Gespeicherte Vorlagen</a>
└── Benutzername/
    ├── <del>Mein Profil</del>
    ├── <a href="index.php">Einstellungen/</a>
    │   ├── <a href="account.php">Account/</a>
    │   |   └── <a href="account.php#changePassword">Password ändern</a>
    |   ├── <a href="database.php">Datenbank/</a>
    │   |   ├── <a href="database.php#addDatabase">Datenbank hinzufügen</a>
    |   |   ├── <a href="database.php#linkedDatabase">Gespeicherte Datenbanken</a>
    │   |   └── <a href="database.php#defaultDatabase">Standard Datenbank</a>
    │   └── <a href="sitemap.php">Sitemap</a>
    └── <a href="../logout.php">Abmelden</a>
            </pre>
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