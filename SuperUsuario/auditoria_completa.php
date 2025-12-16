<?php
// SuperUsuario/auditoria_completa.php
require_once '../includes/init.php';
secure_session_start();
check_login(3);

// Obtener TODAS las actividades
$actividades = obtener_actividades_completas();
$total_actividades = count($actividades);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría Completa</title>
    <link rel="stylesheet" href="../../css/Super.css">
    <link rel="stylesheet" href="../../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/bootstrap-icons/font/bootstrap-icons.css">
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="nav-actions">
                    <a href="../SuperUsuario/inicio.php" class="nav-link">
                        <i class="bi bi-arrow-left"></i> Volver al menú principal
                    </a>
                </div>
                <h2 class="mb-0">Auditoría Completa del Sistema</h2>
            </div>
            <div class="badge bg-primary fs-6">
                <i class="bi bi-list-check me-1"></i>
                Total: <?php echo $total_actividades; ?> registros
            </div>
        </div>

        <div class="card-body p-4">
            <div>
                <button onclick="exportarCSV()" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i> Exportar CSV
                </button>
                <button onclick="window.print()" class="btn btn-outline-primary btn-sm ms-2">
                    <i class="bi bi-printer me-1"></i> Imprimir
                </button>
            </div>
            <br>
            <?php if (empty($actividades)): ?>
            <div class="alert alert-warning">
                <h5><i class="bi bi-exclamation-triangle me-2"></i>No hay actividades registradas</h5>
                <p>Posibles causas:</p>
                <ol>
                    <li>La tabla <code>auditoria_usuario</code> está vacía</li>
                    <li>Los triggers no están funcionando</li>
                    <li>No se han realizado acciones auditables</li>
                </ol>
                <div class="mt-3">
                    <a href="test_auditoria.php" class="btn btn-sm btn-primary">
                        <i class="bi bi-gear me-1"></i> Probar auditoría
                    </a>
                    <button onclick="insertarDatosPrueba()" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Insertar datos de prueba
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="160">Fecha/Hora</th>
                            <th width="150">Usuario</th>
                            <th width="120">Acción</th>
                            <th>Detalle</th>
                            <th width="120">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($actividades)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-database-slash me-2 fs-4"></i>
                                <h5 class="mt-2">No hay actividades registradas</h5>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($actividades as $actividad): ?>
                        <tr>
                            <td class="text-nowrap">
                                <small><?php echo htmlspecialchars($actividad['fecha_hora']); ?></small>
                            </td>
                            <td>
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo htmlspecialchars($actividad['usuario_nombre']); ?>
                            </td>
                            <td>
                                <?php 
                                        $accion = strtoupper($actividad['accion']);
                                        $clase_badge = 'bg-secondary';
                                        $icono = 'bi-activity';
                                        
                                        if (strpos($accion, 'LOGIN') !== false) {
                                            $clase_badge = 'badge-login';
                                            $icono = 'bi-box-arrow-in-right';
                                        } elseif (strpos($accion, 'LOGOUT') !== false) {
                                            $clase_badge = 'badge-logout';
                                            $icono = 'bi-box-arrow-right';
                                        } elseif (strpos($accion, 'CREATE') !== false || strpos($accion, 'INSERT') !== false) {
                                            $clase_badge = 'badge-create';
                                            $icono = 'bi-plus-circle';
                                        } elseif (strpos($accion, 'UPDATE') !== false) {
                                            $clase_badge = 'badge-update';
                                            $icono = 'bi-pencil-square';
                                        } elseif (strpos($accion, 'DELETE') !== false) {
                                            $clase_badge = 'badge-delete';
                                            $icono = 'bi-trash';
                                        }
                                        ?>
                                <span class="badge <?php echo $clase_badge; ?>">
                                    <i class="bi <?php echo $icono; ?> me-1"></i>
                                    <?php echo htmlspecialchars($actividad['accion']); ?>
                                </span>
                            </td>
                            <td class="text-break">
                                <?php echo htmlspecialchars($actividad['detalle']); ?>
                                <?php if (!empty($actividad['user_agent'])): ?>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-laptop"></i>
                                    <?php echo htmlspecialchars(substr($actividad['user_agent'], 0, 60)); ?>...
                                </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <i class="bi bi-globe me-1"></i>
                                    <?php echo htmlspecialchars($actividad['ip_address'] ?? 'N/A'); ?>
                                </small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_actividades > 0): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Mostrando <?php echo $total_actividades; ?> registros
                </small>

            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="../js/d_audit.js">
    </script>
</body>

</html>