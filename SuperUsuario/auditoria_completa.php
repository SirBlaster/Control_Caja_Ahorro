<?php
// SuperUsuario/auditoria_completa.php
require_once '../includes/init.php';
secure_session_start();
check_login(3);

// Obtener actividades
$actividades = obtener_actividades_completas();
$total_actividades = count($actividades);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría Completa</title>
    <link rel="stylesheet" href="../css/Super.css">
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
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