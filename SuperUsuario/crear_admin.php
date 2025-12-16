<?php
// SuperUsuario/crear_admin.php
require_once '../includes/init.php';
secure_session_start();
check_login(3); // Solo SuperUsuario

// Incluir funciones de administrador
require_once '../includes/admin_functions.php';

// Mostrar mensajes si existen
$mensaje = '';
$tipo_mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'];
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Administrador - SETDITSX</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/Super.css">
    
</head>

<body>
    <div class="header d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
        <div class="d-flex align-items-center">
            <img src="../img/NewLogo - 1.png" alt="SETDITSX" width="70" class="me-3" />
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
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='usuarios.php') echo 'active'; ?>"
                        href="./usuarios.php">
                        <i class="bi bi-people-fill me-1"></i>Gestión de Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='parametros.php') echo 'active'; ?>"
                        href="./parametros.php">
                        <i class="bi bi-cash-stack me-1"></i>Gestión de Parámetros
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='auditoria_completa.php') echo 'active'; ?>"
                        href="./auditoria_completa.php">
                        <i class="bi bi-file-earmark-text-fill me-1"></i>Auditoría
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='editar_perfil.php') echo 'active'; ?>"
                        href="./editar_perfil.php">
                        <i class="bi bi-gear-fill me-1"></i>Configuración
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Alertas de mensajes -->
    <?php if ($mensaje): ?>
        <div class="alert-container">
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <i class="bi <?php echo $tipo_mensaje == 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'; ?> me-1"></i>
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <div class="card-form">
        <div class="nav-actions mb-4">
            <a href="../SuperUsuario/inicio.php" class="nav-link">
                <i class="bi bi-arrow-left me-2"></i>Volver al menú principal
            </a>
        </div>

        <div>
            <h2 class="mb-0">Crear Administrador</h2>
        </div>

        <form action="procesar_registro.php" method="POST" id="registroForm" onsubmit="return validarFormulario()">
            <!-- Sección 1: Datos Personales -->
            <div class="form-section">
                <h5 class="section-title">
                    <i></i>Datos Personales
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nombre" class="form-label required-field">Nombre(s)</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" 
                               placeholder="Ej. Juan Carlos" required maxlength="50"
                               onkeyup="validarNombre(this)">
                        <small class="text-muted" id="nombreError"></small>
                    </div>
                    <div class="col-md-4">
                        <label for="paterno" class="form-label required-field">Apellido Paterno</label>
                        <input type="text" id="paterno" name="paterno" class="form-control" 
                               placeholder="Ej. Pérez" required maxlength="50"
                               onkeyup="validarApellido(this)">
                        <small class="text-muted" id="paternoError"></small>
                    </div>
                    <div class="col-md-4">
                        <label for="materno" class="form-label">Apellido Materno</label>
                        <input type="text" id="materno" name="materno" class="form-control" 
                               placeholder="Ej. López" maxlength="50"
                               onkeyup="validarApellido(this)">
                        <small class="text-muted" id="maternoError"></small>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Información de Contacto -->
            <div class="form-section">
                <h5 class="section-title">
                    <i></i>Información de Contacto
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="correo_institucional" class="form-label required-field">Correo Institucional</label>
                        <input type="email" id="correo_institucional" name="correo_institucional" 
                               class="form-control" placeholder="usuario@itsx.edu.mx" required
                               onkeyup="validarCorreoInstitucional(this)">
                        <small class="text-muted">Debe ser un correo válido del dominio ITSX</small>
                        <small class="text-danger" id="correoInstError"></small>
                    </div>
                    <div class="col-md-6">
                        <label for="correo_personal" class="form-label required-field">Correo Personal</label>
                        <input type="email" id="correo_personal" name="correo_personal" 
                               class="form-control" placeholder="usuario@gmail.com" required
                               onkeyup="validarCorreoPersonal(this)">
                        <small class="text-danger" id="correoPersError"></small>
                    </div>
                    <div class="col-md-6">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" class="form-control" 
                               placeholder="Ej. 5512345678" maxlength="15"
                               onkeyup="validarTelefono(this)">
                        <small class="text-muted">Opcional - 10 dígitos mínimo</small>
                        <small class="text-danger" id="telefonoError"></small>
                    </div>
                </div>
            </div>

            <!-- Sección 3: Seguridad -->
            <div class="form-section">
                <h5 class="section-title">
                    <i ></i>Seguridad
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label required-field">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Mínimo 8 caracteres" required minlength="8"
                               onkeyup="validarPassword(this)">
                        <div class="password-requirements">
                            <small>Debe contener al menos:</small>
                            <ul class="mb-0">
                                <li id="reqLongitud">8 caracteres</li>
                                <li id="reqMayuscula">Una letra mayúscula</li>
                                <li id="reqNumero">Un número</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label required-field">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="form-control" placeholder="Repita la contraseña" required
                               onkeyup="validarConfirmacionPassword(this)">
                        <small class="text-danger" id="confirmError"></small>
                    </div>
                </div>
            </div>

            <!-- Sección 4: Términos y Condiciones -->
            <div class="form-section">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terminos" required>
                    <label class="form-check-label" for="terminos">
                        <strong>Acepto los términos y condiciones</strong> del sindicato y autorizo el tratamiento de
                        mis datos personales de acuerdo con la Ley de Protección de Datos Personales.
                    </label>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="row mt-4">
                <div class="col-md-6 mb-2">
                    <a href="../SuperUsuario/usuarios.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-registrar w-100">
                        <i class="bi bi-person-plus me-2"></i>Registrar Administrador
                    </button>
                </div>
            </div>
        </form>

    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="../js/registro_admin.js"></script>
</body>
</html>