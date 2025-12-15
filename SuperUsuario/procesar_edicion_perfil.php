<?php
// SuperUsuario/procesar_edicion_perfil.php
require_once '../includes/init.php';
secure_session_start();
check_login(3); // Solo SuperUsuario

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje'] = 'Acceso no permitido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: editar_perfil.php');
    exit();
}

$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;

if ($id_usuario <= 0) {
    $_SESSION['mensaje'] = 'ID de usuario inválido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: editar_perfil.php');
    exit();
}

// Verificar que el usuario solo puede editar su propio perfil
if ($id_usuario !== $_SESSION['id_usuario']) {
    $_SESSION['mensaje'] = 'Solo puede editar su propio perfil';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: editar_perfil.php');
    exit();
}

// Obtener datos actuales para comparar
global $pdo;
$sql_actual = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt_actual = $pdo->prepare($sql_actual);
$stmt_actual->execute([$id_usuario]);
$usuario_actual = $stmt_actual->fetch(PDO::FETCH_ASSOC);

if (!$usuario_actual) {
    $_SESSION['mensaje'] = 'Usuario no encontrado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: editar_perfil.php');
    exit();
}

// Preparar datos según los nombres de campo del formulario
$datos = [
    'nombre' => trim($_POST['nombre'] ?? ''),
    'apellido_paterno' => trim($_POST['paterno'] ?? ''),
    'apellido_materno' => trim($_POST['materno'] ?? ''),
    'correo_personal' => trim($_POST['correo_personal'] ?? ''),
    'correo_institucional' => trim($_POST['correo_institucional'] ?? ''),
    'id_rol' => 3, // Forzar rol SuperUsuario (3)
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
if (empty($datos['nombre']) || empty($datos['apellido_paterno']) || 
    empty($datos['correo_institucional'])) {
    $_SESSION['mensaje'] = 'Todos los campos obligatorios deben ser completados';
    $_SESSION['tipo_mensaje'] = 'danger';
    header("Location: editar_perfil.php");
    exit();
}

// Validar correo institucional único (excepto para este usuario)
$sql_check_email = "SELECT id_usuario FROM usuario WHERE correo_institucional = ? AND id_usuario != ?";
$stmt_check = $pdo->prepare($sql_check_email);
$stmt_check->execute([$datos['correo_institucional'], $id_usuario]);
if ($stmt_check->fetch()) {
    $_SESSION['mensaje'] = 'El correo institucional ya está registrado por otro usuario';
    $_SESSION['tipo_mensaje'] = 'danger';
    header("Location: editar_perfil.php");
    exit();
}

// Iniciar transacción
$pdo->beginTransaction();

try {
    // Identificar cambios para auditoría
    $cambios = [];
    
    foreach ($datos as $campo => $valor_nuevo) {
        $valor_antiguo = $usuario_actual[$campo] ?? '';
        
        // Para contraseña, manejarlo aparte
        if ($campo === 'password') continue;
        
        if ($valor_antiguo != $valor_nuevo) {
            $cambios[] = [
                'campo' => $campo,
                'anterior' => $valor_antiguo,
                'nuevo' => $valor_nuevo
            ];
        }
    }
    
    // Manejar cambio de contraseña
    $password_hash = null;
    if (!empty($_POST['password'])) {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception('Las contraseñas no coinciden');
        }
        
        if (strlen($_POST['password']) < 8) {
            throw new Exception('La contraseña debe tener al menos 8 caracteres');
        }
        
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $cambios[] = [
            'campo' => 'contrasena',
            'anterior' => '[CONTRASEÑA ANTERIOR]',
            'nuevo' => '[CONTRASEÑA NUEVA]'
        ];
    }
    
    // Construir query de actualización
    $set_parts = [];
    $params = [];
    
    foreach ($datos as $campo => $valor) {
        $set_parts[] = "$campo = :$campo";
        $params[":$campo"] = $valor;
    }
    
    // Agregar fecha de actualización
    //$set_parts[] = "fecha_actualizacion = NOW()";
    
    // Agregar contraseña si se cambió
    if ($password_hash) {
        $set_parts[] = "contrasena = :contrasena";
        $params[':contrasena'] = $password_hash;
    }
    
    $set_clause = implode(', ', $set_parts);
    $params[':id_usuario'] = $id_usuario;
    
    $sql_update = "UPDATE usuario SET $set_clause WHERE id_usuario = :id_usuario";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute($params);
    
    // Registrar cambios en auditoría
    foreach ($cambios as $cambio) {
        $sql_audit = "INSERT INTO auditoria_usuario 
                     (id_usuario, accion, campo_modificado, valor_anterior, valor_nuevo, 
                      usuario_responsable, ip_address, user_agent, fecha_cambio)
                     VALUES (:id_usuario, :accion, :campo, :valor_anterior, :valor_nuevo,
                             :responsable, :ip, :ua, NOW())";
        
        $stmt_audit = $pdo->prepare($sql_audit);
        $stmt_audit->execute([
            ':id_usuario' => $id_usuario,
            ':accion' => 'UPDATE_PERFIL',
            ':campo' => $cambio['campo'],
            ':valor_anterior' => $cambio['anterior'],
            ':valor_nuevo' => $cambio['nuevo'],
            ':responsable' => get_user_name(),
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido'
        ]);
    }
    
    $pdo->commit();
    
    $_SESSION['mensaje'] = 'Perfil actualizado correctamente';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header("Location: editar_perfil.php");
exit();
?>