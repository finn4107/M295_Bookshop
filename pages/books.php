<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include '../includes/connection.php';
include '../includes/navbar.php';
include '../includes/fonts.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';
$filter = isset($_GET['filter']) ? $_GET['filter'] : [];

$query = "SELECT * FROM buecher WHERE 1 = 1";
$conditions = [];
if (!empty($filter)) {
    foreach ($filter as $key => $value) {
        if (!empty($value)) {
            if ($key === 'autor' || $key === 'kurztitle') {
                $conditions[] = "$key LIKE :$key";
                $value = "%$value%";
            } elseif ($key === 'kategorie' || $key === 'zustand') {
                $conditions[] = "$key = :$key";
            }
        }
    }
}
if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}
$query .= " ORDER BY $sort $sort_order LIMIT $limit OFFSET $offset";

$stmt = $db->prepare($query);

if (!empty($filter)) {
    foreach ($filter as $key => $value) {
        if (!empty($value)) {
            if ($key === 'autor' || $key === 'kurztitle') {
                $value = "%$value%";
            }
            $stmt->bindValue(":$key", $value);
        }
    }
}
$stmt->execute();
$books = $stmt->fetchAll();

$stmt = $db->prepare("
    SELECT DISTINCT k.id, k.kategorie
    FROM buecher b
    JOIN kategorien k ON b.kategorie = k.id
");
$stmt->execute();
$kategories = $stmt->fetchAll();

$stmt = $db->prepare("SELECT DISTINCT zustand FROM buecher");
$stmt->execute();
$zustands = $stmt->fetchAll();

$query = "SELECT COUNT(*) FROM buecher WHERE 1 = 1";
if (!empty($filter)) {
    $query .= " AND " . implode(" AND ", $conditions);
}
$stmt = $db->prepare($query);

if (!empty($filter)) {
    foreach ($filter as $key => $value) {
        if (!empty($value)) {
            $stmt->bindValue(":$key", $value);
        }
    }
}
$stmt->execute();
$total_books = $stmt->fetchColumn();

$total_pages = ceil($total_books / $limit);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bücher verwalten</title>
    <link rel="stylesheet" href="../css/sortiment.css">
</head>
<body>

<div class="content">
    <div class="banner">
        <h1>Bücher verwalten</h1>
    </div>

    <div class="function-container">
        <p class="searchp">Suchen sie nach einem Buch?</p>
        <p class="sort-text">Sortieren </p>
        <div class="dropdown" id="sort-dropdown">
            <img src="../images/sort.png" alt="Sortieren" class="sort-icon">
            <div class="dropdown-content">
                <a href="books.php?sort=id&sort_order=asc">ID (aufsteigend)</a>
                <a href="books.php?sort=id&sort_order=desc">ID (absteigend)</a>
                <a href="books.php?sort=autor&sort_order=asc">Autor (aufsteigend)</a>
                <a href="books.php?sort=autor&sort_order=desc">Autor (absteigend)</a>
            </div>
        </div>

        <p class="filter-text">Filtern </p>
        <div class="dropdown" id="filter-dropdown" onmouseover="showDropdown('filter-dropdown-content')" onmouseout="hideDropdown('filter-dropdown-content')">
            <img src="../images/filter.png" alt="Filtern" class="filter-icon">
            <div class="dropdown-content" id="filter-dropdown-content">
                <form action="books.php" method="get">
                    <input class="filter-box" type="hidden" name="page" value="1">
                    <label class="filter-label" for="autor">Autor:</label>
                    <input class="filter-box" type="text" name="filter[autor]" id="autor" value="<?php echo isset($filter['autor']) ? htmlspecialchars($filter['autor']) : '' ?>"><br>
                    <label class="filter-label" for="kurztitle">Kurztitel:</label>
                    <input class="filter-box" type="text" name="filter[kurztitle]" id="kurztitle" value="<?php echo isset($filter['kurztitle']) ? htmlspecialchars($filter['kurztitle']) : '' ?>"><br>
                    <label class="filter-label" for="kategorie">Kategorie:</label>
                    <select class="filter-box" name="filter[kategorie]" id="kategorie">
                        <option value="">- Kategorie -</option>
                        <?php foreach ($kategories as $kategorie): ?>
                            <option value="<?php echo $kategorie['id'] ?>" <?php echo isset($filter['kategorie']) && $filter['kategorie'] == $kategorie['id'] ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($kategorie['kategorie']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>
                    <label class="filter-label" for="zustand">Zustand:</label>
                    <select class="filter-box" name="filter[zustand]" id="zustand">
                        <option value="">- Zustand -</option>
                        <?php foreach ($zustands as $zustand): ?>
                            <option value="<?php echo htmlspecialchars($zustand['zustand']) ?>" <?php echo isset($filter['zustand']) && $filter['zustand'] == $zustand['zustand'] ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($zustand['zustand']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>
                    <button class="filter-button" type="submit">Filtern</button>
                </form>
            </div>
        </div>
    </div>

    <div class="book-container">
        <?php
        $count = 0;
        foreach ($books as $book):
            if ($count % 2 == 0) echo '<div class="row">';
            ?>
            <div class="article column">
                <div class="bookicon"><img src="../images/book_icon.png" alt="Buch Icon"></div>
                <div class="buchbeschreibung">
                    <p>
                        <?php
                        $bookInfo = [];
                        if (!empty($book['kurztitle'])) $bookInfo[] = htmlspecialchars($book['kurztitle']);
                        if (!empty($book['autor'])) $bookInfo[] = 'Autor: ' . htmlspecialchars($book['autor']);
                        if (!empty($book['kategorie'])) $bookInfo[] = 'Kategorie: ' . htmlspecialchars($book['kategorie']);
                        if (!empty($book['id'])) $bookInfo[] = 'ID: ' . htmlspecialchars($book['id']);
                        if (!empty($book['zustand'])) $bookInfo[] = 'Zustand: ' . htmlspecialchars($book['zustand']);
                        echo implode(', ', $bookInfo);
                        ?>
                    </p>
                    <a href="edit_book.php?id=<?php echo $book['id'] ?>"><img src="../images/edit.png" alt="Bearbeiten" class="edit-icon"></a>
                    <a href="delete_book.php?id=<?php echo $book['id'] ?>" onclick="return confirm('Sind Sie sicher, dass Sie dieses Buch löschen möchten?')"><img src="../images/delete.png" alt="Löschen" class="delete-icon"></a>
                </div>
            </div>
            <?php
            $count++;
            if ($count % 2 == 0 || $count == $total_books) echo '</div>';
        endforeach;
        ?>
    </div>

    <br>

    <div class="add-book-container">
        <a href="add_book.php">
         <p class="add-p">Buch hinzufügen</p><img class="add-img" src="../images/add.png" alt="Neues Buch hinzufügen" class="add-icon"></a>
    </div>

    <div class="pagination">
        <?php if ($page > 1 || $page < $total_pages): ?>
            <?php
            $params = $_GET;
            $params['page'] = $page - 1;
            $prev_url = 'books.php?' . http_build_query($params);
            $params['page'] = $page + 1;
            $next_url = 'books.php?' . http_build_query($params);
            ?>
            <a href="<?php echo $prev_url ?>"> < </a>
            <p> Seite <?php echo $page ?> </p>
            <a href="<?php echo $next_url ?>"> > </a>
        <?php endif; ?>
    </div>
</div>

<script>
    function showDropdown(id) {
        document.getElementById(id).style.display = 'block';
    }

    function hideDropdown(id) {
        document.getElementById(id).style.display = 'none';
    }
</script>

</body>
</html>
