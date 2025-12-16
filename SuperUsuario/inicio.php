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
            <div class="card-body p-4">
                <h2 class="mb-0">Panel de Ahorrador</h2>
                <p class="mb-3">Solicitar Préstamo, Ahorros </p>
                <a href="../Usuario/panelAhorrador.php">
                    <button class="btn btn-manage">Gestionar</button>
                </a>
            </div>
        </div>
        <!-- <div class="card card-form">
            <div class="card-body p-4">
                <h2 class="mb-0">Panel de Ahorrador</h2>
                <p class="mb-3">Solicitar Préstamo, Ahorros </p>
                <a href="../Usuario/panelAhorrador.php">
                    <button class="btn btn-manage">Gestionar</button>
                </a>
            </div>
        </div> -->

    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>

</body>

</html>