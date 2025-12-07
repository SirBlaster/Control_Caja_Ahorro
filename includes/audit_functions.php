<?php
// includes/audit_functions.php

/**
 * Registra una acción en el sistema de auditoría
 * 
 * @param string $accion Descripción de la acción realizada
 * @param string $detalle Detalles adicionales (opcional)
 * @param int $usuario_id ID del usuario (si es null, usa el de sesión)
 * @return bool True si se registró correctamente
 */
function registrar_auditoria($accion, $detalle = '', $usuario_id = null)
{
    global $pdo; // Asumiendo que $pdo está disponible desde conexion.php

    try {
        // Si no se especifica usuario, usar el de sesión
        if ($usuario_id === null) {
            $usuario_id = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 0;
        }

        // Preparar la consulta
        $sql = "INSERT INTO auditoria_sistema 
                (usuario_id, fecha_hora, accion, detalle, ip_address, user_agent) 
                VALUES (:usuario_id, NOW(), :accion, :detalle, :ip, :ua)";

        $stmt = $pdo->prepare($sql);

        // Ejecutar con parámetros
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':accion' => $accion,
            ':detalle' => $detalle,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido'
        ]);

    } catch (PDOException $e) {
        // En desarrollo, mostrar error; en producción, solo log
        if (defined('IS_DEV') && IS_DEV) {
            error_log("Error en auditoría: " . $e->getMessage());
        }
        return false;
    }
}

/**
 * Obtiene las actividades recientes del sistema
 * 
 * @param int $limite Cantidad de registros a obtener (por defecto 10)
 * @return array Array con las actividades
 */
function obtener_actividades_recientes($limite = 10)
{
    global $pdo;

    try {
        $sql = "SELECT 
                    a.fecha_hora,
                    CONCAT(u.Nombre, ' ', u.Paterno) as usuario_nombre,
                    a.accion,
                    a.detalle
                FROM auditoria_sistema a
                LEFT JOIN Usuarios u ON a.usuario_id = u.Id_Ahorrador
                ORDER BY a.fecha_hora DESC
                LIMIT :limite";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error obteniendo actividades: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene actividades filtradas por usuario
 */
function obtener_actividades_por_usuario($usuario_id, $limite = 20)
{
    global $pdo;

    try {
        $sql = "SELECT * FROM auditoria_sistema 
                WHERE usuario_id = :usuario_id
                ORDER BY fecha_hora DESC
                LIMIT :limite";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error obteniendo actividades por usuario: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca actividades por rango de fechas
 */
function buscar_actividades_por_fecha($fecha_inicio, $fecha_fin)
{
    global $pdo;

    try {
        $sql = "SELECT 
                    a.*,
                    CONCAT(u.Nombre, ' ', u.Paterno) as usuario_nombre
                FROM auditoria_sistema a
                LEFT JOIN Usuarios u ON a.usuario_id = u.Id_Ahorrador
                WHERE DATE(a.fecha_hora) BETWEEN :fecha_inicio AND :fecha_fin
                ORDER BY a.fecha_hora DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error buscando actividades por fecha: " . $e->getMessage());
        return [];
    }
}
/**
 * Registra la creación de un nuevo usuario
 */
function auditar_registro_usuario($datos_usuario, $exitoso = true, $error = '')
{
    global $pdo;

    try {
        $accion = $exitoso ? 'Registro de nuevo usuario' : 'Error en registro de usuario';

        if ($exitoso) {
            $detalle = sprintf(
                "Nuevo usuario registrado: %s %s %s (ID: %d, Correo: %s, Tel: %s, Rol: %d)",
                $datos_usuario['nombre'],
                $datos_usuario['paterno'],
                $datos_usuario['materno'],
                $datos_usuario['id'],
                $datos_usuario['institucional'],
                $datos_usuario['telefono'],
                $datos_usuario['id_rol']
            );
        } else {
            $detalle = sprintf(
                "Intento fallido de registro: %s %s %s (Correo: %s) - Error: %s",
                $datos_usuario['nombre'] ?? '',
                $datos_usuario['paterno'] ?? '',
                $datos_usuario['materno'] ?? '',
                $datos_usuario['institucional'] ?? '',
                $error
            );
        }

        return registrar_auditoria($accion, $detalle);

    } catch (Exception $e) {
        error_log("Error en auditoría de registro: " . $e->getMessage());
        return false;
    }
}
?>