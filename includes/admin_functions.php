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
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 2, 1)";
        
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
            WHERE u.id_usuario = ? AND u.id_rol = 2
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
function actualizar_ahorrador(array $datos): bool
{
    global $pdo;

    $sql = "
        UPDATE usuario SET
            nombre = :nombre,
            apellido_paterno = :paterno,
            apellido_materno = :materno,
            correo_personal = :correo_personal,
            correo_institucional = :correo_institucional,
            telefono = :telefono,
            rfc = :rfc,
            curp = :curp,
            habilitado = :habilitado
    ";

    // Si viene contraseña, agregarla
    if (isset($datos['password_hash'])) {
        $sql .= ", contrasena = :password_hash";
    }

    $sql .= "
        WHERE id_usuario = :id_usuario
          AND id_rol = 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':nombre', $datos['nombre']);
    $stmt->bindParam(':paterno', $datos['paterno']);
    $stmt->bindParam(':materno', $datos['materno']);
    $stmt->bindParam(':correo_personal', $datos['correo_personal']);
    $stmt->bindParam(':correo_institucional', $datos['correo_institucional']);
    $stmt->bindParam(':telefono', $datos['telefono']);
    $stmt->bindParam(':rfc', $datos['rfc']);
    $stmt->bindParam(':curp', $datos['curp']);
    $stmt->bindParam(':habilitado', $datos['habilitado'], PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);

    if (isset($datos['password_hash'])) {
        $stmt->bindParam(':password_hash', $datos['password_hash']);
    }

    $stmt->execute();

    return $stmt->rowCount() > 0;
}

function obtener_solicitudes($pdo, $pagina = 1, $registros_por_pagina = 10) {
    $offset = ($pagina - 1) * $registros_por_pagina;

    // Contar total de solicitudes
    $total_stmt = $pdo->query("
        SELECT COUNT(*) AS total FROM (
            SELECT id_solicitud_ahorro AS id FROM solicitud_ahorro
            UNION ALL
            SELECT id_solicitud_prestamo AS id FROM solicitud_prestamo
        ) AS todas
    ");
    $total_result = $total_stmt->fetch(PDO::FETCH_ASSOC);
    $total_solicitudes = $total_result['total'];
    $total_paginas = ceil($total_solicitudes / $registros_por_pagina);

    // Obtener solicitudes
    $stmt = $pdo->prepare("
        SELECT s.id_solicitud_ahorro AS id,
               CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS nombre_completo,
               u.rfc,
               'Ahorro' AS tipo,
               s.monto_solicitado AS monto,
               e.estado
        FROM solicitud_ahorro s
        JOIN usuario u ON s.id_usuario = u.id_usuario
        JOIN estado e ON s.id_estado = e.id_estado

        UNION ALL

        SELECT s.id_solicitud_prestamo AS id,
               CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS nombre_completo,
               u.rfc,
               'Préstamo' AS tipo,
               s.monto_solicitado AS monto,
               e.estado
        FROM solicitud_prestamo s
        JOIN usuario u ON s.id_usuario = u.id_usuario
        JOIN estado e ON s.id_estado = e.id_estado

        ORDER BY id DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $registros_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'solicitudes' => $solicitudes,
        'total_paginas' => $total_paginas
    ];
}

/**
 * Función para darle clase al estado
 */
function estado_class($estado) {
    switch(strtolower($estado)){
        case 'pendiente': return 'status-pending';
        case 'aprobado': return 'status-approved';
        case 'rechazado': return 'status-rejected';
        case 'pagado': return 'status-paid';
        case 'cancelado': return 'status-cancelled';
        default: return '';
    }
}

/**
 * Obtener el total de solicitudes pendientes (ahorro y préstamo)
 */
function total_pendientes($pdo) {
    // Contar solicitudes de ahorro pendientes
    $stmt_ahorro = $pdo->prepare("SELECT COUNT(*) AS total FROM solicitud_ahorro WHERE id_estado = (SELECT id_estado FROM estado WHERE LOWER(estado) = 'pendiente')");
    $stmt_ahorro->execute();
    $total_ahorro = $stmt_ahorro->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar solicitudes de préstamo pendientes
    $stmt_prestamo = $pdo->prepare("SELECT COUNT(*) AS total FROM solicitud_prestamo WHERE id_estado = (SELECT id_estado FROM estado WHERE LOWER(estado) = 'pendiente')");
    $stmt_prestamo->execute();
    $total_prestamo = $stmt_prestamo->fetch(PDO::FETCH_ASSOC)['total'];

    return [
        'ahorro' => (int)$total_ahorro,
        'prestamo' => (int)$total_prestamo,
        'total' => (int)$total_ahorro + (int)$total_prestamo
    ];
}

?>