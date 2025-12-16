<?php
// Administrador/Inicio.php
require_once '../includes/init.php';

secure_session_start();
check_login(2); // Nivel 2 = Administrador (ajusta si usas otro)


// --- PAGINACIÓN ---
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$registros_por_pagina = 10;

// Obtener solicitudes
$result = obtener_solicitudes($pdo, $pagina, $registros_por_pagina);
$solicitudes = $result['solicitudes'];
$total_paginas = $result['total_paginas'];

// Calcular paginación avanzada
$max_enlaces = 5;
$start = max(1, $pagina - floor($max_enlaces / 2));
$end = min($total_paginas, $start + $max_enlaces - 1);
if ($end - $start + 1 < $max_enlaces) {
    $start = max(1, $end - $max_enlaces + 1);
}

// --- TOTAL PENDIENTES ---
$pendientes = total_pendientes($pdo); // ['ahorro'=>x, 'prestamo'=>y, 'total'=>z]
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Administrador - SETDITSX</title>

    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../css/admin.css" />
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
                        <i class="bi bi-cash-stack me-1"></i>Gestión de prestamos y ahorros
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
    <div class="card-form">
        <h2>Panel de Administrador</h2>

        <div class="dashboard-cards">
            <!-- Gestión de Préstamos -->
            <div class="dashboard-card">
                <div class="card-title">Gestión de Préstamos y Ahorros</div>
                <div class="card-count"><?php echo $pendientes['total']; ?></div>
                <div class="card-description">Solicitudes por revisar</div>
                <a href="gestion_prestamos.php" class="btn btn-manage">
                    Gestionar
                </a>
            </div>

            <!-- Gestión de Ahorradores -->
            <div class="dashboard-card">
                <div class="card-title">Gestión de usuarios</div>
                <div class="card-icon">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="card-description">
                    Consultar perfiles y registrar nómina
                </div>
                <a href="gestion_ahorradores.php" class="btn btn-manage">
                    Consultar
                </a>
            </div>

            <!-- Reportes -->
            <div class="dashboard-card">
                <div class="card-title">Reportes</div>
                <div class="card-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div class="card-description">Generar y descargar</div>
                <a href="reportes.php" class="btn btn-manage">
                    Emitir
                </a>
            </div>
        </div>

        <hr class="divider" />

        <!-- SOLICITUDES RECIENTES -->
        <h5 class="section-title">Solicitudes recientes</h5>

        <div class="table-container">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Solicitante</th>
                        <th>RFC</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($solicitudes)): ?>
                    <?php foreach ($solicitudes as $sol): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sol['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($sol['rfc']); ?></td>
                        <td><?php echo htmlspecialchars($sol['tipo']); ?></td>
                        <td>$<?php echo number_format($sol['monto'], 2); ?></td>
                        <td class="<?php echo estado_class($sol['estado']); ?>">
                            <?php echo htmlspecialchars($sol['estado']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay solicitudes recientes</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación avanzada -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Botón anterior -->
                <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo max(1, $pagina-1); ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <!-- Números de página -->
                <?php for($i=$start; $i<=$end; $i++): ?>
                <li class="page-item <?php echo ($i==$pagina)?'active':''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>

                <!-- Botón siguiente -->
                <li class="page-item <?php echo ($pagina >= $total_paginas) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo min($total_paginas, $pagina+1); ?>"
                        aria-label="Siguiente">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>

    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>