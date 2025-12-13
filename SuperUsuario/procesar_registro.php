<?php
// SuperUsuario/procesar_registro.php
require_once '../includes/init.php';
secure_session_start();
check_login(3); // Solo SuperUsuario

// Incluir funciones de administrador
require_once '../includes/admin_functions.php';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar datos
    $datos = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'paterno' => trim($_POST['paterno'] ?? ''),
        'materno' => trim($_POST['materno'] ?? ''),
        'correo_institucional' => trim($_POST['correo_institucional'] ?? ''),
        'correo_personal' => trim($_POST['correo_personal'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? ''
    ];
    
    // Validaciones básicas
    if (empty($datos['nombre']) || empty($datos['paterno']) || empty($datos['correo_institucional'])) {
        $_SESSION['mensaje'] = 'Todos los campos obligatorios deben ser completados';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: crear_admin.php');
        exit();
    }
    
    if (!filter_var($datos['correo_institucional'], FILTER_VALIDATE_EMAIL) || 
        !filter_var($datos['correo_personal'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['mensaje'] = 'Correos electrónicos no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: crear_admin.php');
        exit();
    }
    
    // Registrar el administrador
    $resultado = registrar_administrador($datos);
    
    if ($resultado['success']) {
        $_SESSION['mensaje'] = $resultado['message'];
        $_SESSION['tipo_mensaje'] = 'success';
        // Redirigir a la lista de usuarios
        header('Location: usuarios.php');
        exit();
    } else {
        $_SESSION['mensaje'] = $resultado['message'];
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: crear_admin.php');
        exit();
    }
} else {
    // Si no es POST, redirigir
    header('Location: crear_admin.php');
    exit();
}
?>