<?php
// includes/audit_functions.php - VERSIÓN SIMPLE Y SEGURA

/**
 * Obtiene las actividades recientes del sistema - VERSIÓN SIMPLE
 */
function obtener_actividades_recientes($limite = 5) {
    global $pdo;

    try {
        // CONSULTA MÁS SIMPLE - usa EXACTAMENTE los campos de tu tabla
        $sql = "SELECT 
                    fecha_cambio as fecha_hora,
                    COALESCE(usuario_responsable, 'Sistema') as usuario_nombre,
                    accion,
                    CASE 
                        WHEN campo_modificado IS NOT NULL AND campo_modificado != ''
                        THEN CONCAT(campo_modificado, ': ', 
                                   COALESCE(valor_anterior, ''), 
                                   CASE 
                                       WHEN valor_anterior IS NOT NULL AND valor_nuevo IS NOT NULL 
                                       THEN ' → ' 
                                       ELSE ''
                                   END,
                                   COALESCE(valor_nuevo, ''))
                        ELSE COALESCE(valor_nuevo, valor_anterior, accion)
                    END as detalle
                FROM auditoria_usuario 
                ORDER BY fecha_cambio DESC 
                LIMIT :limite";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear fecha si es necesario
        foreach ($resultados as &$fila) {
            if (!empty($fila['fecha_hora'])) {
                $fila['fecha_hora'] = date('Y-m-d H:i:s', strtotime($fila['fecha_hora']));
            }
        }
        
        return $resultados;

    } catch (PDOException $e) {
        // Para depuración
        echo "<!-- Error en obtener_actividades_recientes: " . htmlspecialchars($e->getMessage()) . " -->";
        error_log("Error obteniendo actividades: " . $e->getMessage());
        return [];
    }
}

/**
 * Función de auditoría SIMPLE - compatible con tu código actual
 */
function registrar_auditoria($accion, $detalle = '', $usuario_id = null) {
    global $pdo;
    
    try {
        // Si no hay usuario, usar 0 (Sistema)
        if ($usuario_id === null) {
            $usuario_id = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 0;
        }
        
        // Determinar responsable
        $responsable = 'Sistema';
        if ($usuario_id > 0) {
            // Intentar obtener nombre
            try {
                $stmt = $pdo->prepare("SELECT nombre, apellido_paterno FROM usuario WHERE id_usuario = ?");
                $stmt->execute([$usuario_id]);
                if ($usuario = $stmt->fetch()) {
                    $responsable = $usuario['nombre'] . ' ' . $usuario['apellido_paterno'];
                }
            } catch (Exception $e) {
                // Si falla, usar ID
                $responsable = "Usuario ID: $usuario_id";
            }
        }
        
        // Insertar en auditoría - usando campos exactos de tu tabla
        $sql = "INSERT INTO auditoria_usuario 
                (id_usuario, accion, campo_modificado, valor_anterior, valor_nuevo, 
                 usuario_responsable, ip_address, user_agent, fecha_cambio) 
                VALUES (:id_usuario, :accion, '', '', :detalle, 
                        :responsable, :ip, :ua, NOW())";
        
        $stmt = $pdo->prepare($sql);
        
        return $stmt->execute([
            ':id_usuario' => $usuario_id,
            ':accion' => $accion,
            ':detalle' => $detalle,
            ':responsable' => $responsable,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido'
        ]);
        
    } catch (Exception $e) {
        error_log("Error en registrar_auditoria: " . $e->getMessage());
        return false;
    }
}

/**
 * Para auditoria_completa.php - obtener TODAS las actividades
 */
function obtener_actividades_completas() {
    global $pdo;
    
    try {
        $sql = "SELECT 
                    fecha_cambio as fecha_hora,
                    COALESCE(usuario_responsable, 'Sistema') as usuario_nombre,
                    accion,
                    CASE 
                        WHEN campo_modificado IS NOT NULL AND campo_modificado != ''
                        THEN CONCAT(campo_modificado, ': ', 
                                   COALESCE(valor_anterior, ''), 
                                   CASE 
                                       WHEN valor_anterior IS NOT NULL AND valor_nuevo IS NOT NULL 
                                       THEN ' → ' 
                                       ELSE ''
                                   END,
                                   COALESCE(valor_nuevo, ''))
                        ELSE COALESCE(valor_nuevo, valor_anterior, accion)
                    END as detalle,
                    ip_address,
                    user_agent
                FROM auditoria_usuario 
                ORDER BY fecha_cambio DESC";
        
        $stmt = $pdo->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear fechas
        foreach ($resultados as &$fila) {
            if (!empty($fila['fecha_hora'])) {
                $fila['fecha_hora'] = date('Y-m-d H:i:s', strtotime($fila['fecha_hora']));
            }
        }
        
        return $resultados;
        
    } catch (Exception $e) {
        error_log("Error en obtener_actividades_completas: " . $e->getMessage());
        return [];
    }
}
?>
