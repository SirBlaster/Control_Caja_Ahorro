<?php
// includes/admin_functions.php
// Gestión de administradores

//Registrar un nuevo administrador
function registrar_administrador($datos) {
    global $pdo;
    
    try {
        // Verificar que no exista el correo institucional
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE correo_institucional = ?");
        $stmt->execute([$datos['correo_institucional']]);
        if ($stmt->rowCount() > 0) {
            return [
                'success' => false,
                'message' => 'El correo institucional ya está registrado'
            ];
        }
        
        // Verificar que las contraseñas coincidan
        if ($datos['password'] !== $datos['confirm_password']) {
            return [
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ];
        }
        
        // Hash de la contraseña
        $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
        
        // Insertar nuevo usuario como administrador 
        $sql = "INSERT INTO usuario (
                    nombre, 
                    apellido_paterno, 
                    apellido_materno, 
                    correo_institucional, 
                    correo_personal, 
                    contrasena, 
                    telefono,
                    id_rol,
                    habilitado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1)";
        
        $stmt = $pdo->prepare($sql);
        
        $telefono = !empty($datos['telefono']) ? $datos['telefono'] : '0000000000';
        
        $stmt->execute([
            $datos['nombre'],
            $datos['paterno'],
            $datos['materno'],
            $datos['correo_institucional'],
            $datos['correo_personal'],
            $password_hash,
            $telefono
        ]);
        
        $id_nuevo_usuario = $pdo->lastInsertId();
        
        // Registrar en auditoría
        require_once 'audit_functions.php';
        registrar_auditoria(
            'CREAR_ADMIN',
            "Nuevo administrador: " . $datos['nombre'] . " " . $datos['paterno'],
            $_SESSION['id_usuario'] ?? null
        );
        
        return [
            'success' => true,
            'message' => 'Administrador registrado exitosamente',
            'id_usuario' => $id_nuevo_usuario
        ];
        
    } catch (PDOException $e) {
        error_log("Error al registrar administrador: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en el sistema. Por favor, intente nuevamente.'
        ];
    }
}

//Obtener información de un administrador por ID

function obtener_admin_por_id($id_usuario) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT u.*, r.rol 
            FROM usuario u 
            LEFT JOIN rol r ON u.id_rol = r.id_rol 
            WHERE u.id_usuario = ? AND u.id_rol = 1
        ");
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener admin: " . $e->getMessage());
        return null;
    }
}

//Actualizar información de administrador
function actualizar_admin($id_usuario, $datos) {
    global $pdo;
    
    try {
        // Verificar que el usuario exista y sea administrador
        $admin = obtener_admin_por_id($id_usuario);
        if (!$admin) {
            return [
                'success' => false,
                'message' => 'Administrador no encontrado'
            ];
        }
        
        // Preparar campos a actualizar
        $campos = [];
        $valores = [];
        
        // Campos básicos
        $campos[] = "nombre = ?";
        $valores[] = $datos['nombre'];
        
        $campos[] = "apellido_paterno = ?";
        $valores[] = $datos['paterno'];
        
        $campos[] = "apellido_materno = ?";
        $valores[] = $datos['materno'];
        
        $campos[] = "correo_personal = ?";
        $valores[] = $datos['correo_personal'];
        
        // Si cambió el correo institucional, verificar que no exista
        if ($datos['correo_institucional'] != $admin['correo_institucional']) {
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE correo_institucional = ? AND id_usuario != ?");
            $stmt->execute([$datos['correo_institucional'], $id_usuario]);
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'El correo institucional ya está en uso por otro usuario'
                ];
            }
            $campos[] = "correo_institucional = ?";
            $valores[] = $datos['correo_institucional'];
        }
        
        // Si se proporcionó contraseña, actualizarla
        if (!empty($datos['password'])) {
            if ($datos['password'] !== $datos['confirm_password']) {
                return [
                    'success' => false,
                    'message' => 'Las contraseñas no coinciden'
                ];
            }
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            $campos[] = "contrasena = ?";
            $valores[] = $password_hash;
        }
        
        $valores[] = $id_usuario; // Para el WHERE
        
        $sql = "UPDATE usuario SET " . implode(", ", $campos) . " WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
        
        // Registrar en auditoría
        require_once 'audit_functions.php';
        registrar_auditoria(
            'ACTUALIZAR_ADMIN',
            "Administrador actualizado: " . $datos['nombre'] . " " . $datos['paterno'],
            $_SESSION['id_usuario'] ?? null
        );
        
        return [
            'success' => true,
            'message' => 'Administrador actualizado exitosamente'
        ];
        
    } catch (PDOException $e) {
        error_log("Error al actualizar admin: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en el sistema. Por favor, intente nuevamente.'
        ];
    }
}
?>