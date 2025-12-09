<?php
// includes/admin_user_handlers.php

/**
 * Obtiene todos los usuarios excepto superadministradores (rol 3)
 */
function obtener_usuarios_admin()
{
    global $pdo; // Cambié $conn por $pdo (según tu conexión)

    $sql = "SELECT u.id_usuario as id, 
                   u.nombre, 
                   u.apellido_paterno as paterno, 
                   u.apellido_materno as materno, 
                   CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as nombre_completo,
                   u.correo_institucional as email,
                   u.correo_personal as email_personal,
                   u.telefono,
                   u.id_rol as rol_id,
                   r.rol as nombre_rol,
                   u.rfc,
                   u.curp,
                   u.tarjeta,
                   u.habilitado
            FROM usuario u
            INNER JOIN rol r ON u.id_rol = r.id_rol
            WHERE u.id_rol IN (1, 2)  -- Solo administradores (1) y ahorradores (2)
            ORDER BY u.apellido_paterno, u.apellido_materno, u.nombre";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener usuarios: " . $e->getMessage());
        return [];
    }
}

/**
 * Cambia el estado de un usuario (habilitar/deshabilitar)
 */
function cambiar_estado_usuario($id_usuario)
{
    global $pdo; // Cambié $conn por $pdo

    try {
        // Obtener estado actual
        $sql_estado = "SELECT habilitado FROM usuario WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql_estado);
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        // Cambiar estado (1 = habilitado, 0 = deshabilitado)
        $nuevo_estado = $usuario['habilitado'] == 1 ? 0 : 1;
        $accion = $nuevo_estado == 1 ? 'habilitado' : 'deshabilitado';

        $sql_update = "UPDATE usuario SET habilitado = ? WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute([$nuevo_estado, $id_usuario]);

        // Registrar actividad
        if (function_exists('registrar_auditoria')) {
            $detalle = "Usuario ID: $id_usuario fue $accion";
            registrar_auditoria("Usuario $accion", $detalle, $_SESSION['id_usuario'] ?? null);
        }

        return [
            'success' => true,
            'message' => "Usuario $accion correctamente",
            'nuevo_estado' => $nuevo_estado
        ];

    } catch (PDOException $e) {
        error_log("Error al cambiar estado de usuario: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error en la base de datos'];
    }
}

/**
 * Cambia el rol de un usuario (Administrador ↔ Ahorrador)
 */
function cambiar_rol_usuario($id_usuario)
{
    global $pdo; // Cambié $conn por $pdo

    try {
        // Obtener rol actual
        $sql_rol = "SELECT id_rol FROM usuario WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql_rol);
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        // Cambiar entre rol 1 (Administrador) y 2 (Ahorrador)
        $nuevo_rol = $usuario['id_rol'] == 1 ? 2 : 1;
        $nuevo_rol_nombre = $nuevo_rol == 1 ? 'Administrador' : 'Ahorrador';
        $rol_anterior = $usuario['id_rol'] == 1 ? 'Administrador' : 'Ahorrador';

        $sql_update = "UPDATE usuario SET id_rol = ? WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute([$nuevo_rol, $id_usuario]);

        // Registrar actividad
        if (function_exists('registrar_auditoria')) {
            $detalle = "Usuario ID: $id_usuario cambió de $rol_anterior a $nuevo_rol_nombre";
            registrar_auditoria("Rol cambiado", $detalle, $_SESSION['id_usuario'] ?? null);
        }

        return [
            'success' => true,
            'message' => "Rol cambiado a $nuevo_rol_nombre correctamente",
            'nuevo_rol' => $nuevo_rol,
            'nuevo_rol_nombre' => $nuevo_rol_nombre
        ];

    } catch (PDOException $e) {
        error_log("Error al cambiar rol de usuario: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error en la base de datos'];
    }
}

/**
 * Obtiene los datos de un usuario por ID
 */
function obtener_usuario_por_id($id_usuario)
{
    global $pdo; // Cambié $conn por $pdo

    $sql = "SELECT u.*, r.rol as nombre_rol 
            FROM usuario u 
            INNER JOIN rol r ON u.id_rol = r.id_rol 
            WHERE u.id_usuario = ?";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener usuario: " . $e->getMessage());
        return null;
    }
}

/**
 * Actualiza los datos de un usuario
 */
function actualizar_usuario($id_usuario, $datos)
{
    global $pdo; // Cambié $conn por $pdo

    try {
        $sql = "UPDATE usuario SET 
                nombre = :nombre,
                apellido_paterno = :paterno,
                apellido_materno = :materno,
                correo_institucional = :email,
                correo_personal = :email_personal,
                telefono = :telefono,
                rfc = :rfc,
                curp = :curp,
                tarjeta = :tarjeta,
                id_rol = :rol_id,
                habilitado = :habilitado
                WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':paterno' => $datos['paterno'],
            ':materno' => $datos['materno'],
            ':email' => $datos['email'],
            ':email_personal' => $datos['email_personal'],
            ':telefono' => $datos['telefono'],
            ':rfc' => $datos['rfc'],
            ':curp' => $datos['curp'],
            ':tarjeta' => $datos['tarjeta'],
            ':rol_id' => $datos['rol_id'],
            ':habilitado' => $datos['habilitado'],
            ':id' => $id_usuario
        ]);

        // Registrar actividad
        if (function_exists('registrar_auditoria')) {
            $detalle = "Datos modificados para usuario ID: $id_usuario";
            registrar_auditoria("Usuario actualizado", $detalle, $_SESSION['id_usuario'] ?? null);
        }

        return ['success' => true, 'message' => 'Usuario actualizado correctamente'];

    } catch (PDOException $e) {
        error_log("Error al actualizar usuario: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al actualizar usuario: ' . $e->getMessage()];
    }
}
?>