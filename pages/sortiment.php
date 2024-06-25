<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lese-Lust</title>
    <link rel="stylesheet" href="../css/sortiment.css">
</head>
<body>

<?php
include '../includes/connection.php';
include '../includes/navbar.php';
include '../includes/fonts.php';
?>

<div class="content">

    <?php

    $page = isset($_GET['page'])? (int)$_GET['page'] : 1;
    $limit = 12; // Anzahl der Bücher pro Seite
    $offset = ($page - 1) * $limit;

    $sort = isset($_GET['sort'])? $_GET['sort'] : 'id';
    $sort_order = isset($_GET['sort_order'])? $_GET['sort_order'] : 'asc';
    $filter = isset($_GET['filter'])? $_GET['filter'] : [];

    $query = "SELECT * FROM buecher WHERE 1 = 1";
    $conditions = [];
    if (!empty($filter)) {
        foreach ($filter as $key => $value) {
            if (!empty($value)) {
                if ($key === 'autor' || $key === 'kurztitle') {
                    $conditions[] = "$key LIKE :$key";
                    $value = "%$value%"; // Um nach ähnlichen Begriffen zu suchen
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
                    $value = "%$value%"; // Um nach ähnlichen Begriffen zu suchen
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
    echo '<div class="banner">';
    echo '<h1>Unser Sortiment</h1>';
    echo '</div>';

    echo '<div class="function-container">';
    echo '<p class="searchp">Suchen sie nach einem Buch?</p>';
    echo '<p class="sort-text">Sortieren </p>';
    echo '<div class="dropdown" id="sort-dropdown">';
    echo '<img src="../images/sort.png" alt="Sortieren" class="sort-icon">';
    echo '<div class="dropdown-content">';
    echo '<a href="sortiment.php?sort=id&sort_order=asc">ID (aufsteigend)</a>';
    echo '<a href="sortiment.php?sort=id&sort_order=desc">ID (absteigend)</a>';
    echo '<a href="sortiment.php?sort=autor&sort_order=asc">Autor (aufsteigend)</a>';
    echo '<a href="sortiment.php?sort=autor&sort_order=desc">Autor (absteigend)</a>';
    echo '</div>';
    echo '</div>';

    echo '<p class="filter-text">Filtern </p>';
    echo '<div class="dropdown" id="filter-dropdown" onmouseover="showDropdown(\'filter-dropdown-content\')" onmouseout="hideDropdown(\'filter-dropdown-content\')">';
    echo '<img src="../images/filter.png" alt="Filtern" class="filter-icon">';
    echo '<div class="dropdown-content" id="filter-dropdown-content">';
    echo '<form action="sortiment.php" method="get">';
    echo '<input  class="filter-box" type="hidden" name="page" value="1">';
    echo '<label class="filter-label"for="autor">Autor:</label>';
    echo '<input class="filter-box" type="text" name="filter[autor]" id="autor" value="' . (isset($filter['autor']) ? htmlspecialchars($filter['autor']) : '') . '"><br>';
    echo '<label class="filter-label" for="kurztitle">Kurztitel:</label>';
    echo '<input class="filter-box" type="text" name="filter[kurztitle]" id="kurztitle" value="' . (isset($filter['kurztitle']) ? htmlspecialchars($filter['kurztitle']) : '') . '"><br>';
    echo '<label class="filter-label" for="kategorie">Kategorie:</label>';
    echo '<select  class="filter-box" name="filter[kategorie]" id="kategorie">';
    echo '<option value="">- Kategorie -</option>';
    foreach ($kategories as $kategorie) {
        $selected = isset($filter['kategorie']) && $filter['kategorie'] == $kategorie['id'] ? 'selected' : '';
        echo "<option value='{$kategorie['id']}' $selected>{$kategorie['kategorie']}</option>";
    }
    echo '</select><br>';
    echo '<label class="filter-label" for="zustand">Zustand:</label>';
    echo '<select class="filter-box" name="filter[zustand]" id="zustand">';
    echo '<option value="">- Zustand -</option>';
    foreach ($zustands as $zustand) {
        $selected = isset($filter['zustand']) && $filter['zustand'] == $zustand['zustand'] ? 'selected' : '';
        echo "<option value='{$zustand['zustand']}' $selected>{$zustand['zustand']}</option>";
    
    }
    echo '</select><br>';
    echo '<button class="filter-button" type="submit">Filtern</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '<div class="book-container">';
    $count = 0;
    foreach ($books as $book) {
        if ($count % 2 == 0) {
            echo '<div class="row">';
        }
        echo '<div class="article column">';
        if (!empty($book['kurztitle'])) {
            echo '<div class="bookicon"><img src="../images/book_icon.png" alt="Buch Icon"></div>';
        }
        echo '<div class="buchbeschreibung"><p>';
        $bookInfo = [];
        if (!empty($book['kurztitle'])) {
            $bookInfo[] = htmlspecialchars($book['kurztitle']);
        }
        if (!empty($book['autor'])) {
            $bookInfo[] = htmlspecialchars('Autor: '. $book['autor']);
        }
        if (!empty($book['kategorie'])) {
            $bookInfo[] = htmlspecialchars('Kategorie: '. $book['kategorie']);
        }
        if (!empty($book['id'])) {
            $bookInfo[] = htmlspecialchars('ID: '. $book['id']);
        }
        if (!empty($book['zustand'])) {
            $bookInfo[] = htmlspecialchars('Zustand: '. $book['zustand']);
        }
        echo implode(', ', $bookInfo);
        echo '</p>';
        echo '</div>';
        echo '</div>';
        $count++;
        if ($count % 2 == 0 || $count == $total_books) {
            echo'</div>';
        }
    }
    echo '</div>';
    echo '<br>';

    echo '<div class="pagination">';
    if ($page > 1 or $page < $total_pages) {
        $params = $_GET;
        $params['page'] = $page - 1;
        $prev_url = 'sortiment.php?' . http_build_query($params);
        $params['page'] = $page + 1;
        $next_url = 'sortiment.php?' . http_build_query($params);

        echo '<a href="'. $prev_url .'"> < </a>' . '<p> Seite '. $page. ' </p>' . '<a href="'. $next_url .'"> > </a>';
    }
    echo '</div>';
    ?>

</div>

<?php
include '../includes/footer.php';
?>

<script>
    function showDropdown(id) {
        document.getElementById(id).style.display = "block";
    }

    function hideDropdown(id) {
        document.getElementById(id).style.display = "none";
    }
</script>

</body>
</html>
