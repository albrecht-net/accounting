<?php
// Array Eingabe
$dataInput = array(
    'username' => mysqli_real_escape_string($config['link'], $_POST['inputUsername']),
    'password' => $_POST['inputPassword']
);

// SQL-Query bereitstellen
$sqlquery = "SELECT `username`, `password`, `userID` FROM `users` WHERE `username` = '" . $dataInput['username'] . "' AND `activation` = 'Y' AND `status` = 'Y'";
$result = mysqli_query($config['link'], $sqlquery);

// Benutzer abfragen
if (mysqli_num_rows($result) != 1) {
    $msg['invalid'] = 1;
} else {
    // Abfrage in Array schreiben
    $dataDb = mysqli_fetch_assoc($result);

    // Passwort validieren
    if (!password_verify($dataInput['password'], $dataDb['password'])) {
        $msg['invalid'] = 1;
    } else {
        // Benutzerdaten in Session schreiben
        $_SESSION['userID'] = $dataDb['userID'];
        $_SESSION['username'] = $dataDb['username'];

        // Benutzerdatenbank
        $_SESSION['userDb']['userDbSet'] = 0;

        // Weiterleitung
        if (empty($_GET['rd'])) {
            header('Location: selectDatabase.php');
        } else {
            $rd = $_GET['rd'];
            header('Location: selectDatabase.php?rd=' . urlencode($rd));
        }
        exit();
    }
}
?>