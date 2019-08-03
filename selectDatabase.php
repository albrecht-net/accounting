<?php
// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php');
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
    <link rel="stylesheet" href="css/login.css">

    <title>Datenbank auswählen</title>
</head>
<body class="text-center">
    <div class="form-group-database">
        <h3>Datenbank auswählen</h3>
        <?php if (!$_SESSION['userDb']['userDbSet']): // Überprüfen ob Benutzer Db ausgewählt wurde ?>
            <?php include_once 'includes/alertProvider.inc.php'; // Alert Provider ?>
            <p>Bitte wählen Sie die Zieldatenbank aus, auf welcher die Eingaben der Aktuellen Sitzung geschrieben werden sollen.</p>

            <?php
            // Prüfen ob eine Datenbank für den angemeldeten Benutzer verfügbar ist
            $sqlquery = "SELECT `dbID`, `dbHost`, `dbName` FROM `databases` WHERE `userID` = '" . intval($_SESSION['userID']) . "'";
            $result = mysqli_query($config['link'], $sqlquery);
            if (mysqli_num_rows($result) >= 1):

                if (empty($_GET['rd'])): ?>
                <form action="includes/selectDatabase.inc.php" method="POST">
                <?php else: ?>
                <form action="includes/selectDatabase.inc.php?rd=<?php echo urlencode($_GET['rd']); ?>" method="POST">
                <?php endif; ?>
                    <div class="form-group"> <!-- Datenbank Select -->
                        <select class="form-control" id="dbID" name="dbID">
                            <option disabled selected>Datenbank auswählen</option>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <option value="<?php echo intval($row['dbID']); ?>"><?php echo htmlspecialchars($row['dbName'], ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($row['dbHost'], ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group"> <!-- Datenbank speichern -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="saveDbSelection" name="saveDbSelection" value="1">
                            <label class="form-check-label" for="saveDbSelection">Auswahl speichern</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <button type="submit" class="btn btn-primary btn-block" name="submit">Bestätigen</button>
                        </div>
                        <div class="col">
                            <?php if (empty($_GET['rd'])): ?>
                            <a href="index.php" class="btn btn-secondary btn-block" role="button">Überspringen</a>
                            <?php else: ?>
                            <a href="<?php echo $_GET['rd']; ?>" class="btn btn-secondary btn-block" role="button">Überspringen</a>
                            <?php endif ?>
                        </div>
                    </div>
                </form>

            <?php else: ?>
                <p>Es wurde keine Datenbank welche mit Ihrem Account verknüpft ist gefunden!</p>
                <?php if (empty($_GET['rd'])): ?>
                <a href="index.php" class="btn btn-primary" role="button">OK</a>
                <?php else: ?>
                <a href="<?php echo $_GET['rd']; ?>" class="btn btn-primary" role="button">OK</a>
                <?php endif ?>
            <?php endif; ?>

        <?php else: ?>
        <p class="lead">Für die aktuelle Sitzung wurde bereits eine Datenbank ausgewählt.</p>
        <p>Um Fehler zu vermeiden müssen Sie sich <a href="logout.php?forceDatabaseSelect=1">abmelden</a> um die Datenbank zu wechseln. Sie haben eine Standarddatenbank festgelegt? <a href="settings/database.php#defaultDatabase">Klicken Sie hier</a>, damit Sie diese entfernen können. </p>
        <?php endif ?>
    </div>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>