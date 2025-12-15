<?php
// includes/parametros_functions.php 

//Obtiene todos los parámetros del sistema

function obtener_parametros_sistema() {
    global $pdo;
    
    try {
        $sql = "SELECT 
                    nombre_director,
                    periodo,
                    nombre_enc_personal,
                    tasa_interes_general,
                    rendimiento_anual_ahorros,
                    correo_soporte,
                    fecha_actualizacion,
                    usuario_actualizacion
                FROM datos_sistema 
                WHERE id_datos = 1 
                LIMIT 1";
        
        $stmt = $pdo->query($sql);
        
        if ($stmt && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
    } catch (PDOException $e) {
        error_log("Error obteniendo parámetros: " . $e->getMessage());
    }
    
    return [
        'nombre_director' => 'Administrativo en Cargo',
        'periodo' => '2025-2026',
        'nombre_enc_personal' => 'Administrativo en Cargo',
        'tasa_interes_general' => 30.00,
        'rendimiento_anual_ahorros' => 5.00,
        'correo_soporte' => 'soporte@itsx.com',
        'fecha_actualizacion' => null,
        'usuario_actualizacion' => null
    ];
}

//Actualiza los parámetros del sistema

function actualizar_parametros_sistema($datos, $usuario) {
    global $pdo;
    
    try {
        // Obtener valores actuales para comparar
        $parametros_actuales = obtener_parametros_sistema();
        
        // Actualizar datos_sistema
        $sql = "UPDATE datos_sistema SET
                    nombre_director = :nombre_director,
                    periodo = :periodo,
                    nombre_enc_personal = :nombre_enc_personal,
                    tasa_interes_general = :tasa_general,
                    rendimiento_anual_ahorros = :rendimiento,
                    correo_soporte = :correo_soporte,
                    usuario_actualizacion = :usuario,
                    fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id_datos = 1";
        
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            ':nombre_director' => $datos['nombre_director'],
            ':periodo' => $datos['periodo'],
            ':nombre_enc_personal' => $datos['nombre_enc_personal'],
            ':tasa_general' => $datos['tasa_general'],
            ':rendimiento' => $datos['rendimiento'],
            ':correo_soporte' => $datos['correo_soporte'],
            ':usuario' => $usuario
        ]);
        
        // Si no se actualizó (tabla vacía), insertar
        if ($stmt->rowCount() === 0) {
            $sql_insert = "INSERT INTO datos_sistema 
                (nombre_director, periodo, nombre_enc_personal, 
                 tasa_interes_general, rendimiento_anual_ahorros, correo_soporte,
                 usuario_actualizacion)
                VALUES (:nombre_director, :periodo, :nombre_enc_personal,
                        :tasa_general, :rendimiento, :correo_soporte, :usuario)";
            
            $stmt_insert = $pdo->prepare($sql_insert);
            $resultado = $stmt_insert->execute([
                ':nombre_director' => $datos['nombre_director'],
                ':periodo' => $datos['periodo'],
                ':nombre_enc_personal' => $datos['nombre_enc_personal'],
                ':tasa_general' => $datos['tasa_general'],
                ':rendimiento' => $datos['rendimiento'],
                ':correo_soporte' => $datos['correo_soporte'],
                ':usuario' => $usuario
            ]);
        }
        
        if ($resultado) {
            // Obtener ID del usuario actual
            $usuario_id = obtener_id_usuario_actual();
            
            // Registrar cambios si hay diferencias
            if ($parametros_actuales['tasa_interes_general'] != $datos['tasa_general']) {
                registrar_auditoria_parametro(
                    $usuario_id,
                    'tasa_interes_general',
                    $parametros_actuales['tasa_interes_general'],
                    $datos['tasa_general'],
                    $usuario
                );
            }
            
            if ($parametros_actuales['rendimiento_anual_ahorros'] != $datos['rendimiento']) {
                registrar_auditoria_parametro(
                    $usuario_id,
                    'rendimiento_anual_ahorros',
                    $parametros_actuales['rendimiento_anual_ahorros'],
                    $datos['rendimiento'],
                    $usuario
                );
            }
        }
        
        return $resultado;
        
    } catch (PDOException $e) {
        error_log("Error actualizando parámetros: " . $e->getMessage());
        return false;
    }
}

//Obtiene el ID del usuario actualmente logueado

function obtener_id_usuario_actual() {
    if (isset($_SESSION['id_usuario'])) {
        return $_SESSION['id_usuario'];
    }
    return null; // Devolver null es seguro para la FK
}

//Registrar auditoría de parámetros

function registrar_auditoria_parametro($usuario_id, $parametro, $valor_anterior, $valor_nuevo, $usuario_nombre) {
    global $pdo;
    
    try {
        $sql = "INSERT INTO auditoria_usuario 
                (id_usuario, accion, campo_modificado, valor_anterior, valor_nuevo, 
                 usuario_responsable, fecha_cambio)
                VALUES (:id_usuario, 'UPDATE', :parametro, 
                        CONCAT(:valor_anterior, '%'), 
                        CONCAT(:valor_nuevo, '%'), 
                        :usuario_nombre, NOW())";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => $usuario_id,
            ':parametro' => $parametro,
            ':valor_anterior' => $valor_anterior,
            ':valor_nuevo' => $valor_nuevo,
            ':usuario_nombre' => $usuario_nombre
        ]);
        
    } catch (PDOException $e) {
        error_log("Error en auditoría: " . $e->getMessage());
        return false;
    }
}
?>