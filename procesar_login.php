<?php
// procesar_login.php

// 1. INCLUIR FUNCIONES Y CONEXIÓN (Lo que hizo tu amigo)
require_once 'includes/init.php'; 

// Headers de seguridad básicos
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. RECIBIR Y LIMPIAR DATOS
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    // 3. VALIDACIÓN BÁSICA
    if (empty($correo) || empty($password)) {
        header("Location: login.php?error=Todos los campos son obligatorios");
        exit();
    }

    // 4. CONSULTA A LA BASE DE DATOS
    // Usamos $pdo que viene incluido en 'init.php'
    $sql = "SELECT * FROM Usuarios WHERE Institucional = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch();

    // 5. VERIFICACIÓN DE CONTRASEÑA
    // (Asegúrate si las contraseñas están en texto plano o encriptadas. Aquí asumo texto plano como en tus ejemplos)
    if ($usuario && $usuario['Contrasena'] == $password) {
        
        // --- ¡ÉXITO! ---
        
        // A. Iniciamos la sesión (si no la inició init.php)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // B. Guardamos variables de sesión
        $_SESSION['id_usuario'] = $usuario['Id_Ahorrador'];
        $_SESSION['nombre'] = $usuario['Nombre'];
        $_SESSION['paterno'] = $usuario['Paterno'] ?? '';
        $_SESSION['materno'] = $usuario['Materno'] ?? '';
        $_SESSION['correo'] = $usuario['Institucional'];
        $_SESSION['id_rol'] = $usuario['Id_Rol'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        // C. Auditoría (Usando la función de tu amigo si existe)
        if (function_exists('registrar_auditoria')) {
            registrar_auditoria(
                'Inicio de sesión',
                'Usuario ' . $usuario['Nombre'] . ' ingresó al sistema'
            );
        }

        // D. REDIRECCIÓN SEGÚN ROL
        switch ($usuario['Id_Rol']) {
            case 1: // Admin
                header("Location: Admin/Inicio.php");
                break;
            case 2: // Ahorrador
                header("Location: Usuario/panelAhorrador.php");
                break;
            case 3: // SuperUsuario
                header("Location: SuperUsuario/Inicio.php");
                break;
            default:
                // Rol desconocido
                session_destroy();
                header("Location: login.php?error=Tu usuario no tiene un rol válido");
        }
        exit();

    } else {
        // --- FALLO ---
        
        // Auditoría de error
        if (function_exists('registrar_auditoria')) {
            registrar_auditoria(
                'Login Fallido',
                'Credenciales incorrectas para: ' . $correo
            );
        }

        // Redirigir al login con mensaje de error
        header("Location: login.php?error=Usuario o contraseña incorrectos");
        exit();
    }
} else {
    // Si intentan entrar directo a este archivo sin formulario
    header("Location: login.php");
    exit();
}
?>