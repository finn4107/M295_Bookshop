<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include '../includes/connection.php';
include '../includes/navbar.php';
include '../includes/fonts.php';

$kid = $_GET['kid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vorname = $_POST['vorname'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $geschlecht = $_POST['geschlecht'];
    $kontaktpermail = isset($_POST['kontaktpermail']) ? 1 : 0;

    // Aktualisierung des Kunden
    $query = "UPDATE kunden SET vorname = :vorname, name = :name, email = :email, geschlecht = :geschlecht, kontaktpermail = :kontaktpermail WHERE kid = :kid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':vorname', $vorname);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':geschlecht', $geschlecht);
    $stmt->bindValue(':kontaktpermail', $kontaktpermail);
    $stmt->bindValue(':kid', $kid);
    $stmt->execute();

    header("Location: customers.php");
    exit;
}

$query = "SELECT * FROM kunden WHERE kid = :kid";
$stmt = $db->prepare($query);
$stmt->bindValue(':kid', $kid);
$stmt->execute();
$customer = $stmt->fetch();

if (!$customer) {
    header("Location: customers.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kunden bearbeiten</title>
    <link rel="stylesheet" href="../css/sortiment.css">
</head>
<body>
<div class="content">
    <h1>Kunden bearbeiten</h1>
    <form action="edit_customer.php?kid=<?php echo $kid; ?>" method="post">
        <label for="vorname">Vorname:</label>
        <input type="text" id="vorname" name="vorname" value="<?php echo htmlspecialchars($customer['vorname']); ?>" required><br>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required><br>
        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required><br>
        <label for="geschlecht">Geschlecht:</label>
        <select id="geschlecht" name="geschlecht" required>
            <option value="M" <?php echo $customer['geschlecht'] == 'M' ? 'selected' : ''; ?>>Männlich</option>
            <option value="W" <?php echo $customer['geschlecht'] == 'W' ? 'selected' : ''; ?>>Weiblich</option>
        </select><br>
        <label for="kontaktpermail">Kontakt per Mail:</label>
        <input type="checkbox" id="kontaktpermail" name="kontaktpermail" <?php echo $customer['kontaktpermail'] ? 'checked' : ''; ?>><br>
        <button type="submit">Speichern</button>
    </form>
</div>
</body>
</html>
