<?php
// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php?rd=' . urlencode('recipient.php'));
    exit();
}

// Mit Ziel Datenbank verbinden
require_once 'includes/userDbConnect.inc.php';

// Fällige Daueraufträge prüfen
include 'includes/standingOrderCheck.inc.php';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/mainSite.css">

    <title>Empfänger</title>
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
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <?php if (intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']) > 0): ?>
                    <a class="nav-link" href="buchung.php">Neue Buchung <span class="badge badge-warning"><?php echo intval(json_decode($_COOKIE['standingOrder'], TRUE)['count']); ?></span><span class="sr-only">pending booking</span></a>
                    <?php else: ?>
                    <a class="nav-link" href="buchung.php">Neue Buchung</a>
                    <?php endif; ?>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Weitere erfassen
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="standingOrder.php">Dauerauftrag</a>
                        <a class="dropdown-item disabled" href="#">Konto</a>
                        <a class="dropdown-item" href="recipient.php">Empfänger</a>
                        <a class="dropdown-item" href="classification.php">Klassifikation</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="templates.php">Vorlagen</a>
                </li>
            </ul>
            <ul class="navbar-nav navbar-right">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_SESSION['username']; ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item disabled" href="#">Mein Profil</a>
                        <a class="dropdown-item" href="settings/account.php">Einstellungen</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Abmelden</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php if ($_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>
        <h3 class="mt-3" id="addRecipient">Empfänger erfassen</h3>
        <hr class="mb-4">
        <div class="row">
            <div class="col-12 mb-5">
                <?php include_once 'includes/alertProvider.inc.php'; // Alert Provider ?>
                <form action="includes/addRecipient.inc.php" method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-7"> <!-- Empfänger -->
                            <label for="label">Empfänger Bezeichnung</label>
                            <input class="form-control" type="text" id="label" name="label" required>
                        </div>
                        <div class="form-group col-md-5"> <!-- Kundennummer -->
                            <label for="customerNumber">Kundennummer</label>
                            <input class="form-control" type="text" id="customerNumber" name="customerNumber">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3">
                            <button type="submit" class="btn btn-primary btn-block" name="submit">Speichern</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h3 class="mt-3" id="savedRecipient">Erfasste Empfänger</h3>
        <hr class="mb-4">
        <div class="row">
            <div class="col-12 mb-5">
                <?php
                // SQL-Query bereitstellen
                $sqlquery = "SELECT * FROM recipient";
                $result = mysqli_query($userLink, $sqlquery);

                // Prüfen ob Datensätze vorhanden
                if (mysqli_num_rows($result) >= 1):
                ?>
                <div class="table-responsive">                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Bezeichnung</th>
                                <th scope="col">Kundennummer</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)):
                                switch ($row['active']) {
                                    case 'Y':
                                        $row['active'] = 'Aktiv';
                                        break;
                                    case 'N':
                                        $row['active'] = 'Inaktiv';
                                        break;
                                }
                            ?>
                            <tr>
                                <td><?php echo intval($row['recipientID']); ?></td>
                                <td><?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo ($row['customerNumber'] == NULL ? '-' : htmlspecialchars($row['customerNumber'], ENT_QUOTES, 'UTF-8')); ?></td>
                                <td><?php echo $row['active']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="lead">Keine Einträge gefunden</p>
                <p>Sie haben für die ausgewählte Ziel-Datenbank noch keine Empfänger erfasst. Erfassen Sie Ihren erste Empfänger gleich <a href="recipient.php#addRecipient">hier</a>.</p>
                <?php endif; ?>
                </div>
            </div>
        </div>

        <?php else: ?>
        <p class="lead">Für die aktuelle Sitzung wurde keine Datenbank ausgewählt. Sie können eine <a href="settings/database.php">neue Datenbank hinzufügen</a> oder sich <a href="logout.php">abmelden</a></p>
        <?php endif ?>

    <!-- /container -->
    </div>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <!-- Radiobutton Toggle -->
</body>
</html>