<?php
include 'fonts.php';
?>
<link rel="stylesheet" href="../css/navbar.css">
<body>
<div class="nav-container">
    <nav class="navbar">
        <a href="../index.php">
            <img class="navbar-logo" src="../images/logo.png" alt="Logo">
        </a>
        <ul class="navbar-elements">
            <li><a class="navbar-element" href="../pages/sortiment.php">Bücher</a></li>
            <li><a class="navbar-element" href="../pages/contact.php">Kontakt</a></li>
            <li>
                <form class="navbar-search" action="../pages/sortiment.php" method="GET">
                    <input class="search-bar" type="text" name="filter[autor]" placeholder="Suchen">
                    <button class="search-button" type="submit">Suchen</button>
                </form>
            </li>
            <li><a class="navbar-element" href="../pages/login.php">Anmelden</a></li>
        </ul>
    </nav>
</div>
</body>
</html>
