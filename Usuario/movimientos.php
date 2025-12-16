<?php
require_once '../includes/init.php';
secure_session_start();
check_login(1); // Rol 2: Ahorrador

// Cargar la lógica de datos
require_once '../includes/Usuario/logica_mov_ahorro.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movimientos de Ahorro - SETDITSX</title>

    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/estilo_ahorrador.css">
    <style>
    .navbar.header {
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }

    /* body {
        padding-top: 70px !important;
    } */
    </style>

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
    <div class="container mt-5 pt-4 mb-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-0">Cuenta de Ahorro</h3>
                <p class="text-muted mb-0">Historial exclusivo de depósitos y retiros</p>
            </div>
            <a href="panelAhorrador.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Regresar
            </a>
        </div>

        <div class="card card-ahorro bg-white">
            <div class="card-header header-ahorro d-flex justify-content-between align-items-center">
                <span><i class="bi bi-piggy-bank me-2"></i> Movimientos Registrados</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase small">Fecha</th>
                            <th class="py-3 text-secondary text-uppercase small">Concepto</th>
                            <th class="py-3 text-secondary text-uppercase small text-center">Tipo</th>
                            <th class="pe-4 py-3 text-secondary text-uppercase small text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($movimientos_ahorro)): ?>
                        <?php foreach ($movimientos_ahorro as $mov): ?>
                        <?php 
                                    // Lógica visual
                                    $es_deposito = ($mov['id_tipo_movimiento'] == 1);
                                    
                                    $clase_monto = $es_deposito ? 'text-success' : 'text-danger';
                                    $signo       = $es_deposito ? '+' : '-';
                                    $clase_badge = $es_deposito ? 'badge-deposito' : 'badge-retiro';
                                    $icono       = $es_deposito ? 'bi-arrow-up-circle' : 'bi-arrow-down-circle';
                                ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted">
                                <?php echo date("d/m/Y", strtotime($mov['fecha'])); ?>
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i
                                        class="bi <?php echo $icono; ?> <?php echo $es_deposito ? 'text-success' : 'text-danger'; ?>"></i>
                                    <?php echo htmlspecialchars($mov['concepto']); ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge <?php echo $clase_badge; ?> rounded-pill px-3">
                                    <?php echo htmlspecialchars($mov['tipo_movimiento']); ?>
                                </span>
                            </td>

                            <td class="pe-4 text-end fw-bold <?php echo $clase_monto; ?>" style="font-size: 1.1rem;">
                                <?php echo $signo . '$' . number_format($mov['monto'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted opacity-50 mb-2">
                                    <i class="bi bi-wallet2 fs-1"></i>
                                </div>
                                <h6 class="text-muted">No hay movimientos de ahorro</h6>
                                <small class="text-secondary">Aquí aparecerán tus nóminas registradas y retiros.</small>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>