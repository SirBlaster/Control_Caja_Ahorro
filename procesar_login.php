<?php
// ============ SECCIÓN PHP (AL INICIO) ============
session_start(); // Añade esto al inicio

require_once 'includes/conexion.php'; // Asegúrate de incluir la conexión

// Incluir funciones
if (file_exists('includes/audit_functions.php')) {
    require_once 'includes/audit_functions.php';
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// DEBUG: Ver qué datos llegan
error_log("DEBUG: POST recibido - correo: " . ($_POST['correo'] ?? 'vacío'));

// Si ya está logueado, redirigir según su rol
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    redirectByRole($_SESSION['id_rol']);
    exit();
}

// Procesar el formulario si se envió
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validaciones básicas
    if (empty($correo) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } else {
        // DEBUG: Log de consulta
        error_log("DEBUG: Buscando usuario con correo: $correo");
        
        // CORRECCIÓN: Tabla 'usuario' (minúscula) y campo 'correo_institucional'
        $sql = "SELECT * FROM usuario WHERE correo_institucional = :correo AND habilitado = 1 LIMIT 1";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':correo' => $correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // DEBUG: Ver qué usuario encontramos
            if ($usuario) {
                error_log("DEBUG: Usuario encontrado - ID: " . $usuario['id_usuario'] . ", Rol: " . $usuario['id_rol'] . ", Nombre: " . $usuario['nombre']);
                error_log("DEBUG: Contraseña DB: " . $usuario['contrasena'] . " vs Ingresada: " . $password);
            } else {
                error_log("DEBUG: Usuario NO encontrado o deshabilitado");
            }

            // Validar contraseña
            if ($usuario) {
                // Versión para desarrollo (comparación directa)
                // IMPORTANTE: Si tu contraseña está hasheada, usa password_verify()
                if ($usuario['contrasena'] === $password || password_verify($password, $usuario['contrasena'])) {
                    // ¡Login Exitoso! Guardamos datos en sesión
                    $_SESSION['id_usuario'] = $usuario['id_usuario'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['paterno'] = $usuario['apellido_paterno'] ?? '';
                    $_SESSION['materno'] = $usuario['apellido_materno'] ?? '';
                    $_SESSION['correo'] = $usuario['correo_institucional'];
                    $_SESSION['id_rol'] = (int)$usuario['id_rol']; // Asegurar que sea entero
                    $_SESSION['habilitado'] = $usuario['habilitado'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['login_time'] = time();
                    
                    // DEBUG: Log de sesión
                    error_log("DEBUG: Sesión creada - ID: " . $_SESSION['id_usuario'] . ", Rol: " . $_SESSION['id_rol']);

                    // AUDITORÍA: Registrar inicio de sesión
                    if (function_exists('registrar_auditoria')) {
                        registrar_auditoria(
                            'Inicio de sesión',
                            'Usuario ' . $usuario['nombre'] . ' inició sesión correctamente',
                            $usuario['id_usuario']
                        );
                    }

                    // Redireccionar según el ROL
                    redirectByRole($_SESSION['id_rol']);
                    exit();
                } else {
                    // AUDITORÍA: Login fallido
                    if (function_exists('registrar_auditoria')) {
                        registrar_auditoria(
                            'Intento fallido de login',
                            'Contraseña incorrecta para usuario: ' . $correo
                        );
                    }
                    $error = "Usuario o contraseña incorrectos";
                }
            } else {
                // AUDITORÍA: Login fallido
                if (function_exists('registrar_auditoria')) {
                    registrar_auditoria(
                        'Intento fallido de login',
                        'Usuario no encontrado o deshabilitado: ' . $correo
                    );
                }
                $error = "Usuario o contraseña incorrectos o cuenta deshabilitada";
            }
        } catch (PDOException $e) {
            error_log("ERROR en login: " . $e->getMessage());
            $error = "Error en la base de datos. Intenta más tarde.";
        }
    }
}

// Función para redirigir según rol - CORREGIDA
function redirectByRole($role)
{
    $role = (int)$role; // Asegurar que sea entero
    
    error_log("DEBUG redirectByRole: Redirigiendo rol $role");
    
    switch ($role) {
        case 1: // Admin
            error_log("DEBUG: Redirigiendo a Admin/Inicio.php");
            header("Location: Admin/Inicio.php");
            break;
        case 2: // Ahorrador
            error_log("DEBUG: Redirigiendo a Usuario/panelAhorrador.php");
            header("Location: Usuario/panelAhorrador.php");
            break;
        case 3: // SuperUsuario
            error_log("DEBUG: Redirigiendo a SuperUsuario/Inicio.php");
            header("Location: SuperUsuario/Inicio.php");
            break;
        default:
            error_log("DEBUG: Rol inválido: $role, redirigiendo a login con error");
            header("Location: login.php?error=rol_invalido");
    }
    exit();
}
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
