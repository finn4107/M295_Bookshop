<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include '../includes/connection.php';

$kid = $_GET['kid'];
// Kunde löschen
$query = "DELETE FROM kunden WHERE kid = :kid";
$stmt = $db->prepare($query);
$stmt->bindValue(':kid', $kid);
$stmt->execute();

header("Location: customers.php");
exit;
?>
