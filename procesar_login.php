<?php
// ============ SECCIÓN PHP (AL INICIO) ============
require_once 'includes/init.php'; // Este ya incluye TODO

// Headers anti-cache para evitar volver atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Si ya está logueado, redirigir según su rol
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    redirectByRole($_SESSION['id_rol']);
    exit();
}

// Procesar el formulario si se envió
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // NOTA: $pdo YA está disponible desde init.php
    // NO necesitas: require 'includes/conexion.php';

    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    // Validaciones básicas
    if (empty($correo) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } else {
        // Buscar al usuario por su correo institucional
        $sql = "SELECT * FROM Usuarios WHERE Institucional = :correo LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch();

        // Validar contraseña (EN PRODUCCIÓN usa password_verify)
        if ($usuario) {
            // Versión para desarrollo (comparación directa)
            if ($usuario['Contrasena'] == $password) {
                // Versión para producción (descomenta esta):
                // if (password_verify($password, $usuario['Contrasena'])) {

                // ¡Login Exitoso! Guardamos datos en sesión
                $_SESSION['id_usuario'] = $usuario['Id_Ahorrador'];
                $_SESSION['nombre'] = $usuario['Nombre'];
                $_SESSION['paterno'] = $usuario['Paterno'] ?? '';
                $_SESSION['materno'] = $usuario['Materno'] ?? '';
                $_SESSION['correo'] = $usuario['Institucional'];
                $_SESSION['id_rol'] = $usuario['Id_Rol'];
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();

                // AUDITORÍA: Registrar inicio de sesión
                // NOTA: $pdo está disponible globalmente en audit_functions.php
                registrar_auditoria(
                    'Inicio de sesión',
                    'Usuario ' . $usuario['Nombre'] . ' inició sesión correctamente'
                );

                // Redireccionar según el ROL
                redirectByRole($usuario['Id_Rol']);
                exit();
            } else {
                // AUDITORÍA: Login fallido (contraseña incorrecta)
                registrar_auditoria(
                    'Intento fallido de login',
                    'Contraseña incorrecta para usuario: ' . $correo
                );
                $error = "Usuario o contraseña incorrectos";
            }
        } else {
            // AUDITORÍA: Login fallido (usuario no existe)
            registrar_auditoria(
                'Intento fallido de login',
                'Usuario no encontrado: ' . $correo
            );
            $error = "Usuario o contraseña incorrectos";
        }
    }
}

// Función para redirigir según rol
function redirectByRole($role)
{
    switch ($role) {
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
            header("Location: login.php?error=rol_invalido");
    }
    exit();
}
// ============ FIN SECCIÓN PHP ============
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar Sesión - SETDITSX</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">

    <!-- JavaScript para prevenir cache -->
    <script>
        // Prevenir que el navegador guarde en cache
        if (performance.navigation.type === 2) {
            // Si viene del cache (botón atrás), recargar
            location.reload(true);
        }

        window.onpageshow = function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        };
    </script>
</head>

<body>
    <div class="page-header">
        <img src="img/NewLogo - 1.png" alt="logo" class="brand-logo" />
        <div>
            <div style="font-weight:700;color:#153b52;">SETDITSX - Sindicato ITSX</div>
        </div>
    </div>

    <main class="card card-login">
        <h1 class="title">Iniciar Sesión</h1>

        <!-- Formulario (ahora se envía a sí mismo) -->
        <form method="POST" action="">
            <!-- Mostrar errores -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['logout'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    Sesión cerrada exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['session_expired'])): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Tu sesión ha expirado. Por favor, inicia sesión nuevamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <input type="email" name="correo" class="form-control" id="email" placeholder="Correo electrónico"
                    value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>" required />
            </div>

            <div class="mb-3">
                <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña"
                    required />
            </div>

            <button type="submit" class="btn btn-golden">Ingresar</button>
            <a href="registro.php" class="register-link">Registrarme</a>
        </form>
    </main>

    <script src="js/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- Focus en el primer campo -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('email').focus();
        });
    </script>
</body>

</html>
