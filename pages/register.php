<?php
include '../includes/connection.php';


$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    // Serverseitige Validierung
    if (empty($_POST['username'])) {
        $errors[] = "Benutzername ist erforderlich.";
    }
    elseif (strlen($username) > 50) {
        $errors[] = "Benutzername darf nicht länger als 50 Zeichen sein.";
    }

    if (empty($_POST['password'])) {
        $errors[] = "Passwort ist erforderlich.";
    }
    elseif (strlen($password) > 50) {
        $errors[] = "Passwort darf nicht länger als 50 Zeichen sein.";
    }

    // Wenn keine Fehler vorhanden sind, Daten in die Datenbank einfügen
    if (empty($errors)) {
        // Passwort hashen
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Daten in die Datenbank einfügen
        $stmt = $db->prepare("INSERT INTO benutzer (benutzername, passwort) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);

        // Weiterleitung nach erfolgreicher Registrierung
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrieren</title>
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="content">
        <div class="banner">
            <h1>Registrieren</h1>
        </div>
        <!-- Registrierungsformular -->
        <form class="login-page" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input class="login-field" type="text" name="username" placeholder="Benutzername" required><br>
            <input class="login-field" type="password" name="password" placeholder="Passwort" required><br>
            <input class="login-button" type="submit" value="Registrieren"><br>
        </form>
        <?php
        // Fehlermeldungen anzeigen, falls vorhanden
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
