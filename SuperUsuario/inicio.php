<?php
// SuperUsuario/Inicio.php
require_once '../includes/init.php';
secure_session_start();
check_login(3);

// Obtener actividades recientes
$actividades = obtener_actividades_recientes(5);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="../css/Super.css">
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css ">
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
    <div class="card card-form">
        <div>
            <h2 class="mb-0">Panel de SuperUsuario</h2>
        </div>
        <div class="card-body p-4">
            <!-- Gestión de Usuarios -->
            <div class="management-card">
                <h5 class="section-title">Gestión de Usuarios</h5>
                <p class="mb-3">Administrar roles, permisos y cuentas</p>
                <a href="../SuperUsuario/usuarios.php">
                    <button class="btn btn-manage">Gestionar</button>
                </a>
            </div>

            <!-- Parámetros del sistema -->
            <div class="management-card">
                <h5 class="section-title">Parámetros del sistema</h5>
                <p class="mb-3">Configurar tasas, montos, límite y variables</p>
                <a href="../SuperUsuario/parametros.php">
                    <button class="btn btn-manage">Gestionar</button>
                </a>
            </div>

            <hr class="divider">

            <!-- Actividad reciente del sistema -->
            <h5 class="section-title">Actividad reciente del sistema</h5>

            <?php if (empty($actividades)): ?>
            <!-- Mensaje cuando NO hay actividades -->
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No hay actividades registradas en el sistema.
                <small class="d-block mt-1">
                    Esto puede significar que:
                    <br>1. No hay datos en la tabla auditoria_usuario
                    <br>2. Hay un error en la consulta
                    <br>3. Los triggers no están funcionando
                </small>
            </div>
            <?php endif; ?>

            <div class="table-container">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($actividades)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                <i class="bi bi-database-slash me-2"></i>
                                No hay actividades registradas
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($actividades as $actividad): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($actividad['fecha_hora'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($actividad['usuario_nombre'] ?? 'Sistema'); ?></td>
                            <td>
                                <span class="badge 
                                            <?php
                                            $accion = strtoupper($actividad['accion'] ?? '');
                                            if (in_array($accion, ['CREATE', 'INSERT']))
                                                echo 'bg-success';
                                            elseif (in_array($accion, ['UPDATE', 'MODIFY']))
                                                echo 'bg-warning text-dark';
                                            elseif (in_array($accion, ['DELETE', 'REMOVE']))
                                                echo 'bg-danger';
                                            elseif (in_array($accion, ['LOGIN', 'LOGOUT']))
                                                echo 'bg-primary';
                                            else
                                                echo 'bg-secondary';
                                            ?>">
                                    <?php echo htmlspecialchars($actividad['accion'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($actividad['detalle'] ?? ''); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Opcional: Enlace para ver más actividades -->
            <?php if (!empty($actividades)): ?>
            <div class="text-end mt-3">
                <a href="../SuperUsuario/auditoria_completa.php" class="view-history">
                    <i class="bi bi-clock-history me-1"></i> Ver historial completo
                </a>
            </div>
            <?php endif; ?>
        </div>
        <div class="card card-form">
            <div class="card-body p-4">
                <h2 class="mb-0">Panel de Administrador</h2>
                <p class="mb-3">Administrar Ahorradores</p>
                <a href="../Admin/inicio.php">
                    <button class="btn btn-manage">Gestionar</button>
                </a>
            </div>
        </div>
        <div class="card card-form">
            <div class="card-body p-4">
                <h2 class="mb-0">Panel de Ahorrador</h2>
                <p class="mb-3">Solicitar Préstamo, Ahorros </p>
                <a href="../Usuario/panelAhorrador.php">
                    <button class="btn btn-manage">Gestionar</button>
                </a>
            </div>
        </div>

    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- Script para depuración en consola -->
    <script>
    console.log("Actividades obtenidas: <?php echo count($actividades); ?>");
    </script>
</body>

</html>