<?php
session_start();

// Konfiguration einbinden
require_once 'config.php';

// Prüfen ob Benutzer angemeldet
require 'includes/loginSessionCheck.inc.php';
if (!$lsc) {
    header('Location: login.php');
    exit();
}

// Überprüfen ob Submit geklickt wurde
if (isset($_POST['submit']) && !empty($_POST['dbID'])) {
    if (!include 'includes/.php') {
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

    <title>Datenbank auswählen</title>
</head>
<body>
    <h2>Datenbank auswählen</h2>
    <p>Bitte wählen Sie die Zieldatenbank aus, auf welcher die Eingaben der Aktuellen Sitzung geschrieben werden sollen.</p>
    
    <?php
    // Prüfen ob eine Datenbank für den angemeldeten Benutzer verfügbar ist
    $sqlquery = "SELECT `dbID`, `dbHost`, `dbName` FROM `databases` WHERE `userID` = '" . $_SESSION['userID'] . "'";
    $result = mysqli_query($config['link'], $sqlquery);
    if (mysqli_num_rows($result) >= 1):

        if (empty($_GET['rd'])): ?>
        <form action="selectDatabase.php" method="POST">
        <?php else: ?>
        <form action="selectDatabase.php?rd=<?php echo urlencode($_GET['rd']); ?>" method="POST">
        <?php endif; ?>
            <div class="form-group">
                <label for="dbID">Datenbank auswählen</label>
                <select class="form-control" id="dbID" name="dbID">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <option value="<?php echo $row['dbID']; ?>"><?php echo $row['dbName'] . ', ' . $row['dbHost']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="saveDbSelection" name="saveDbSelection" value="1" disabled>
                <label class="form-check-label" for="saveDbSelection">Auswahl speichern</label>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Bestätigen</button>
        </form>

    <?php else: ?>
        <p>Es wurde keine Datenbank welche mit Ihrem Account verknüpft ist gefunden!</p>
        <a href="<?php echo $_GET['rd']; ?>" class="btn btn-primary" role="button">OK</a>
    <?php endif; ?>
</body>
</html>