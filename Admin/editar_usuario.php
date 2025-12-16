<?php
require_once '../includes/init.php';
secure_session_start();
check_login(2);

// Obtener el ID del usuario a editar
$id_usuario = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_usuario <= 0) {
    $_SESSION['mensaje'] = 'ID de usuario inválido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: usuarios.php');
    exit();
}

// Obtener datos del usuario usando la función CORRECTA
$usuario = obtener_usuario_completo($id_usuario); // Cambiado

if (!$usuario) {
    $_SESSION['mensaje'] = 'Usuario no encontrado.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: usuarios.php');
    exit();
}

// Manejar mensajes de sesión
$mensaje = '';
$tipo_mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'];
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}

global $pdo;
$stmt = $pdo->query("SELECT id_rol, rol FROM rol WHERE id_rol != 3 ORDER BY id_rol");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Usuario - SETDITSX</title>
    <link rel="stylesheet" href="../css/registrar.css">
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <!-- HEADER -->
    <div class="header d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
        <div class="d-flex align-items-center">
            <img src="../img/logoChico.png" alt="SETDITSX" width="70" class="me-3" />
            <h4 class="mb-0">SETDITSX - Sindicato ITSX</h4>
        </div>

        <div class="user-info d-flex align-items-center">
            <i class="bi bi-person-square user-icon me-2"></i>

            <div class="user-details me-3">
                <p class="user-name mb-0"><?php echo htmlspecialchars(get_user_name()); ?></p>
                <small class="text-muted"><?php echo htmlspecialchars(get_user_role_text()); ?></small>
            </div>

            <form action="../logout.php" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-logout" onclick="return confirm('¿Deseas cerrar sesión?')">
                    <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <!-- NAVBAR DE ADMINISTRADOR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='inicio.php') echo 'active'; ?>"
                        href="./inicio.php">
                        <i class="bi bi-house-door-fill me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='ahorros.php') echo 'active'; ?>"
                        href="./gestion_prestamos.php">
                        <i class="bi bi-cash-stack me-1"></i>Prestamos y Ahorros
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='usuarios.php') echo 'active'; ?>"
                        href="./gestion_ahorradores.php">
                        <i class="bi bi-people-fill me-1"></i>Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='reportes.php') echo 'active'; ?>"
                        href="./reportes.php">
                        <i class="bi bi-file-earmark-text-fill me-1"></i>Reportes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='configuracion.php') echo 'active'; ?>"
                        href="./configuracion.php">
                        <i class="bi bi-gear-fill me-1"></i>Configuración
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <div class="card card-form container mt-4 p-4 shadow-sm" style="max-width: 800px;">
        <div class="nav-actions">
            <a href="./gestion_ahorradores.php" class="btn btn-secondary mb-4">
                <i class="bi bi-arrow-left"></i> Volver a Gestión de Usuarios
            </a>
        </div>

        <h2 class="text-center mb-4">Edición de Usuario</h2>

        <!-- Mostrar mensajes -->
        <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
            <i
                class="bi <?php echo $tipo_mensaje == 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'; ?> me-1"></i>
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Información del usuario -->
        <div class="card bg-light mb-4">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Información del Usuario</h6>
                <p class="mb-1"><strong>ID:</strong> <?php echo htmlspecialchars($usuario['id_usuario']); ?></p>
                <p class="mb-1"><strong>Rol Actual:</strong>
                    <span class="badge <?php echo $usuario['id_rol'] == 1 ? 'bg-primary' : 'bg-success'; ?>">
                        <?php echo htmlspecialchars($usuario['rol']); ?>
                    </span>
                </p>
                <p class="mb-1"><strong>Estado:</strong>
                    <?php echo $usuario['habilitado'] == 1 ? 
                        '<span class="text-success"><i class="bi bi-check-circle"></i> Habilitado</span>' : 
                        '<span class="text-danger"><i class="bi bi-x-circle"></i> Deshabilitado</span>'; ?>
                </p>
                <p class="mb-0"><strong>Última actualización:</strong>
                    <?php echo !empty($usuario['fecha_actualizacion']) ? 
                        date('d/m/Y H:i', strtotime($usuario['fecha_actualizacion'])) : 
                        'No disponible'; ?>
                </p>
            </div>
        </div>

        <form action="./procesar_edicion_ahorrador.php" method="POST" id="formEditar">
            <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">

            <h5 class="section-title border-bottom pb-2 mb-3">Datos Personales</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre(s) *</label>
                    <input type="text" name="nombre" class="form-control"
                        value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido Paterno *</label>
                    <input type="text" name="paterno" class="form-control"
                        value="<?php echo htmlspecialchars($usuario['apellido_paterno']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido Materno *</label>
                    <input type="text" name="materno" class="form-control"
                        value="<?php echo htmlspecialchars($usuario['apellido_materno']); ?>" required>
                </div>
            </div>

            <h5 class="section-title border-bottom pb-2 mb-3">Información de Contacto</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Correo Personal *</label>
                    <input type="email" name="correo_personal" class="form-control"
                        value="<?php echo htmlspecialchars($usuario['correo_personal'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo Institucional *</label>
                    <input type="email" name="correo_institucional" class="form-control"
                        value="<?php echo htmlspecialchars($usuario['correo_institucional'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                        value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>"
                        placeholder="Número celular (10 dígitos)" maxlength="15">
                </div>
                <div class="col-md-6">
                    <label class="form-label">RFC</label>
                    <input type="text" name="rfc" class="form-control"
                        value="<?php echo htmlspecialchars($usuario['rfc'] ?? ''); ?>" placeholder="RFC (13 caracteres)"
                        maxlength="13">
                </div>
                <div class="col-md-6">
                    <label class="form-label">CURP</label>
                    <input type="text" name="curp" class="form-control"
                        value="<?php echo htmlspecialchars($usuario['curp'] ?? ''); ?>"
                        placeholder="CURP (18 caracteres)" maxlength="18">
                </div>
            </div>

            <h5 class="section-title border-bottom pb-2 mb-3">Configuración de Cuenta</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Rol del Usuario *</label>
                    <select class="form-select" name="id_rol" disabled>
                        <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo $rol['id_rol']; ?>"
                            <?php echo $usuario['id_rol'] == $rol['id_rol'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($rol['rol']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">
                        <i class="bi bi-lock-fill me-1"></i>
                        El rol del usuario no puede modificarse desde esta sección
                    </small>
                    <!-- Valor oculto para que no se pierda al enviar el formulario -->
                    <input type="hidden" name="id_rol" value="<?php echo $usuario['id_rol']; ?>">

                </div>
                <div class="col-md-6">
                    <label class="form-label">Estado de la Cuenta *</label>
                    <select class="form-select" name="habilitado" required>
                        <option value="1" <?php echo $usuario['habilitado'] == 1 ? 'selected' : ''; ?>>Habilitado
                        </option>
                        <option value="0" <?php echo $usuario['habilitado'] == 0 ? 'selected' : ''; ?>>Deshabilitado
                        </option>
                    </select>
                </div>
            </div>

            <h5 class="section-title border-bottom pb-2 mb-3">Cambiar Contraseña (Opcional)</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nueva Contraseña</label>
                    <input type="password" name="password" class="form-control"
                        placeholder="Dejar vacío para no cambiar" minlength="8">
                    <small class="text-muted">Mínimo 8 caracteres. Dejar vacío si no desea cambiarla.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input type="password" name="confirm_password" class="form-control"
                        placeholder="Confirmar nueva contraseña">
                </div>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong>Nota:</strong> Todos los cambios serán auditados automáticamente en el sistema.
                <br><small class="text-muted">* Campos obligatorios</small>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-6 mb-2">
                    <a href="./gestion_ahorradores.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100"
                        style="background-color: #d18819; border: none;">
                        <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="../js/editar_usuario.js"></script>
</body>

</html>