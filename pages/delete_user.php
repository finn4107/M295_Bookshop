<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include '../includes/connection.php';

$id = $_GET['ID'];
// Benutzer löschen
$query = "DELETE FROM benutzer WHERE ID = :ID";
$stmt = $db->prepare($query);
$stmt->bindValue(':ID', $id);
$stmt->execute();

header("Location: users.php");
exit;
?>
