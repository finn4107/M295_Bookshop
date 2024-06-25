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
    $vorname = $_POST['vorname'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $geschlecht = $_POST['geschlecht'];
    $kontaktpermail = isset($_POST['kontaktpermail']) ? 1 : 0;

    // Einen neuen Kunden einfügen
    $query = "INSERT INTO kunden (vorname, name, email, geschlecht, kontaktpermail) VALUES (:vorname, :name, :email, :geschlecht, :kontaktpermail)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':vorname', $vorname);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':geschlecht', $geschlecht);
    $stmt->bindValue(':kontaktpermail', $kontaktpermail);
    $stmt->execute();

    header("Location: customers.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuen Kunden hinzufügen</title>
    <link rel="stylesheet" href="../css/sortiment.css">
</head>
<body>
<div class="content">
    <h1>Neuen Kunden hinzufügen</h1>
    <div class="add-div">
    <form action="add_customer.php" method="post">
        <label class="add-label"for="vorname">Vorname:</label>
        <input class="add-field" type="text" id="vorname" name="vorname" required><br>
        <label class="add-label" for="name">Name:</label>
        <input class="add-field" type="text" id="name" name="name" required><br>
        <label class="add-label" for="email">E-Mail:</label>
        <input class="add-field" type="email" id="email" name="email" required><br>
        <label class="add-label" for="geschlecht">Geschlecht:</label>
        <select class="add-dropdown-content" id="geschlecht" name="geschlecht" required>
            <option value="M">Männlich</option>
            <option value="W">Weiblich</option>
        </select><br>
        <label class="add-label" for="kontaktpermail">Kontakt per Mail:</label>
        <input class="add-check" type="checkbox" id="kontaktpermail" name="kontaktpermail"><br>
        <button class="add-button" type="submit">Hinzufügen</button>
    </form>
    </div>
</div>
</body>
</html>
