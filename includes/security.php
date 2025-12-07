<?php
// includes/security.php

/**
 * Inicia sesión segura con headers anti-cache
 */
function secure_session_start() {
    // Headers anti-cache (IMPORTANTE)
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Verifica si el usuario está logueado
 * @param int $min_role Rol mínimo requerido (opcional)
 * @return bool|void Retorna true o redirige al login
 */
function check_login($min_role = null) {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        // Registrar intento de acceso no autorizado (opcional)
        error_log("Intento de acceso no autorizado desde IP: " . $_SERVER['REMOTE_ADDR']);
        
        // Redirigir al login
        header("Location: ../login.php?error=session_expired");
        exit();
    }
    
    // Verificar rol mínimo si se especifica
    if ($min_role !== null && isset($_SESSION['id_rol'])) {
        if ($_SESSION['id_rol'] < $min_role) {
            // Usuario no tiene permisos suficientes
            header("Location: ../login.php?error=insufficient_permissions");
            exit();
        }
    }
    
    return true;
}

/**
 * Destruye completamente la sesión
 */

function destroy_session() {
    // Registrar logout antes de destruir
    if (isset($_SESSION['nombre'])) {
        global $pdo;
        
        try {
            $sql = "INSERT INTO auditoria_sistema 
                    (usuario_id, fecha_hora, accion, detalle, ip_address, user_agent) 
                    VALUES (:usuario_id, NOW(), 'Cierre de sesión', 
                    :detalle, :ip, :user_agent)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $_SESSION['id_usuario'] ?? 0,
                ':detalle' => 'Usuario ' . ($_SESSION['nombre'] ?? '') . ' cerró sesión',
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido'
            ]);
        } catch (Exception $e) {
            // Silenciar errores de auditoría para no interrumpir el logout
            if (defined('IS_DEV') && IS_DEV) {
                error_log("Error en auditoría de logout: " . $e->getMessage());
            }
        }
    }
    
    // Destruir sesión PHP
    session_unset();
    session_destroy();
    
    // Eliminar cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), 
            '', 
            time() - 42000,
            $params["path"], 
            $params["domain"],
            $params["secure"], 
            $params["httponly"]
        );
    }
}


/**
 * Redirige según el rol del usuario
 */
function redirect_by_role() {
    if (!isset($_SESSION['id_rol'])) {
        header("Location: login.php");
        exit();
    }
    
    switch ($_SESSION['id_rol']) {
        case 1: header("Location: Admin/Inicio.php"); break;
        case 2: header("Location: Usuario/panelAhorrador.php"); break;
        case 3: header("Location: SuperUsuario/Inicio.php"); break;
        default: header("Location: login.php");
    }
    exit();
}

/**
 * Obtiene el nombre completo del usuario
 */
function get_full_name() {
    if (!isset($_SESSION['nombre'])) return 'Usuario';
    
    $nombre = $_SESSION['nombre'];
    $paterno = $_SESSION['paterno'] ?? '';
    $materno = $_SESSION['materno'] ?? '';
    
    return trim("$nombre $paterno $materno");
}
?>