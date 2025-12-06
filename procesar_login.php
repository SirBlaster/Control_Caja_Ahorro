<?php
// procesar_login.php
session_start();
require 'includes/conexion.php'; // Traemos la conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['Institucional'];
    $password = $_POST['password'];

    // Buscar al usuario por su correo institucional
    $sql = "SELECT * FROM Usuarios WHERE Institucional = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch();

    // Validar contraseña (NOTA: Para producción usa password_verify, aquí usaremos comparación simple por ahora)
    if ($usuario && $usuario['Contrasena'] == $password) {
        
        // ¡Login Exitoso! Guardamos datos en sesión
        $_SESSION['id_usuario'] = $usuario['Id_Ahorrador'];
        $_SESSION['nombre'] = $usuario['Nombre'];
        $_SESSION['id_rol'] = $usuario['Id_Rol'];

        // Redireccionar según el ROL (1=Admin, 2=Ahorrador, 3=Super)
        switch ($usuario['Id_Rol']) {
            case 1: // Admin
                header("Location: Admin/Inicio.php");
                break;
            case 2: // Ahorrador (Usuario normal)
                header("Location: Usuario/panelAhorrador.php");
                break;
            case 3: // SuperUsuario
                header("Location: SuperUsuario/Inicio.php");
                break;
            default:
                echo "Rol no reconocido.";
        }
        exit();
    } else {
        // Error de login
        header("Location: login.php?error=CredencialesIncorrectas");
        exit();
    }
}
?>