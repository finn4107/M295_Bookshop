<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include '../includes/connection.php';
include '../includes/navbar.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'kid';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';
$filter = isset($_GET['filter']) ? $_GET['filter'] : [];

$query = "SELECT * FROM kunden WHERE 1 = 1";
$conditions = [];
$filterValues = [];
if (!empty($filter)) {
    foreach ($filter as $key => $value) {
        if (!empty($value)) {
            if (in_array($key, ['vorname', 'name', 'email'])) {
                $conditions[] = "$key LIKE :$key";
                $filterValues[":$key"] = "%$value%";
            } elseif (in_array($key, ['geschlecht', 'kontaktpermail'])) {
                $conditions[] = "$key = :$key";
                $filterValues[":$key"] = $value;
            }
        }
    }
}
if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}
$query .= " ORDER BY $sort $sort_order LIMIT $limit OFFSET $offset";

$stmt = $db->prepare($query);
foreach ($filterValues as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$customers = $stmt->fetchAll();

$query = "SELECT COUNT(*) FROM kunden WHERE 1 = 1";
if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}
$stmt = $db->prepare($query);
foreach ($filterValues as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$total_customers = $stmt->fetchColumn();

$total_pages = ceil($total_customers / $limit);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kunden verwalten</title>
    <link rel="stylesheet" href="../css/sortiment.css">
</head>
<body>

<div class="content">
    <div class="banner">
        <h1>Kunden verwalten</h1>
    </div>

    <div class="function-container">
        <p class="searchp">Suchen Sie nach einem Kunden?</p>
        <p class="sort-text">Sortieren </p>
        <div class="dropdown" id="sort-dropdown">
            <img src="../images/sort.png" alt="Sortieren" class="sort-icon">
            <div class="dropdown-content">
                <a href="customers.php?sort=kid&sort_order=asc">ID (aufsteigend)</a>
                <a href="customers.php?sort=kid&sort_order=desc">ID (absteigend)</a>
                <a href="customers.php?sort=vorname&sort_order=asc">Vorname (aufsteigend)</a>
                <a href="customers.php?sort=vorname&sort_order=desc">Vorname (absteigend)</a>
                <a href="customers.php?sort=name&sort_order=asc">Name (aufsteigend)</a>
                <a href="customers.php?sort=name&sort_order=desc">Name (absteigend)</a>
            </div>
        </div>

        <p class="filter-text">Filtern </p>
        <div class="dropdown" id="filter-dropdown" onmouseover="showDropdown('filter-dropdown-content')" onmouseout="hideDropdown('filter-dropdown-content')">
            <img src="../images/filter.png" alt="Filtern" class="filter-icon">
            <div class="dropdown-content" id="filter-dropdown-content">
                <form action="customers.php" method="get">
                    <input class="filter-box" type="hidden" name="page" value="1">
                    <label class="filter-label" for="vorname">Vorname:</label>
                    <input class="filter-box" type="text" name="filter[vorname]" id="vorname" value="<?php echo isset($filter['vorname']) ? htmlspecialchars($filter['vorname']) : '' ?>"><br>
                    <label class="filter-label" for="name">Name:</label>
                    <input class="filter-box" type="text" name="filter[name]" id="name" value="<?php echo isset($filter['name']) ? htmlspecialchars($filter['name']) : '' ?>"><br>
                    <label class="filter-label" for="email">E-Mail:</label>
                    <input class="filter-box" type="text" name="filter[email]" id="email" value="<?php echo isset($filter['email']) ? htmlspecialchars($filter['email']) : '' ?>"><br>
                    <label class="filter-label" for="geschlecht">Geschlecht:</label>
                    <select class="filter-box" name="filter[geschlecht]" id="geschlecht">
                        <option value="">- Geschlecht -</option>
                        <option value="M" <?php echo isset($filter['geschlecht']) && $filter['geschlecht'] == 'M' ? 'selected' : '' ?>>Männlich</option>
                        <option value="W" <?php echo isset($filter['geschlecht']) && $filter['geschlecht'] == 'W' ? 'selected' : '' ?>>Weiblich</option>
                    </select><br>
                    <label class="filter-label" for="kontaktpermail">Kontakt per Mail:</label>
                    <select class="filter-box" name="filter[kontaktpermail]" id="kontaktpermail">
                        <option value="">- Kontakt per Mail -</option>
                        <option value="1" <?php echo isset($filter['kontaktpermail']) && $filter['kontaktpermail'] == '1' ? 'selected' : '' ?>>Ja</option>
                        <option value="0" <?php echo isset($filter['kontaktpermail']) && $filter['kontaktpermail'] == '0' ? 'selected' : '' ?>>Nein</option>
                    </select><br>
                    <button class="filter-button" type="submit">Filtern</button>
                </form>
            </div>
        </div>
    </div>

    <div class="customer-container">
        <?php
        $count = 0;
        foreach ($customers as $customer):
            if ($count % 2 == 0) echo '<div class="row">';
            ?>
            <div class="article column">
                <div class="bookicon"><img src="../images/customer.png" alt="Kunden Icon"></div>
                <div class="kundenbeschreibung">
                    <p>
                        <?php
                        $customerInfo = [];
                        if (!empty($customer['vorname'])) $customerInfo[] = htmlspecialchars($customer['vorname']);
                        if (!empty($customer['name'])) $customerInfo[] = 'Name: ' . htmlspecialchars($customer['name']);
                        if (!empty($customer['email'])) $customerInfo[] = 'E-Mail: ' . htmlspecialchars($customer['email']);
                        if (!empty($customer['kid'])) $customerInfo[] = 'ID: ' . htmlspecialchars($customer['kid']);
                        if (!empty($customer['geschlecht'])) $customerInfo[] = 'Geschlecht: ' . htmlspecialchars($customer['geschlecht']);
                        if (isset($customer['kontaktpermail'])) $customerInfo[] = 'Kontakt per Mail: ' . ($customer['kontaktpermail'] ? 'Ja' : 'Nein');
                        echo implode(', ', $customerInfo);
                        ?>
                    </p>
                    <a href="edit_customer.php?kid=<?php echo $customer['kid'] ?>"><img src="../images/edit.png" alt="Bearbeiten" class="edit-icon"></a>
                    <a href="delete_customer.php?kid=<?php echo $customer['kid'] ?>" onclick="return confirm('Sind Sie sicher, dass Sie diesen Kunden löschen möchten?')"><img src="../images/delete.png" alt="Löschen" class="delete-icon"></a>
                </div>
            </div>
            <?php
            $count++;
            if ($count % 2 == 0 || $count == count($customers)) echo '</div>';
        endforeach;
        ?>
    </div>

    <br>

    <div class="add-book-container">
        <p class="add-p">Kunde hinzufügen</p>
        <a href="add_customer.php"><img class="add-img" src="../images/add.png" alt="Neuen Kunden hinzufügen" class="add-icon"></a>
    </div>

    <div class="pagination">
        <?php if ($page > 1 || $page < $total_pages): ?>
            <?php
            $params = $_GET;
            $params['page'] = $page - 1;
            $prev_url = 'customers.php?' . http_build_query($params);
            $params['page'] = $page + 1;
            $next_url = 'customers.php?' . http_build_query($params);
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
