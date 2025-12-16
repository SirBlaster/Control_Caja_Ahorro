<?php
// includes/init.php

// Iniciar sesión PRIMERO (antes de cualquier output)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a la base de datos
require_once __DIR__ . '/conexion.php';

// Incluir funciones de auditoría (necesita $pdo de conexion.php)
require_once __DIR__ . '/audit_functions.php';

// Incluir funciones de seguridad (necesita sesión iniciada)
require_once __DIR__ . '/security.php';

// Incluir funciones de usuario
require_once __DIR__ . '/user_functions.php';

require_once __DIR__ . '/admin_user_handlers.php';

require_once __DIR__ . '/admin_functions.php';
require_once __DIR__ . '/audit_functions.php';
require_once __DIR__ . '/parametros_functions.php';
require_once __DIR__ . '/reports_functions.php';

// Configuraciones
date_default_timezone_set('America/Mexico_City');

// Manejo de errores (define IS_DEV en algún lugar o ajusta)
define('IS_DEV', true); // Cambia a false en producción

if (IS_DEV) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}