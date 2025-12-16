<?php
// Activar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// SuperUsuario/editar_perfil.php - VERSIÓN CORREGIDA
require_once '../includes/init.php';
secure_session_start();
check_login(3);

// Debug: Verificar que estamos aquí
echo "<!-- Debug 1: Pasó los includes -->";

// Obtener datos del superusuario logueado
$id_usuario = $_SESSION['id_usuario'] ?? 0;

if ($id_usuario <= 0) {
    echo "<!-- Debug 2: ID de usuario inválido -->";
    $_SESSION['mensaje'] = 'Usuario no identificado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: inicio.php');
    exit();
}

echo "<!-- Debug 3: ID de usuario es $id_usuario -->";

// Obtener datos del usuario usando la función CORRECTA
$usuario = obtener_superusuario_completo($id_usuario);

if (!$usuario) {
    echo "<!-- Debug 4: Usuario no encontrado -->";
    $_SESSION['mensaje'] = 'Usuario no encontrado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: inicio.php');
    exit();
}

echo "<!-- Debug 5: Usuario obtenido correctamente -->";

// Manejar mensajes de sesión
$mensaje = '';
$tipo_mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'];
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}

// Obtener lista de roles (en este caso solo mostrará SuperUsuario ya que no puede cambiar su rol)
global $pdo;
$stmt = $pdo->query("SELECT id_rol, rol FROM rol WHERE id_rol = 3 ORDER BY id_rol");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Mi Perfil - SETDITSX</title>
    <link rel="stylesheet" href="../css/Super.css">
    <link rel="stylesheet" href="../css/registrar.css">
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light header">
        <div class="container-fluid">
            <a class="navbar-brand" href="../SuperUsuario/inicio.php">
                <img src="../img/NewLogo - 1.png" width="50" height="50" class="d-inline-block align-items-center"
                    alt=""> SETDITSX
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Panel Principal
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../SuperUsuario/editar_perfil.php">Editar Perfil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../SuperUsuario/usuarios.php">Gestionar Usuarios</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../SuperUsuario/parametros.php">Modificar Parámetros</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../SuperUsuario/auditoria_completa.php">Auditoría</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Apartados (Administrador)
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../Admin/gestion_ahorradores.php">Gestionar
                                    Ahorradores</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../Admin/gestion_prestamos.php">Gestionar Préstamos</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../Admin/reportes.php">Reportes</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Apartados (Ahorrador)
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../Usuario/registrahorro.php">Solicitar Ahorro</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../Usuario/solicitud_prestamo.php">Solicitar préstamo</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../Usuario/movimientos.php">Ver movimientos</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../Usuario/mis_solicitudes.php">Mis solicitudes</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../Usuario/Estado_Prestamo.php">Estado de mi préstamo</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../Usuario/historial_completo.php">Historial completo</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="user-details text-end d-none d-md-block">
                    <div class="d-flex align-items-center gap-2">
                        <p class="user-name mb-0 fw-bold"><?php echo htmlspecialchars(get_user_name()); ?></p>
                        <?php if ($_SESSION['id_rol'] == 3): // Solo para Super Usuario ?>
                            <a href="../SuperUsuario/editar_perfil.php" class="btn btn-link btn-sm p-0"
                                title="Editar perfil">
                                <i class="bi bi-pencil-square text-primary"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted"><?php echo htmlspecialchars(get_user_role_text()); ?></small>
                </div>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="card card-form container mt-4 p-4 shadow-sm" style="max-width: 800px;">
        <div class="nav-actions">
            <a href="inicio.php" class="nav-link">
                <i class="bi bi-arrow-left"></i> Volver al Panel Principal
            </a>
        </div>

        <h2 class="text-center mb-4">Editar Mi Perfil</h2>

        <!-- Mostrar mensajes -->
        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <i class="bi <?php echo $tipo_mensaje == 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'; ?> me-1"></i>
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Información del usuario -->
        <div class="card bg-light mb-4">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Información de Mi Cuenta</h6>
                <p class="mb-1"><strong>ID:</strong> <?php echo htmlspecialchars($usuario['id_usuario']); ?></p>
                <p class="mb-1"><strong>Rol:</strong> 
                    <span class="badge bg-danger">
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

        <form action="procesar_edicion_perfil.php" method="POST" id="formEditar">
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
                    <select class="form-select" name="id_rol" required disabled>
                        <option value="3" selected>Super Usuario</option>
                    </select>
                    <input type="hidden" name="id_rol" value="3">
                    <small class="text-muted">El rol de SuperUsuario no puede ser modificado</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estado de la Cuenta *</label>
                    <select class="form-select" name="habilitado" required>
                        <option value="1" <?php echo $usuario['habilitado'] == 1 ? 'selected' : ''; ?>>Habilitado</option>
                        <option value="0" <?php echo $usuario['habilitado'] == 0 ? 'selected' : ''; ?>>Deshabilitado</option>
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
                    <a href="inicio.php" class="btn btn-outline-secondary w-100">
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