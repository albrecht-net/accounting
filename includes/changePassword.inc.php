<?php
// Array Eingabe
$dataInput = array(
    'oldPassword' => $_POST['inputOldPassword'],
    'password1' => $_POST['inputPassword1'],
    'password2' => $_POST['inputPassword2']
);

// Passwortübereinstimmung prüfen
if ($dataInput['password1'] != $dataInput['password2']) {
    $msg['noPasswordMatch'] = 1;
} else {
    // SQL-Query bereitstellen
    $sqlquery = "SELECT `password` FROM `users` WHERE `userID` = '" . $_SESSION['userID'] . "'";
    $result = mysqli_query($config['link'], $sqlquery);

    // Benutzer abfragen
    if (mysqli_num_rows($result) != 1) {
        $dataInput['invalid'] = 1;
    } else {
        // Abfrage in Array schreiben
        $dataDb = mysqli_fetch_assoc($result);

        // Passwort validieren
        if (!password_verify($dataInput['oldPassword'], $dataDb['password'])) {
            $msg['oldPasswordInvalid'] = 1;
        } else {
            // Passwort Hash
            $dataInput['password'] = password_hash($dataInput['password1'], PASSWORD_DEFAULT);
            
            unset($dataInput['oldPassword']);
            unset($dataInput['password1']);
            unset($dataInput['password2']);

            // SQL-Query bereitstellen
            $set = [];
            foreach ($dataInput as $column => $value) {
                $set[] = "`" . $column . "` = '" . $value . "'";
            }
            $sqlquery = "UPDATE `users` SET " . implode(", ", $set) . " WHERE `users`.`userID` = '" . $_SESSION['userID'] . "'";

            // SQL-Query ausführen und überprüfen
            if (!mysqli_query($config['link'], $sqlquery)) {
                echo date('H:i:s') . ' MySQL Error: ' . mysqli_error($config['link']);
                exit();
            }

            // Benutzer abmelden
            session_start();
            session_destroy();
            header("Location: ../login.php?passwordchanged=1");
        }
    }
}
?>