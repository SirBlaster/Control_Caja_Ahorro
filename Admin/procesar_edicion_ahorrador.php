<?php
require_once '../includes/init.php';
require_once '../includes/admin_functions.php';

secure_session_start();
check_login(2); // Admin

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gestion_ahorradores.php');
    exit;
}

// ==================
// 1. VALIDAR ID
// ==================
$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;

if ($id_usuario <= 0) {
    $_SESSION['mensaje'] = 'ID de usuario inválido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: gestion_ahorradores.php');
    exit;
}

// ==================
// 2. DATOS BÁSICOS
// ==================
$datos = [
    'id_usuario'           => $id_usuario,
    'nombre'               => trim($_POST['nombre'] ?? ''),
    'paterno'              => trim($_POST['paterno'] ?? ''),
    'materno'              => trim($_POST['materno'] ?? ''),
    'correo_personal'      => trim($_POST['correo_personal'] ?? ''),
    'correo_institucional' => trim($_POST['correo_institucional'] ?? ''),
    'telefono'             => trim($_POST['telefono'] ?? ''),
    'rfc'                  => trim($_POST['rfc'] ?? ''),
    'curp'                 => trim($_POST['curp'] ?? ''),
    'habilitado'           => (int)($_POST['habilitado'] ?? 1)
];

// ==================
// 3. VALIDACIONES
// ==================
if (
    $datos['nombre'] === '' ||
    $datos['paterno'] === '' ||
    $datos['correo_institucional'] === ''
) {
    $_SESSION['mensaje'] = 'Todos los campos obligatorios deben completarse';
    $_SESSION['tipo_mensaje'] = 'danger';
    header("Location: editar_usuario.php?id=$id_usuario");
    exit;
}

if (
    !filter_var($datos['correo_personal'], FILTER_VALIDATE_EMAIL) ||
    !filter_var($datos['correo_institucional'], FILTER_VALIDATE_EMAIL)
) {
    $_SESSION['mensaje'] = 'Correos electrónicos no válidos';
    $_SESSION['tipo_mensaje'] = 'danger';
    header("Location: editar_usuario.php?id=$id_usuario");
    exit;
}

// ==================
// 4. CONTRASEÑA (OPCIONAL)
// ==================
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if ($password !== '') {
    if (strlen($password) < 8) {
        $_SESSION['mensaje'] = 'La contraseña debe tener al menos 8 caracteres';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: editar_usuario.php?id=$id_usuario");
        exit;
    }

    if ($password !== $confirm) {
        $_SESSION['mensaje'] = 'Las contraseñas no coinciden';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: editar_usuario.php?id=$id_usuario");
        exit;
    }

    // Guardar hash
    $datos['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
}

// ==================
// 5. ACTUALIZAR
// ==================
try {
    $resultado = actualizar_ahorrador($datos);

    if ($resultado) {
        $_SESSION['mensaje'] = 'Datos del ahorrador actualizados correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'No se realizaron cambios';
        $_SESSION['tipo_mensaje'] = 'warning';
    }

} catch (Exception $e) {
    $_SESSION['mensaje'] = 'Error en base de datos';
    $_SESSION['tipo_mensaje'] = 'danger';
}

// ==================
// 6. REDIRECCIÓN
// ==================
header('Location: gestion_ahorradores.php');
exit;