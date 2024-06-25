<?php
session_start();

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Wenn nicht, zur Login-Seite umleiten
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verwaltung</title>
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="content">
        <div class="banner">
            <h1>Administration </h1>
        </div>
        <!-- Auswahl -->
        <div class="admin-content">
            <a class="admin-adjust" href="./users.php">Benutzer verwalten</a><br>
            <a class="admin-adjust" href="./books.php">Bücher verwalten</a><br>
            <a class="admin-adjust" href="./customers.php">Kunden verwalten</a>
            <form action="logout.php" method="post">
                <input type="submit" class="login-button" value="Logout">
            </form>
        </div>
    </div>
</body>