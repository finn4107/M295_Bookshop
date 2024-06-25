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

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'ID';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';
$filter = isset($_GET['filter']) ? $_GET['filter'] : [];

$query = "SELECT * FROM benutzer WHERE 1 = 1";
$conditions = [];
if (!empty($filter)) {
    foreach ($filter as $key => $value) {
        if (!empty($value)) {
            $conditions[] = "$key LIKE :$key";
            $value = "%$value%";
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
            $stmt->bindValue(":$key", $value);
        }
    }
}
$stmt->execute();
$users = $stmt->fetchAll();

$query = "SELECT COUNT(*) FROM benutzer WHERE 1 = 1";
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
$total_users = $stmt->fetchColumn();

$total_pages = ceil($total_users / $limit);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer verwalten</title>
    <link rel="stylesheet" href="../css/sortiment.css">
</head>
<body>

<div class="content">
    <div class="banner">
        <h1>Benutzer verwalten</h1>
    </div>

    <div class="function-container">
        <p class="searchp">Suchen Sie nach einem Benutzer?</p>
        <p class="sort-text">Sortieren </p>
        <div class="dropdown" id="sort-dropdown">
            <img src="../images/sort.png" alt="Sortieren" class="sort-icon">
            <div class="dropdown-content">
                <a href="users.php?sort=ID&sort_order=asc">ID (aufsteigend)</a>
                <a href="users.php?sort=ID&sort_order=desc">ID (absteigend)</a>
                <a href="users.php?sort=benutzername&sort_order=asc">Benutzername (aufsteigend)</a>
                <a href="users.php?sort=benutzername&sort_order=desc">Benutzername (absteigend)</a>
            </div>
        </div>

        <p class="filter-text">Filtern </p>
        <div class="dropdown" id="filter-dropdown" onmouseover="showDropdown('filter-dropdown-content')" onmouseout="hideDropdown('filter-dropdown-content')">
            <img src="../images/filter.png" alt="Filtern" class="filter-icon">
            <div class="dropdown-content" id="filter-dropdown-content">
                <form action="users.php" method="get">
                    <input class="filter-box" type="hidden" name="page" value="1">
                    <label class="filter-label" for="benutzername">Benutzername:</label>
                    <input class="filter-box" type="text" name="filter[benutzername]" id="benutzername" value="<?php echo isset($filter['benutzername']) ? htmlspecialchars($filter['benutzername']) : '' ?>"><br>
                    <label class="filter-label" for="name">Name:</label>
                    <input class="filter-box" type="text" name="filter[name]" id="name" value="<?php echo isset($filter['name']) ? htmlspecialchars($filter['name']) : '' ?>"><br>
                    <label class="filter-label" for="vorname">Vorname:</label>
                    <input class="filter-box" type="text" name="filter[vorname]" id="vorname" value="<?php echo isset($filter['vorname']) ? htmlspecialchars($filter['vorname']) : '' ?>"><br>
                    <label class="filter-label" for="email">E-Mail:</label>
                    <input class="filter-box" type="text" name="filter[email]" id="email" value="<?php echo isset($filter['email']) ? htmlspecialchars($filter['email']) : '' ?>"><br>
                    <button class="filter-button" type="submit">Filtern</button>
                </form>
            </div>
        </div>
    </div>

    <div class="user-container">
        <?php
        $count = 0;
        foreach ($users as $user):
            if ($count % 2 == 0) echo '<div class="row">';
            ?>
            <div class="article column">
                <div class="bookicon"><img src="../images/user.png" alt="Benutzer Icon"></div>
                <div class="benutzerbeschreibung">
                    <p>
                        <?php
                        $userInfo = [];
                        if (!empty($user['benutzername'])) $userInfo[] = htmlspecialchars($user['benutzername']);
                        if (!empty($user['name'])) $userInfo[] = 'Name: ' . htmlspecialchars($user['name']);
                        if (!empty($user['vorname'])) $userInfo[] = 'Vorname: ' . htmlspecialchars($user['vorname']);
                        if (!empty($user['ID'])) $userInfo[] = 'ID: ' . htmlspecialchars($user['ID']);
                        if (!empty($user['email'])) $userInfo[] = 'E-Mail: ' . htmlspecialchars($user['email']);
                        echo implode(', ', $userInfo);
                        ?>
                    </p>
                    <a href="edit_user.php?ID=<?php echo $user['ID'] ?>"><img src="../images/edit.png" alt="Bearbeiten" class="edit-icon"></a>
                    <a href="delete_user.php?ID=<?php echo $user['ID'] ?>" onclick="return confirm('Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?')"><img src="../images/delete.png" alt="Löschen" class="delete-icon"></a>
                </div>
            </div>
            <?php
            $count++;
            if ($count % 2 == 0 || $count == $total_users) echo '</div>';
        endforeach;
        ?>
    </div>

    <br>

    <div class="add-book-container">
        <a href="add_user.php">
        <p class="add-p">User hinzufügen</p><img class="add-img" src="../images/add.png" alt="Neuen Benutzer hinzufügen" class="add-icon"></a>
    </div>

    <div class="pagination">
        <?php if ($page > 1 || $page < $total_pages): ?>
            <?php
            $params = $_GET;
            $params['page'] = $page - 1;
            $prev_url = 'users.php?' . http_build_query($params);
            $params['page'] = $page + 1;
            $next_url = 'users.php?' . http_build_query($params);
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
