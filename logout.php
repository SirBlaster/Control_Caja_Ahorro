<?php
// logout.php
require_once 'includes/init.php';

secure_session_start();

// Registrar logout en auditoría si hay sesión activa
if (isset($_SESSION['id_usuario']) && isset($_SESSION['nombre'])) {
    try {
        global $pdo;
        
        // Preparar el nombre del usuario
        $nombre_completo = $_SESSION['nombre'];
        if (isset($_SESSION['apellido_paterno'])) {
            $nombre_completo .= ' ' . $_SESSION['apellido_paterno'];
        }
        if (isset($_SESSION['apellido_materno'])) {
            $nombre_completo .= ' ' . $_SESSION['apellido_materno'];
        }
        
        // Calcular duración de sesión si existe el tiempo de login
        $duracion = 'Desconocida';
        if (isset($_SESSION['login_time'])) {
            $inicio = strtotime($_SESSION['login_time']);
            $fin = time();
            $duracion_minutos = round(($fin - $inicio) / 60, 1);
            $duracion = $duracion_minutos . ' minutos';
        }
        
        // Insertar directamente en auditoria_usuario
        $sql = "INSERT INTO auditoria_usuario 
                (id_usuario, accion, campo_modificado, valor_anterior, valor_nuevo, 
                 usuario_responsable, ip_address, user_agent, fecha_cambio) 
                VALUES (:id_usuario, 'LOGOUT', 'sesión', 'Activa', 'Cerrada', 
                        :usuario_responsable, :ip_address, :user_agent, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $_SESSION['id_usuario'],
            ':usuario_responsable' => $nombre_completo,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido'
        ]);
        
        // También opcionalmente usar la función registrar_auditoria si existe
        if (function_exists('registrar_auditoria')) {
            registrar_auditoria(
                'LOGOUT',
                'Cierre de sesión - Duración: ' . $duracion,
                $_SESSION['id_usuario']
            );
        }
        
        // Log en el sistema
        error_log("Logout exitoso: " . $nombre_completo . " (ID: " . $_SESSION['id_usuario'] . 
                  ") - Duración: " . $duracion . " - IP: " . $_SERVER['REMOTE_ADDR']);
                  
    } catch (Exception $e) {
        // No interrumpir el logout si hay error en auditoría
        error_log("Error registrando logout (no crítico): " . $e->getMessage());
    }
}

// Destruir sesión
destroy_session();

// Redirigir al login
header("Location: login.php?logout=1");
exit();
?>
