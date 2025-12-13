<?php
// procesar_registro.php
require_once 'includes/init.php'; // Cambiado a init.php para tener todas las funciones

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recibir y limpiar datos
    $nombre = trim($_POST['nombre'] ?? '');
    $paterno = trim($_POST['paterno'] ?? '');
    $materno = trim($_POST['materno'] ?? '');
    $curp = trim($_POST['curp'] ?? '');
    $rfc = trim($_POST['rfc'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo_personal = trim($_POST['correo_personal'] ?? '');
    $institucional = trim($_POST['correo_institucional'] ?? '');
    
    // Tarjeta (opcional)
    $tarjeta = isset($_POST['dato_bancario']) ? trim($_POST['dato_bancario']) : null;
    
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // 2. Validaciones básicas
    $errores = [];
    
    // Campos obligatorios
    $campos_obligatorios = [
        'nombre' => $nombre,
        'paterno' => $paterno, 
        'materno' => $materno,
        'curp' => $curp,
        'rfc' => $rfc,
        'telefono' => $telefono,
        'correo_institucional' => $institucional,
        'password' => $password
    ];
    
    foreach ($campos_obligatorios as $campo => $valor) {
        if (empty($valor)) {
            $errores[] = "El campo " . str_replace('_', ' ', $campo) . " es obligatorio";
        }
    }
    
    // Validaciones específicas
    if ($password !== $confirm) {
        $errores[] = "Las contraseñas no coinciden";
    }
    
    if (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    if ($tarjeta && strlen($tarjeta) != 16) {
        $errores[] = "La tarjeta debe tener 16 dígitos";
    }
    
    if (!filter_var($institucional, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo institucional no es válido";
    }
    
    if ($correo_personal && !filter_var($correo_personal, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo personal no es válido";
    }
    
    // 3. Si hay errores, mostrar y terminar
    if (!empty($errores)) {
        $mensaje_error = implode("\\n", $errores);
        // Auditar intento fallido de registro
        if (function_exists('registrar_auditoria')) {
            registrar_auditoria(
                'Intento fallido de registro',
                'Validación fallida para: ' . $institucional . ' - Errores: ' . implode(', ', $errores)
            );
        }
        die("<script>alert('$mensaje_error'); window.history.back();</script>");
    }

    // 4. Verificar duplicados - CORRECCIÓN: Tabla 'usuario' y campos nuevos
    $checkSql = "SELECT id_usuario FROM usuario WHERE correo_institucional = ? OR CURP = ? OR RFC = ?";
    $stmtCheck = $pdo->prepare($checkSql);
    $stmtCheck->execute([$institucional, $curp, $rfc]);

    if ($stmtCheck->rowCount() > 0) {
        $usuario_existente = $stmtCheck->fetch();
        // Auditar intento de registro duplicado
        if (function_exists('registrar_auditoria')) {
            registrar_auditoria(
                'Intento de registro duplicado',
                'Datos duplicados para: ' . $institucional . ' - CURP: ' . $curp . ' - RFC: ' . $rfc . ' - IP: ' . ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0')
            );
        }
        die("<script>alert('El correo institucional, CURP o RFC ya está registrado.'); window.history.back();</script>");
    }

    // 5. Encriptar contraseña
    $password_hash = $password; // Para desarrollo - texto plano
    // Para producción: $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 6. INSERTAR en la base de datos - CORRECCIÓN: Campos nuevos
    $sql = "INSERT INTO usuario 
            (nombre, apellido_paterno, apellido_materno, correo_institucional, correo_personal, rfc, curp, telefono, contrasena, tarjeta, id_rol, habilitado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 2, 1)";
    
    $stmt = $pdo->prepare($sql);
    
    try {
        $resultado = $stmt->execute([
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

        if ($resultado) {
            // Obtener el ID del nuevo usuario
            $nuevo_id = $pdo->lastInsertId();
            
            // AUDITORÍA: Registrar creación de usuario exitosa
            if (function_exists('registrar_auditoria')) {
                registrar_auditoria(
                    'Registro de nuevo usuario',
                    sprintf(
                        "Nuevo usuario registrado: %s %s %s (ID: %d, Correo: %s, Tel: %s, CURP: %s)",
                        $nombre,
                        $paterno,
                        $materno,
                        $nuevo_id,
                        $institucional,
                        $telefono,
                        $curp
                    ),
                    $nuevo_id
                );
            }
            
            // Mostrar mensaje de éxito
            echo "<script>
                alert('¡Registro Exitoso!\\nTu cuenta ha sido creada correctamente.');
                window.location.href = 'login.php';
            </script>";
            
        } else {
            throw new Exception("Error al ejecutar la consulta");
        }
        
    } catch (PDOException $e) {
        // Auditar error en registro
        if (function_exists('registrar_auditoria')) {
            registrar_auditoria(
                'Error en registro de usuario',
                'Error de base de datos para: ' . $institucional . ' - ' . $e->getMessage()
            );
        }
        
        echo "<script>
            alert('Error al guardar en la base de datos.\\nPor favor, intenta nuevamente.');
            window.history.back();
        </script>";
        error_log("Error en registro: " . $e->getMessage());
        
    } catch (Exception $e) {
        if (function_exists('registrar_auditoria')) {
            registrar_auditoria(
                'Error en registro de usuario',
                'Error general para: ' . $institucional . ' - ' . $e->getMessage()
            );
        }
        
        echo "<script>
            alert('Ocurrió un error inesperado.\\nPor favor, intenta nuevamente.');
            window.history.back();
        </script>";
        error_log("Error general en registro: " . $e->getMessage());
    }
    
} else {
    header("Location: registro.php");
    exit();
}
?>
