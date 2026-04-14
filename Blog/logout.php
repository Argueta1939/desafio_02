<?php
require_once 'config/database.php';

// Destruir sesión
$_SESSION = array();
session_destroy();

// Redirigir al login
header("Location: login.php");
exit();
?>