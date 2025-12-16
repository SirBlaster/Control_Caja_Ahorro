<?php
require_once '../includes/init.php';
secure_session_start();
check_login(1);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Historial de Movimientos</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/estilo_ahorrador.css">

</head>

<body>
    <!-- HEADER -->
    <div class="header d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
        <div class="d-flex align-items-center">
            <img src="../img/LogoChico.png" alt="SETDITSX" width="70" class="me-3" />
            <h4 class="mb-0">SETDITSX - Panel Ahorrador</h4>
        </div>

        <div class="user-info d-flex align-items-center">
            <i class="bi bi-person-square user-icon me-2"></i>

            <div class="user-details me-3 text-end">
                <p class="user-name mb-0">
                    <?php echo htmlspecialchars(get_user_name()); ?>
                </p>
                <small class="text-muted">
                    <?php echo htmlspecialchars(get_user_role_text()); ?>
                </small>
            </div>

            <form action="../logout.php" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-logout" onclick="return confirm('¿Deseas cerrar sesión?')">
                    <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <!-- NAVBAR AHORRADOR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <!-- BOTÓN RESPONSIVE -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAhorrador"
                aria-controls="navbarAhorrador" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarAhorrador">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- ================= PANEL PRINCIPAL ================= -->
                    <li class="nav-item">
                        <a class="nav-link" href="../includes/redirect_inicio.php">
                            <i class="bi bi-house-door-fill me-1"></i>Inicio
                        </a>
                    </li>
                    <!-- ================= EDITAR PERFIL ================= -->
                    <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='editar_perfil.php') echo 'active'; ?>"
                            href="editar_perfil.php">
                            <i class="bi bi-person-gear me-1"></i>Editar perfil
                        </a>
                    </li>
                    <?php endif; ?>
                    <!-- ================= AHORRO ================= -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-piggy-bank me-1"></i>Ahorro
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="./registrahorro.php">
                                    <i class="bi bi-plus-circle me-1"></i>Solicitar Ahorro
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="./movimientos.php">
                                    <i class="bi bi-list-ul me-1"></i>Ver movimientos
                                </a></li>
                        </ul>
                    </li>
                    <!-- ================= PRÉSTAMOS ================= -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-cash-stack me-1"></i>Préstamos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="./solicitud_prestamo.php">
                                    <i class="bi bi-currency-dollar me-1"></i>Solicitar préstamo
                                </a></li>
                            <li><a class="dropdown-item" href="./Estado_Prestamo.php">
                                    <i class="bi bi-clipboard-check me-1"></i>Estado de mi préstamo
                                </a></li>
                        </ul>
                    </li>
                    <!-- ================= CONSULTAS ================= -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-search me-1"></i>Consultas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="./mis_solicitudes.php">
                                    <i class="bi bi-clock-history me-1"></i>Mis solicitudes
                                </a></li>
                            <li><a class="dropdown-item" href="./historial_completo.php">
                                    <i class="bi bi-journal-text me-1"></i>Historial completo
                                </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <main class="container mt-4">
        <h4 class="mb-3 fw-bold">Historial completo de movimientos</h4>

        <!-- BOTÓN REGRESAR -->
        <a href="panelAhorrador.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar</a>

        <!-- TABLA COMPLETA -->
        <div class="container mt-5 pt-4 mb-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark mb-0">Movimientos Generales</h2>
                <a href="historial_completo.php" class="btn btn-primary btn-sm rounded-pill px-4">
                    <i class="bi bi-calculator"></i> Ver con Saldos
                </a>
            </div>

            <div class="card card-custom">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Fecha</th>
                                <th>Descripción / Concepto</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-end pe-4">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($lista_movimientos)): ?>
                            <?php foreach ($lista_movimientos as $mov): ?>
                            <?php 
                                    // Lógica visual: ID 1 es Depósito (Verde), el resto (Retiro/Pago) es Rojo
                                    $es_ingreso = ($mov['id_tipo_movimiento'] == 1);
                                    
                                    $clase_texto = $es_ingreso ? 'text-success' : 'text-danger';
                                    $signo       = $es_ingreso ? '+' : '-';
                                    
                                    // Color de la etiqueta (Badge)
                                    $clase_badge = 'bg-secondary';
                                    if($mov['id_tipo_movimiento'] == 1) $clase_badge = 'bg-success'; // Depósito
                                    if($mov['id_tipo_movimiento'] == 2) $clase_badge = 'bg-warning text-dark'; // Retiro
                                    if($mov['id_tipo_movimiento'] == 3) $clase_badge = 'bg-info text-dark'; // Pago
                                ?>
                            <tr>
                                <td class="ps-4 text-muted fw-bold">
                                    <?php echo date("d/m/Y", strtotime($mov['fecha'])); ?>
                                    <br>
                                    <small
                                        class="fw-normal"><?php echo date("h:i A", strtotime($mov['fecha'])); ?></small>
                                </td>

                                <td class="fw-bold text-dark">
                                    <?php echo htmlspecialchars($mov['concepto']); ?>
                                </td>

                                <td class="text-center">
                                    <span class="badge badge-tipo <?php echo $clase_badge; ?>">
                                        <?php echo htmlspecialchars($mov['tipo_movimiento']); ?>
                                    </span>
                                </td>

                                <td class="text-end pe-4 fw-bold <?php echo $clase_texto; ?>"
                                    style="font-size: 1.1rem;">
                                    <?php echo $signo . '$' . number_format($mov['monto'], 2); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <div class="my-3">
                                        <i class="bi bi-receipt fs-1 text-secondary opacity-50"></i>
                                    </div>
                                    <h5>Sin movimientos registrados</h5>
                                    <p class="small">Aún no tienes actividad en tu cuenta.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-center">
                <small class="text-muted">Mostrando todos los movimientos registrados en el sistema.</small>
            </div>

        </div>

        <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>