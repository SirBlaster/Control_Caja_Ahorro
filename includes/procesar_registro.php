<?php
// ================== PROCESAR REGISTRO ==================
require_once __DIR__ . '/init.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../registro.php");
    exit();
}

// ================== 1. RECIBIR DATOS ==================
$nombre            = trim($_POST['nombre'] ?? '');
$paterno           = trim($_POST['paterno'] ?? '');
$materno           = trim($_POST['materno'] ?? '');
$curp              = trim($_POST['curp'] ?? '');
$rfc               = trim($_POST['rfc'] ?? '');
$telefono          = trim($_POST['telefono'] ?? '');
$correo_personal   = trim($_POST['correo_personal'] ?? '');
$institucional     = trim($_POST['correo_institucional'] ?? '');
$tarjeta           = isset($_POST['dato_bancario']) && $_POST['dato_bancario'] !== ''
                     ? trim($_POST['dato_bancario'])
                     : null;

$password          = $_POST['password'] ?? '';
$confirm           = $_POST['confirm_password'] ?? '';

// ================== 2. VALIDACIONES ==================
$errores = [];

// Campos obligatorios
$campos_obligatorios = [
    'Nombre' => $nombre,
    'Apellido paterno' => $paterno,
    'Apellido materno' => $materno,
    'CURP' => $curp,
    'RFC' => $rfc,
    'Teléfono' => $telefono,
    'Correo institucional' => $institucional,
    'Contraseña' => $password
];

foreach ($campos_obligatorios as $campo => $valor) {
    if (empty($valor)) {
        $errores[] = "El campo $campo es obligatorio";
    }
}

// Validaciones específicas
if (!filter_var($institucional, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El correo institucional no es válido";
}

if ($correo_personal && !filter_var($correo_personal, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El correo personal no es válido";
}

if ($password !== $confirm) {
    $errores[] = "Las contraseñas no coinciden";
}

if (strlen($password) < 6) {
    $errores[] = "La contraseña debe tener al menos 6 caracteres";
}

if ($tarjeta && strlen($tarjeta) != 16) {
    $errores[] = "La tarjeta debe tener exactamente 16 dígitos";
}

// Si hay errores
if (!empty($errores)) {
    if (function_exists('registrar_auditoria')) {
        registrar_auditoria(
            'Intento fallido de registro',
            'Errores: ' . implode(', ', $errores) . ' | Correo: ' . $institucional
        );
    }

    $mensaje = implode("\\n", $errores);
    die("<script>alert('$mensaje'); window.history.back();</script>");
}

// ================== 3. VERIFICAR DUPLICADOS ==================
$sqlCheck = "SELECT id_usuario 
             FROM usuario 
             WHERE correo_institucional = ? 
                OR curp = ? 
                OR rfc = ? 
             LIMIT 1";

$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->execute([$institucional, $curp, $rfc]);

if ($stmtCheck->rowCount() > 0) {
    if (function_exists('registrar_auditoria')) {
        registrar_auditoria(
            'Intento de registro duplicado',
            "Correo: $institucional | CURP: $curp | RFC: $rfc"
        );
    }

    die("<script>
        alert('El correo institucional, CURP o RFC ya están registrados.');
        window.history.back();
    </script>");
}

// ================== 4. ENCRIPTAR CONTRASEÑA ==================
// DESARROLLO:
// $password_hash = $password;

// PRODUCCIÓN (RECOMENDADO):
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// ================== 5. INSERTAR USUARIO ==================
$sqlInsert = "INSERT INTO usuario
    (nombre, apellido_paterno, apellido_materno, correo_institucional,
     correo_personal, rfc, curp, telefono, contrasena, tarjeta, id_rol, habilitado)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 2, 1)";

$stmt = $pdo->prepare($sqlInsert);

try {
    $stmt->execute([
        $nombre,
        $paterno,
        $materno,
        $institucional,
        $correo_personal,
        $rfc,
        $curp,
        $telefono,
        $password_hash,
        $tarjeta
    ]);

    $nuevo_id = $pdo->lastInsertId();

    if (function_exists('registrar_auditoria')) {
        registrar_auditoria(
            'Registro de nuevo usuario',
            "Usuario creado ID: $nuevo_id | Correo: $institucional | CURP: $curp",
            $nuevo_id
        );
    }

    echo "<script>
        alert('¡Registro exitoso!\\nAhora puedes iniciar sesión.');
        window.location.href = '../login.php';
    </script>";

} catch (PDOException $e) {

    if (function_exists('registrar_auditoria')) {
        registrar_auditoria(
            'Error en registro de usuario',
            'DB ERROR: ' . $e->getMessage() . ' | Correo: ' . $institucional
        );
    }

    error_log("ERROR REGISTRO: " . $e->getMessage());

    echo "<script>
        alert('Error al guardar los datos.\\nIntenta nuevamente.');
        window.history.back();
    </script>";
}