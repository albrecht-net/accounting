<?php
// Array Eingabe
$dataInput = array(
    'username' => mysqli_real_escape_string($config['link'], $_POST['inputUsername']),
    'password' => $_POST['inputPassword']
);

// SQL-Query bereitstellen
$sqlquery = "SELECT `username`, `password`, `userID` FROM `users` WHERE `username` = '" . $dataInput['username'] . "' AND `activation` = 1 AND `status` = 1";
$result = mysqli_query($config['link'], $sqlquery);

// Benutzer abfragen
if (mysqli_num_rows($result) != 1) {
    $dataInput['invalid'] = 1;
} else {
    // Abfrage in Array schreiben
    $dataDb = mysqli_fetch_assoc($result);

    // Passwort validieren
    if (!password_verify($dataInput['password'], $dataDb['password'])) {
        $dataInput['invalid'] = 1;
    } else {
        // Benutzerdaten in Session schreiben
        $_SESSION['uid'] = $dataDb['userID'];
        $_SESSION['username'] = $dataDb['username'];

        // Weiterleitung
        if (empty($_GET['rd'])) {
            header('Location: index.php');
        } else {
            $rd = $_GET['rd'];
            header('Location: ' . $rd);
        }
        exit();
    }
}
?>