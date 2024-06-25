<?php
session_start();
include '../includes/connection.php';

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Benutzerdaten aus dem Formular extrahieren
    $username = htmlspecialchars($_POST['username']);
    $current_password = htmlspecialchars($_POST['password']);
    $new_password = htmlspecialchars($_POST['newpw']);
    $confirm_new_password = htmlspecialchars($_POST['confirm_newpw']);

    // Serverseitige Validierung
    if (empty($username)) {
        $errors[] = "Benutzername ist erforderlich.";
    }
    elseif (strlen($username) > 50) {
        $errors[] = "Benutzername darf nicht länger als 50 Zeichen sein.";
    }

    if (empty($current_password)) {
        $errors[] = "Aktuelles Passwort ist erforderlich.";
    }

    if (empty($new_password)) {
        $errors[] = "Neues Passwort ist erforderlich.";
    }
    elseif (strlen($new_password) > 50) {
        $errors[] = "Neues Passwort darf nicht länger als 50 Zeichen sein.";
    }

    if (empty($confirm_new_password)) {
        $errors[] = "Bestätigung des neuen Passworts ist erforderlich.";
    }

    // Überprüfen, ob das neue Passwort und die Bestätigung übereinstimmen
    if ($new_password !== $confirm_new_password) {
        $errors[] = "Die eingegebenen Passwörter stimmen nicht überein.";
    }

    if (empty($errors)) {
        // Benutzer aus der Datenbank abrufen
        $stmt = $db->prepare("SELECT * FROM benutzer WHERE benutzername = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Überprüfen, ob der Benutzer existiert und das aktuelle Passwort korrekt ist
        if ($user && password_verify($current_password, $user['passwort'])) {
            // Passwort hashen
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Passwort in der Datenbank aktualisieren
            $stmt = $db->prepare("UPDATE benutzer SET passwort = ? WHERE benutzername = ?");
            $stmt->execute([$hashed_new_password, $username]);

            // Meldung anzeigen
            $success = "Passwort erfolgreich geändert!";
        } else {
            $errors[] = "Ungültige Benutzerdaten oder Passwort.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Passwort Zurücksetzen</title>
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
    <?php
    include '../includes/fonts.php';
    include '../includes/navbar.php';
    ?>
    <div class="content">
        <div class="banner">
            <h1>Passwort Zurücksetzen</h1>
        </div>
        <form class="login-page" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input class="login-field" type="text" name="username" placeholder="Benutzername" required><br>
            <input class="login-field" type="password" name="password" placeholder="Aktuelles Passwort" required><br>
            <input class="login-field" type="password" name="newpw" placeholder="Neues Passwort" required><br>
            <input class="login-field" type="password" name="confirm_newpw" placeholder="Neues Passwort bestätigen" required><br>
            <br>
            <input class="login-button" type="submit" value="Bestätigen"><br>
        </form>
        <?php 
            // Fehlermeldungen anzeigen
            if (!empty($errors)) {
                echo '<div class="error">';
                foreach ($errors as $error) {
                    echo '<p>' . $error . '</p>';
                }
                echo '</div>';
            }
            if(isset($success)) echo '<div class="success">' . $success . '</div>';
        ?>
    </div>
    <?php
    include '../includes/footer.php';
    ?>
</body>
</html>
