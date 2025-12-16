<?php
require_once __DIR__ . '/init.php';

secure_session_start();
check_login();

if (!isset($_SESSION['id_rol'])) {
    header("Location: ../login.php");
    exit();
}

switch ($_SESSION['id_rol']) {
    case 1: // Ahorrador
        header("Location: ../Usuario/panelAhorrador.php");
        break;

    case 2: // Admin
        header("Location: ../Admin/Inicio.php");
        break;

    case 3: // SuperUsuario
        header("Location: ../SuperUsuario/Inicio.php");
        break;

    default:
        header("Location: ../login.php");
}

exit();