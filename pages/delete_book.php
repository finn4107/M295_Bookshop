<?php
include '../includes/connection.php';

// Überprüfen, ob die Buch-ID im URL-Parameter vorhanden ist
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $book_id = $_GET['id'];

    // SQL-Statement zum Löschen des Buches
    $sql = "DELETE FROM buecher WHERE id = :book_id";

    // Vorbereiten und Ausführen des SQL-Statements
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    
    // Versuchen, das Buch zu löschen
    try {
        $stmt->execute();
        // Weiterleitung zur Bücherseite nach erfolgreichem Löschen
        header("Location: books.php");
        exit;
    } catch(PDOException $e) {
        // Fehlermeldung ausgeben, wenn das Löschen fehlschlägt
        echo "Fehler beim Löschen des Buches: " . $e->getMessage();
    }
} else {
    // Fehlermeldung ausgeben, wenn keine Buch-ID im URL-Parameter vorhanden ist
    echo "Buch-ID nicht gefunden.";
}
?>
