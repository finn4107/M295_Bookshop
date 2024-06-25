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
    $autor = $_POST['autor'];
    $kurztitle = $_POST['kurztitle'];
    $kategorie = $_POST['kategorie'];
    $zustand = $_POST['zustand'];

    // Ein neues Buch hinzufügen
    $stmt = $db->prepare("INSERT INTO buecher (autor, kurztitle, kategorie, zustand) VALUES (:autor, :kurztitle, :kategorie, :zustand)");
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':kurztitle', $kurztitle);
    $stmt->bindParam(':kategorie', $kategorie);
    $stmt->bindParam(':zustand', $zustand);

    if ($stmt->execute()) {
        header("Location: books.php");
        exit;
    } else {
        echo "Fehler beim Hinzufügen des Buches.";
    }
} else {
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
    <title>Neues Buch hinzufügen</title>
    <link rel="stylesheet" href="../css/sortiment.css">
</head>
<body>

<div class="content">
    <div class="banner">
        <h1>Neues Buch hinzufügen</h1>
    </div>
    <div class="add-div">
    <form action="add_book.php" method="post">
        <label class="add-label" for="autor">Autor:</label>
        <input class="add-field" type="text" id="autor" name="autor"><br>
        <label class="add-label" for="kurztitle">Kurztitel:</label>
        <input class="add-field" type="text" id="kurztitle" name="kurztitle"><br>
        <label class="add-label" for="kategorie">Kategorie:</label>
        <select class="add-dropdown-content" id="kategorie" name="kategorie">
            <?php foreach ($kategories as $kategorie): ?>
                <option  value="<?php echo $kategorie['id'] ?>">
                    <?php echo htmlspecialchars($kategorie['kategorie']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        
        <label class="add-label" for="zustand">Zustand:</label>
        <input class="add-field" type="text" id="zustand" name="zustand"><br>
        <button class="add-button" type="submit">Hinzufügen</button>
    </form>
    </div>
</div>

</body>
</html>
