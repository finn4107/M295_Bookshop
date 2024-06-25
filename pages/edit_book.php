<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include '../includes/connection.php';
include '../includes/navbar.php';
include '../includes/fonts.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $autor = $_POST['autor'];
    $kurztitle = $_POST['kurztitle'];
    $kategorie = $_POST['kategorie'];
    $zustand = $_POST['zustand'];

    // Aktualisieren des Buches
    $stmt = $db->prepare("UPDATE buecher SET autor = :autor, kurztitle = :kurztitle, kategorie = :kategorie, zustand = :zustand WHERE id = :id");
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':kurztitle', $kurztitle);
    $stmt->bindParam(':kategorie', $kategorie);
    $stmt->bindParam(':zustand', $zustand);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header("Location: edit_book.php?id=" . $id);
        exit;
    } else {
        echo "Fehler beim Aktualisieren des Buches.";
    }
} else {
    $id = $_GET['id'];
    $stmt = $db->prepare("SELECT * FROM buecher WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $book = $stmt->fetch();

    $stmt = $db->prepare("SELECT * FROM kategorien");
    $stmt->execute();
    $kategories = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buch bearbeiten</title>
    <link rel="stylesheet" href="../css/sortiment.css">
</head>
<body>

<div class="content">
    <div class="banner">
        <h1>Buch bearbeiten</h1>
    </div>

    <form action="edit_book.php" method="post">
        <input type="hidden" name="id" value="<?php echo $book['id'] ?>">
        <label for="autor">Autor:</label>
        <input type="text" id="autor" name="autor" value="<?php echo htmlspecialchars($book['autor']) ?>"><br>
        <label for="kurztitle">Kurztitel:</label>
        <input type="text" id="kurztitle" name="kurztitle" value="<?php echo htmlspecialchars($book['kurztitle']) ?>"><br>
        <label for="kategorie">Kategorie:</label>
        <select id="kategorie" name="kategorie">
            <?php foreach ($kategories as $kategorie): ?>
                <option value="<?php echo $kategorie['id'] ?>" <?php echo $book['kategorie'] == $kategorie['id'] ? 'selected' : '' ?>>
                    <?php echo htmlspecialchars($kategorie['kategorie']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <label for="zustand">Zustand:</label>
        <input type="text" id="zustand" name="zustand" value="<?php echo htmlspecialchars($book['zustand']) ?>"><br>
        <button type="submit">Speichern</button>
    </form>
</div>

</body>
</html>
