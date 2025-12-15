<?php
// ================== INICIO PHP ==================
session_start();

// Incluir init.php (MISMA carpeta includes)
require_once 'init.php';

// Evitar cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Si ya está logueado, redirigir según rol
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    redirectByRole($_SESSION['id_rol']);
    exit();
}

$error = '';

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($correo) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } else {

        $sql = "SELECT * 
                FROM usuario 
                WHERE correo_institucional = :correo 
                AND habilitado = 1 
                LIMIT 1";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':correo' => $correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && (
                $usuario['contrasena'] === $password || 
                password_verify($password, $usuario['contrasena'])
            )) {

                // ===== LOGIN EXITOSO =====
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre']     = $usuario['nombre'];
                $_SESSION['paterno']    = $usuario['apellido_paterno'] ?? '';
                $_SESSION['materno']    = $usuario['apellido_materno'] ?? '';
                $_SESSION['correo']     = $usuario['correo_institucional'];
                $_SESSION['id_rol']     = (int)$usuario['id_rol'];
                $_SESSION['habilitado'] = $usuario['habilitado'];
                $_SESSION['logged_in']  = true;
                $_SESSION['login_time'] = time();

                // Redirigir según rol
                redirectByRole($_SESSION['id_rol']);
                exit();

            } else {
                $error = "Usuario o contraseña incorrectos";
            }

        } catch (PDOException $e) {
            error_log("ERROR LOGIN: " . $e->getMessage());
            $error = "Error en la base de datos. Intenta más tarde.";
        }
    }
}

// ================== FUNCIÓN REDIRECCIÓN ==================
function redirectByRole($role)
{
    switch ((int)$role) {
        case 1: // Ahorrador
            header("Location: ../Usuario/panelAhorrador.php");
            break;

        case 2: // Admin
            header("Location: ../Admin/Inicio.php");
            break;

        case 3: // SuperUsuario
            header("Location: ../SuperUsuario/Inicio.php");
            break;

        default:
            header("Location: ../login.php?error=rol_invalido");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - SETDITSX</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilo.css">

    <script>
    if (performance.navigation.type === 2) {
        location.reload(true);
    }
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };
    </script>
</head>

<body>
    <div class="page-header">
        <img src="../img/NewLogo - 1.png" alt="logo" class="brand-logo">
        <div>
            <div style="font-weight:700;color:#153b52;">SETDITSX - Sindicato ITSX</div>
        </div>
    </div>

    <main class="card card-login">
        <h1 class="title">Iniciar Sesión</h1>

        <form method="POST">
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['logout'])): ?>
            <div class="alert alert-info alert-dismissible fade show">
                Sesión cerrada exitosamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['session_expired'])): ?>
            <div class="alert alert-warning alert-dismissible fade show">
                Tu sesión ha expirado. Inicia sesión nuevamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <input type="email" name="correo" class="form-control" placeholder="Correo electrónico"
                    value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>

            <button type="submit" class="btn btn-golden">Ingresar</button>
            <a href="../registro.php" class="register-link">Registrarme</a>
        </form>
    </main>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('input[name="correo"]').focus();
    });
    </script>
</body>

</html>