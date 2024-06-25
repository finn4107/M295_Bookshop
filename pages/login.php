<?php
session_start();
include '../includes/connection.php';

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Benutzerdaten aus dem Formular holen
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Serverseitige Validierung
    if (empty($username)) {
        $errors[] = "Benutzername ist erforderlich.";
    }
    elseif (strlen($username) > 45) {
        $errors[] = "Benutzername darf nicht länger als 50 Zeichen sein.";
    }
    

    if (empty($password)) {
        $errors[] = "Passwort ist erforderlich.";
    }
    elseif (strlen($password) < 8) {
        $errors[] = "Passwort darf nicht kürzer als 8 Zeichen sein.";
    }

    if (empty($errors)) {
        // Benutzer aus der Datenbank abrufen
        $stmt = $db->prepare("SELECT * FROM benutzer WHERE benutzername = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Passwort überprüfen
        if ($user && password_verify($password, $user['passwort'])) {
            // Benutzer erfolgreich authentifiziert, Session starten und Benutzer-ID speichern
            $_SESSION['user_id'] = $user['ID'];
            // Setzen Sie die Session-Variable 'loggedin' auf true
            $_SESSION['loggedin'] = true;
            // Benutzer zur anderen Seite weiterleiten
            header("Location: ./admin.php");
            // Sicherstellen, dass kein weiterer Code ausgeführt wird
            exit;
        } else {
            $error = "Ungültiger Benutzername oder Passwort.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Anmelden</title>
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="content">
        <div class="banner">
            <h1>Anmelden</h1>
        </div>
        <!-- Login-Formular -->
        <form class="login-page" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input class="login-field" type="text" name="username" placeholder="Benutzername" required><br>
            <input class="login-field" type="password" name="password" placeholder="Passwort" required><br>
            <input class="login-button" type="submit" value="Anmelden"><br>
        </form>
		<a href="./register.php">Noch keinen Account? Hier registrieren!</a><br>
		<a href="./pwreset.php">Passwort vergessen?</a>
        <?php 
            if(isset($error)) echo '<div class="error">' . $error . '</div>';
            if(isset($success)) echo '<div class="success">' . $success . '</div>';
        ?>
        <?php 
            // Fehlermeldungen anzeigen
            if (!empty($errors)) {
                echo '<div class="error">';
                foreach ($errors as $error) {
                    echo '<p>' . $error . '</p>';
                }
                echo '</div>';
            }
        ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
