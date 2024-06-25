<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
include '../includes/navbar.php';
include '../includes/connection.php';
include '../includes/fonts.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $benutzername = $_POST['benutzername'];
    $name = $_POST['name'];
    $vorname = $_POST['vorname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Serverseitige Validierung
    if (strlen($benutzername) > 45 || strlen($password) < 8) {
        echo "Fehler: Benutzername darf nicht länger als 45 Zeichen und Passwort nicht kürzer als 8 Zeichen sein.";
        exit;
    }
    // Einen neuen Benutzer einfügen
    $query = "INSERT INTO benutzer (benutzername, name, vorname, email, passwort) VALUES (:benutzername, :name, :vorname, :email, :passwort)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':benutzername', $benutzername);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':vorname', $vorname);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':passwort', password_hash($password, PASSWORD_DEFAULT));
    $stmt->execute();

    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuen Benutzer hinzufügen</title>
    <link rel="stylesheet" href="../css/sortiment.css">
</head>
<body>
<div class="content">
    <h1>Neuen Benutzer hinzufügen</h1>
    <div class="add-div">
    <form action="add_user.php" method="post">
        <label class="add-label" for="benutzername">Benutzername:</label>
        <input class="add-field" type="text" id="benutzername" name="benutzername" required maxlength="45"><br>
        <label class="add-label" for="name">Name:</label>
        <input class="add-field" type="text" id="name" name="name" required><br>
        <label class="add-label" for="vorname">Vorname:</label>
        <input class="add-field" type="text" id="vorname" name="vorname" required><br>
        <label class="add-label" for="email">E-Mail:</label>
        <input class="add-field" type="email" id="email" name="email" required><br>
        <label class="add-label" for="password">Passwort:</label>
        <input class="add-field" type="password" id="password" name="password" required minlength="8"><br>
        <button class="add-button" type="submit">Hinzufügen</button>
    </form>
    </div>
</div>
</body>
</html>
