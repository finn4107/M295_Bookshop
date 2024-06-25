<?php
// Verbindungsparameter
$host = 'localhost';
$dbname = 'books';
$user = 'root';
$password = '';

// Mit Database verbinden
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>