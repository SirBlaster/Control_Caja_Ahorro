<?php
require_once '../includes/init.php';

secure_session_start();
check_login(1); 

require_once '../includes/Usuario/logica_panel.php'; 

// --- RECARGAMOS DATOS LOCALMENTE PARA ASEGURARNOS QUE ESTÉN FRESCOS ---
$id_usuario = $_SESSION['id_usuario'];
$nombreUsuario = get_user_name(); 

// A. Obtener Saldo Ahorro
$stmt = $pdo->prepare("SELECT monto_ahorrado FROM ahorro WHERE id_usuario = :id");
$stmt->execute([':id' => $id_usuario]);
$ahorro = $stmt->fetch(PDO::FETCH_ASSOC);
$saldo_total = $ahorro ? $ahorro['monto_ahorrado'] : 0.00;

// B. CONSULTA CORREGIDA: BUSCAR PRÉSTAMO (PENDIENTE O ACTIVO)
// Buscamos estado 1 (Pendiente) o 2 (Aprobado). Ordenamos DESC para priorizar el más reciente.
$sqlPrestamo = "SELECT * FROM solicitud_prestamo 
                WHERE id_usuario = :id AND id_estado IN (1, 2) 
                ORDER BY id_estado DESC LIMIT 1";
$stmtPrestamo = $pdo->prepare($sqlPrestamo);
$stmtPrestamo->execute([':id' => $id_usuario]);
$miPrestamo = $stmtPrestamo->fetch(PDO::FETCH_ASSOC);

// Variables de control para la vista
$tieneSolicitud = false;
$esPendiente = false;
$montoMostrar = 0;
$textoEstado = "Sin préstamo activo";
$claseEstado = "text-muted"; 

if ($miPrestamo) {
    $tieneSolicitud = true;
    $montoMostrar = $miPrestamo['total_a_pagar']; 
    
    if ($miPrestamo['id_estado'] == 1) {
        // CASO: PENDIENTE
        $esPendiente = true;
        $textoEstado = "Solicitud en Revisión";
        $claseEstado = "text-primary"; 
        $montoMostrar = $miPrestamo['total_a_pagar']; 
    } elseif ($miPrestamo['id_estado'] == 2) {
        // CASO: ACTIVO
        $textoEstado = "Saldo Pendiente";
        $claseEstado = "text-warning";
        $montoMostrar = $miPrestamo['saldo_pendiente']; 
    }
}


?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Panel - Ahorrador</title>

    <link rel="stylesheet" href="../css/estilo_ahorrador.css">
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/Bootstrap-icons/font/Bootstrap-icons.min.css">
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
    <?php if (isset($_GET['msg'])): ?>
    <div class="container mt-4">
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Aviso:</strong> <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>


    <div style="color: #1a237e;" class="alert d-flex align-items-center" role="alert">
        <div>
            El porcentaje de la caja se asigna al corte de la caja, despues del 30 de noviembre de cada año.
        </div>
    </div>
    <div class="main-container">


        <div class="dashboard-cards">

            <div class="info-card">
                <h6 class="card-label">CAJA DE AHORRO</h6>
                <div class="card-amount amount-success">
                    $ <?php echo number_format($saldo_total, 2); ?> <span class="fs-6 text-muted">MXN</span>

                    <?php 
             $mes_actual = date('n');
             if ($mes_actual == 12): 
             ?>
                    <hr class="my-2" style="opacity: 0.1">
                    <h6 class="card-label" style="font-size: 0.8rem;">Rendimiento
                        (<?php echo $porcentaje_Rendimiento; ?>%):</h6>
                    <div class="text-success fw-bold">
                        <?php 
                        $ganancia = ($saldo_total * $porcentaje_Rendimiento) / 100;
                        echo '+ $ ' . number_format($ganancia, 2); 
                    ?>
                        <span class="fs-6 text-muted">MXN</span>
                    </div>

                    <h6 class="card-label mt-2" style="font-size: 0.8rem;">Total al cierre:</h6>
                    <div class="fw-bold" style="color: #153b52;">
                        $ <?php echo number_format($saldo_total + $ganancia, 2); ?>
                        <span class="fs-6 text-muted">MXN</span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="text-muted small">Saldo total disponible</div>
                <a href="movimientos.php" class="btn btn-outline-primary mt-3 btn-sm w-100">Ver movimientos</a>
            </div>

            <div class="info-card" style="<?php echo $esPendiente ? 'border: 1px solid #0d6efd;' : ''; ?>">
                <h6 class="card-label">
                    <?php echo $esPendiente ? 'ESTADO SOLICITUD' : 'PRÉSTAMO ACTIVO'; ?>
                </h6>

                <div class="card-amount <?php echo $esPendiente ? 'text-primary' : 'amount-warning'; ?>">
                    $ <?php echo number_format($montoMostrar, 2); ?> <span class="fs-6 text-muted">MXN</span>
                </div>

                <div class="small fw-bold <?php echo $claseEstado; ?>">
                    <?php echo $textoEstado; ?>
                </div>

                <?php if ($tieneSolicitud): ?>
                <a href="Estado_Prestamo.php" class="btn btn-outline-primary mt-3 btn-sm w-100">
                    <?php echo $esPendiente ? 'Ver estado de revisión' : 'Consultar detalles'; ?>
                </a>
                <?php else: ?>
                <button class="btn btn-light mt-3 btn-sm w-100" disabled>Sin préstamo activo</button>
                <?php endif; ?>
            </div>

        </div>

        <section>
            <h6 class="section-title">ÚLTIMOS MOVIMIENTOS</h6>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th>Tipo</th>
                            <th class="text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($movimientos)): ?>
                        <?php foreach ($movimientos as $mov): 
                           // CORRECCIÓN: Claves en minúscula (id_tipo_movimiento)
                           $es_ingreso = ($mov['id_tipo_movimiento'] == 1); 
                           
                           $clase_monto = $es_ingreso ? 'text-success' : 'text-danger';
                           $signo = $es_ingreso ? '+' : '-';
                           $badge_bg = $es_ingreso ? 'bg-success' : 'bg-warning text-dark';
                       ?>
                        <tr>
                            <td><?php echo date("d/m/Y", strtotime($mov['fecha'])); ?></td>

                            <td><?php echo htmlspecialchars($mov['concepto']); ?></td>

                            <td>
                                <span class="badge badge-tipo <?php echo $badge_bg; ?>">
                                    <?php echo htmlspecialchars($mov['etiqueta_tipo'] ?? 'Sin Asignar'); ?>
                                </span>
                            </td>

                            <td class="text-end fw-bold <?php echo $clase_monto; ?>">
                                <?php echo $signo . '$' . number_format($mov['monto'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No hay movimientos recientes.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-end">
                <a href="historial_completo.php" class="text-primary fw-bold text-decoration-none"> Ver historial
                    completo &rarr;</a>
            </div>
        </section>

    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>