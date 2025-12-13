<?php
// SuperUsuario/procesar_edicion.php
require_once '../includes/init.php';
secure_session_start();
check_login(3);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje'] = 'Acceso no permitido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: usuarios.php');
    exit();
}

$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;

if ($id_usuario <= 0) {
    $_SESSION['mensaje'] = 'ID de usuario inválido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: usuarios.php');
    exit();
}

// Preparar datos según los nombres de campo del formulario
$datos = [
    'nombre' => trim($_POST['nombre'] ?? ''),
    'paterno' => trim($_POST['paterno'] ?? ''),
    'materno' => trim($_POST['materno'] ?? ''),
    'correo_personal' => trim($_POST['correo_personal'] ?? ''),
    'correo_institucional' => trim($_POST['correo_institucional'] ?? ''),
    'id_rol' => isset($_POST['id_rol']) ? (int)$_POST['id_rol'] : 0,
    'habilitado' => isset($_POST['habilitado']) ? (int)$_POST['habilitado'] : 1
];

// Campos opcionales
if (isset($_POST['telefono'])) {
    $datos['telefono'] = trim($_POST['telefono']);
}
if (isset($_POST['rfc']) && !empty($_POST['rfc'])) {
    $datos['rfc'] = trim($_POST['rfc']);
}
if (isset($_POST['curp']) && !empty($_POST['curp'])) {
    $datos['curp'] = trim($_POST['curp']);
}

// Validar datos requeridos
if (empty($datos['nombre']) || empty($datos['paterno']) || 
    empty($datos['correo_institucional']) || $datos['id_rol'] == 0) {
    $_SESSION['mensaje'] = 'Todos los campos obligatorios deben ser completados';
    $_SESSION['tipo_mensaje'] = 'danger';
    header("Location: editar_usuario.php?id=$id_usuario");
    exit();
}

// Usar la función CORRECTA para actualizar
$resultado = actualizar_usuario_general($id_usuario, $datos);

if ($resultado['success']) {
    $_SESSION['mensaje'] = $resultado['message'];
    $_SESSION['tipo_mensaje'] = 'success';
} else {
    $_SESSION['mensaje'] = $resultado['message'];
    $_SESSION['tipo_mensaje'] = 'danger';
}

header("Location: editar_usuario.php?id=$id_usuario");
exit();
?>