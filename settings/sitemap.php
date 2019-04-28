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
    <?php
    // Navigation einbinden
    include_once '../core/navigation.php';
    ?>

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
│   └── <a href="../index.php#lastEntries">Zuletzt erfasste Buchungen</a>
├── <a href="../entry.php">Neue Buchung/</a>
|   ├── <a href="../entry.php#newEntry">Neue Buchung erfassen</a>
│   └── <a href="../entry.php#addTemplate">Als Vorlage speichern</a>
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
    │   |   ├── <a href="database.php#newDatabaseNotes">Hinweise für eine neue Datenbank</a>
    │   |   ├── <a href="database.php#addDatabase">Datenbank hinzufügen</a>
    |   |   └── <a href="database.php#linkedDatabase">Gespeicherte Datenbanken</a>
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