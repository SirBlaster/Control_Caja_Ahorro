<?php
// Página actual
$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina < 1) $pagina = 1;

// Registros por página
$por_pagina = 20;
$offset = ($pagina - 1) * $por_pagina;

// Obtener préstamos
require_once __DIR__ . '/acciones/obtener_prestamos.php';

require_once '../includes/init.php';

secure_session_start();
check_login(2);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Préstamos - Administrador</title>

    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>

    <div class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="../img/NewLogo - 1.png" alt="SETDITSX" width="70" class="me-3">
            <h4 class="mb-0">SETDITSX - Sindicato ITSX</h4>
        </div>

        <div>
            <h2 class="mb-0">Administrador</h2>
        </div>

        <div class="user-info">
            <i class="bi bi-person-square user-icon"></i>

            <div class="user-details">
                <p class="user-name">
                    <?php echo htmlspecialchars(get_user_name()); ?>
                </p>
                <small class="text-muted">
                    <?php echo htmlspecialchars(get_user_role_text()); ?>
                </small>
            </div>

            <!-- CERRAR SESIÓN -->
            <form action="../logout.php" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-logout" onclick="return confirm('¿Deseas cerrar sesión?')">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <div class="container-fluid mt-4">

        <a href="./inicio.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar</a>

        <h2 class="mb-3">Solicitudes de préstamos pendientes</h2>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Solicitante</th>
                        <th>Fecha</th>
                        <th>Monto solicitado</th>
                        <th>Total a pagar</th>
                        <th>Plazo (quincenas)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (count($prestamos) > 0): ?>
                    <?php foreach ($prestamos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['solicitante']) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['fecha_solicitud'])) ?></td>
                        <td>$<?= number_format($p['monto_solicitado'], 2) ?></td>
                        <td><strong>$<?= number_format($p['total_a_pagar'], 2) ?></strong></td>
                        <td><?= $p['plazo_quincenas'] ?></td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                <?= htmlspecialchars($p['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                <form action="acciones/aprobar_prestamo.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $p['id_solicitud_prestamo'] ?>">
                                    <button class="btn btn-success btn-sm">Aprobar</button>
                                </form>

                                <form action="acciones/rechazar_prestamo.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $p['id_solicitud_prestamo'] ?>">
                                    <button class="btn btn-danger btn-sm">Rechazar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7">No hay solicitudes pendientes</td>
                    </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        <nav>
            <ul class="pagination justify-content-center">

                <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $pagina - 1 ?>">Anterior</a>
                </li>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?= $pagina >= $total_paginas ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $pagina + 1 ?>">Siguiente</a>
                </li>

            </ul>
        </nav>

    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>