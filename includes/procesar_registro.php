<?php
// procesar_registro.php
require_once 'init.php'; // Cambiado a init.php para tener todas las funciones

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
        registrar_auditoria(
            'Intento fallido de registro',
            'Validación fallida para: ' . $institucional . ' - Errores: ' . implode(', ', $errores)
        );
        die("<script>alert('$mensaje_error'); window.history.back();</script>");
    }

    // 4. Verificar duplicados
    $checkSql = "SELECT Id_Ahorrador FROM Usuarios WHERE Institucional = ? OR CURP = ? OR RFC = ?";
    $stmtCheck = $pdo->prepare($checkSql);
    $stmtCheck->execute([$institucional, $curp, $rfc]);

    if ($stmtCheck->rowCount() > 0) {
        $usuario_existente = $stmtCheck->fetch();
        // Auditar intento de registro duplicado
        registrar_auditoria(
            'Intento de registro duplicado',
            'Datos duplicados para: ' . $institucional . ' - CURP: ' . $curp . ' - RFC: ' . $rfc . ' - IP: ' . ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0')
        );
        die("<script>alert('El correo institucional, CURP o RFC ya está registrado.'); window.history.back();</script>");
    }

    // 5. Encriptar contraseña (IMPORTANTE para producción)
    // Para desarrollo, mantenemos texto plano. Para producción descomenta:
    // $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $password_hash = $password; // Para desarrollo

    // 6. INSERTAR en la base de datos
    $sql = "INSERT INTO Usuarios 
            (Nombre, Paterno, Materno, Institucional, Personal, RFC, CURP, Telefono, Contrasena, Tarjeta, Id_Rol) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 2)";
    
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
                $nuevo_id // Usamos el ID del nuevo usuario como quien realiza la acción
            );
            
            // Mostrar mensaje de éxito
            echo "<script>
                alert('¡Registro Exitoso!\\nTu cuenta ha sido creada correctamente.\\nID de usuario: $nuevo_id');
                window.location.href = 'login.php';
            </script>";
            
        } else {
            throw new Exception("Error al ejecutar la consulta");
        }
        
    } catch (PDOException $e) {
        // Auditar error en registro
        registrar_auditoria(
            'Error en registro de usuario',
            'Error de base de datos para: ' . $institucional . ' - ' . $e->getMessage()
        );
        
        // Mostrar error
        echo "<script>
            alert('Error al guardar en la base de datos.\\nPor favor, intenta nuevamente.');
            window.history.back();
        </script>";
        error_log("Error en registro: " . $e->getMessage());
        
    } catch (Exception $e) {
        // Auditar error general
        registrar_auditoria(
            'Error en registro de usuario',
            'Error general para: ' . $institucional . ' - ' . $e->getMessage()
        );
        
        echo "<script>
            alert('Ocurrió un error inesperado.\\nPor favor, intenta nuevamente.');
            window.history.back();
        </script>";
        error_log("Error general en registro: " . $e->getMessage());
    }
    
} else {
    // Si no es POST, redirigir al formulario
    header("Location: registro.php");
    exit();
}
?>
