<?php
// logout.php
require_once 'includes/init.php';

secure_session_start();

// Registrar logout (opcional)
if (isset($_SESSION['nombre'])) {
    error_log("Logout: " . $_SESSION['nombre'] . " desde IP: " . $_SERVER['REMOTE_ADDR']);
}

// Destruir sesión
destroy_session();

// Redirigir al login con mensaje
header("Location: login.php?logout=1");
exit();
?>
