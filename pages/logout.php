<?php
session_start();

// Löschen der Session-Variablen
$_SESSION = array();
// Session beenden
session_destroy();
// Auf Login Seite weiterleiten
header("Location: login.php");
exit;
?>