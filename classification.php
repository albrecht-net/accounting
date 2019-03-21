<?php
// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php?rd=' . urlencode('classification.php'));
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

    <title>Klassifikation</title>
</head>
<body>
    <?php
    // Navigation einbinden
    include_once 'core/navigation.php';
    ?>

    <div class="container">
        <?php if ($_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>
        <h3 class="mt-3" id="addClassification">Klassifikation erfassen</h3>
        <hr class="mb-4">
        <div class="row">
            <div class="col-12 mb-5">
                <?php include_once 'includes/alertProvider.inc.php'; // Alert Provider ?>
                <form action="includes/addClassification.inc.php" method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-7"> <!-- Klassifikation -->
                            <label for="label">Klassifikation Bezeichnung</label>
                            <input class="form-control" type="text" id="label" name="label" required>
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

        <h3 class="mt-3" id="savedClassification">Erfasste Klassifikationen</h3>
        <hr class="mb-4">
        <div class="row">
            <div class="col-12 mb-5">
                <?php
                // SQL-Query bereitstellen
                $sqlquery = "SELECT * FROM classification";
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
                                <td><?php echo intval($row['classificationID']); ?></td>
                                <td><?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['active'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="lead">Keine Einträge gefunden</p>
                <p>Sie haben für die ausgewählte Ziel-Datenbank noch keine Klassifikation erfasst. Erfassen Sie Ihre erste Klassifikation gleich <a href="classification.php#addClassification">hier</a>.</p>
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