<?php
// Usuario/solicitud_prestamo.php
require_once '../includes/init.php';
secure_session_start();
check_login(1);


// // 1. Seguridad: Verificar sesión y rol (Ahorrador o SuperUsuario)
// if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['id_rol'], [1])) {
//     header("Location: ../login.php");
//     exit();
//   }

  // Obtener nombre para mostrar
  $nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] . " " . ($_SESSION['apellido_paterno'] ?? '') : 'Usuario';
  
  // --- NUEVO: OBTENER TASA DE INTERÉS DINÁMICA ---
  try {
    $stmtConfig = $pdo->query("SELECT tasa_interes_general FROM datos_sistema WHERE id_datos = 1");
    $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);
    // Si encuentra el valor lo usa, si no, usa 30% por defecto. Se divide entre 100 para decimal (30.00 -> 0.30)
    $tasa_interes = ($config && isset($config['tasa_interes_general'])) ? ($config['tasa_interes_general'] / 100) : 0.30;
  } catch (Exception $e) {
    $tasa_interes = 0.30; // Fallback en caso de error
}

// 2. REGLA DE NEGOCIO: FECHAS Y PLAZOS
$mesActual = intval(date('m'));
$diaActual = intval(date('d'));

// Variables de control
$esDiciembre = ($mesActual == 12);
$quincenasRestantes = 0;
$mensajeAviso = "";

if ($esDiciembre) {
  // --- DICIEMBRE (LISTA DE ESPERA) ---
  $quincenasRestantes = 22; 
    $mensajeAviso = "AVISO DE CIERRE: Estamos en corte de caja. Puedes enviar tu solicitud, pero entrará en <strong>LISTA DE ESPERA</strong> y será procesada por el administrador hasta <strong>ENERO</strong>.";
} else {
    // --- RESTO DEL AÑO (Hasta 30 Nov) ---
    $mesesFaltantes = 11 - $mesActual; 
    $quincenasRestantes = $mesesFaltantes * 2;
    
    // Sumar quincenas del mes actual
    if ($diaActual <= 15) {
      $quincenasRestantes += 2; 
    } else {
      $quincenasRestantes += 1; 
    }
}
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Solicitud de Préstamo</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/prestamo.css">
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
    <main class="container my-5">

        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($esDiciembre): ?>
        <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm">
            <i class="bi bi-hourglass-split fs-2 me-3"></i>
            <div><?php echo $mensajeAviso; ?></div>
        </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card main-card p-5 shadow-sm border-0">
                    <h1 class="card-title text-center mb-4 fw-bold" style="color: #1a237e;">
                        <?php echo $esDiciembre ? "Pre-Solicitud (Lista de Espera)" : "Solicitud de Préstamo"; ?>
                    </h1>

                    <form class="row g-4" action="procesar_prestamo.php" method="POST">

                        <div class="col-12">
                            <label class="form-label fw-bold">Monto de préstamo deseado (MXN)</label>
                            <input type="number" name="monto" id="monto"
                                class="form-control form-control-lg input-amount" placeholder="Ej. 1000" min="100"
                                step="0.01" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">
                                <?php echo $esDiciembre ? "Plazo estimado (Inicio Enero)" : "Plazo calculado (Cierre 30 Nov)"; ?>
                            </label>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="term-pill bg-primary text-white p-2 rounded px-4 fw-bold">
                                    <?php echo $quincenasRestantes; ?> Quincena(s)
                                </div>
                                <div class="text-warning-note text-warning">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <small>El plazo se ajusta automáticamente al cierre fiscal.</small>
                                </div>
                            </div>
                            <input type="hidden" name="plazo" value="<?php echo $quincenasRestantes; ?>">
                        </div>

                        <div class="col-12">
                            <div class="estimate-box p-3 bg-light rounded border">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Monto Solicitado:</span> <span class="fw-bold"
                                        id="lbl_solicitado">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Interés (<?php echo ($tasa_interes * 100); ?>%):</span> <span
                                        class="fw-bold text-danger" id="lbl_interes">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <span>Total a Pagar:</span> <span class="fw-bold text-dark"
                                        id="lbl_total">$0.00</span>
                                </div>
                                <div class="text-center">
                                    <div class="small-muted text-secondary">Descuento quincenal estimado:</div>
                                    <div class="estimate-amount fs-3 fw-bold text-success" id="lbl_descuento">$0.00 MXN
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                                <label class="form-check-label" for="acceptTerms">
                                    Acepto los términos y condiciones del préstamo (Tasa de interés:
                                    <?php echo ($tasa_interes * 100); ?>%).
                                </label>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-3 flex-wrap align-items-center mt-4">
                            <a href="panelAhorrador.php" class="btn btn-outline-dark btn-cancel px-4">Cancelar</a>
                            <button type="submit" class="btn btn-warning ms-auto px-5 fw-bold text-white"
                                style="background-color: #fca311; border:none;">
                                Enviar Solicitud
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>

    <script>
    // Pasamos la variable PHP $tasa_interes (que ya es decimal, ej: 0.30) a JS
    const TASA_INTERES = <?php echo $tasa_interes; ?>;
    const PLAZO = <?php echo $quincenasRestantes; ?>;
    const inputMonto = document.getElementById('monto');
    const lblSolicitado = document.getElementById('lbl_solicitado');
    const lblInteres = document.getElementById('lbl_interes');
    const lblTotal = document.getElementById('lbl_total');
    const lblDescuento = document.getElementById('lbl_descuento');
    const formato = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    });

    inputMonto.addEventListener('input', function() {
        let monto = parseFloat(this.value);
        if (monto > 0 && PLAZO > 0) {
            let interes = monto * TASA_INTERES;
            let totalDeuda = monto + interes;
            let pagoQuincenal = totalDeuda / PLAZO;

            lblSolicitado.textContent = formato.format(monto);
            lblInteres.textContent = formato.format(interes);
            lblTotal.textContent = formato.format(totalDeuda);
            lblDescuento.textContent = formato.format(pagoQuincenal);
        } else {
            lblSolicitado.textContent = "$0.00";
            lblInteres.textContent = "$0.00";
            lblTotal.textContent = "$0.00";
            lblDescuento.textContent = "$0.00 MXN";
        }
    });
    </script>
</body>

</html>