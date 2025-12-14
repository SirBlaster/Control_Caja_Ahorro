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
    <div class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="../img/NewLogo - 1.png" alt="SETDITSX" width="70" class="me-3">
            <h4 class="mb-0">SETDITSX - Sindicato ITSX</h4>
        </div>

        <div class="user-info">
            <i class="bi bi-person-square user-icon"></i>
            <div class="user-details">
                <p class="user-name"><?php echo get_user_name(); ?></p>
                <small class="text-muted"><?php echo get_user_role_text(); ?></small>
            </div>
            <form action="../logout.php" method="POST" style="display: inline;">
                <button type="submit" class="btn btn-logout">
                    <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
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
                                            if (in_array($accion, ['CREATE', 'INSERT'])) echo 'bg-success';
                                            elseif (in_array($accion, ['UPDATE', 'MODIFY'])) echo 'bg-warning text-dark';
                                            elseif (in_array($accion, ['DELETE', 'REMOVE'])) echo 'bg-danger';
                                            elseif (in_array($accion, ['LOGIN', 'LOGOUT'])) echo 'bg-primary';
                                            else echo 'bg-secondary';
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
    </div>

    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- Script para depuración en consola -->
    <script>
    console.log("Actividades obtenidas: <?php echo count($actividades); ?>");
    </script>
</body>

</html>