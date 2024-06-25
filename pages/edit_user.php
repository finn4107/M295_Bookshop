<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include '../includes/connection.php';
include '../includes/navbar.php';
include '../includes/fonts.php';

$id = $_GET['ID'];

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
    // Aktualisieren des Benutzers
    $query = "UPDATE benutzer SET benutzername = :benutzername, name = :name, vorname = :vorname, email = :email, passwort = :passwort WHERE ID = :ID";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':benutzername', $benutzername);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':vorname', $vorname);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':passwort', password_hash($password, PASSWORD_DEFAULT));
    $stmt->bindValue(':ID', $id);
    $stmt->execute();

    header("Location: users.php");
    exit;
}

$query = "SELECT * FROM benutzer WHERE ID = :ID";
$stmt = $db->prepare($query);
$stmt->bindValue(':ID', $id);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer bearbeiten</title>
    <link rel="stylesheet" href="../css/sortiment.css">
</head>
<body>
<div class="content">
    <h1>Benutzer bearbeiten</h1>
    <form action="edit_user.php?ID=<?php echo $id; ?>" method="post">
        <label for="benutzername">Benutzername:</label>
        <input type="text" id="benutzername" name="benutzername" value="<?php echo htmlspecialchars($user['benutzername']); ?>" required maxlength="45"><br>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>
        <label for="vorname">Vorname:</label>
        <input type="text" id="vorname" name="vorname" value="<?php echo htmlspecialchars($user['vorname']); ?>" required><br>
        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required minlength="8"><br>
        <button type="submit">Speichern</button>
    </form>
</div>
</body>
</html>
