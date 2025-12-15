<link rel="stylesheet" href="../css/estilo_ahorrador.css">
<?php
// Usuario/solicitud_prestamo.php
require_once '../includes/init.php';
secure_session_start();
check_login(1);


// 1. Seguridad: Verificar sesión y rol (Ahorrador o SuperUsuario)
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['id_rol'], [1])) {
    header("Location: ../login.php");
    exit();
}

// Obtener nombre para mostrar (Ajustado a nuevas columnas: nombre, apellido_paterno)
$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] . " " . ($_SESSION['apellido_paterno'] ?? '') : 'Usuario';

// 2. REGLA DE NEGOCIO: FECHAS Y PLAZOS (Audio)
// Los préstamos se cierran el 30 de Noviembre. Diciembre es corte de caja.
$mesActual = intval(date('m'));
$diaActual = intval(date('d'));

// Variables de control
$esDiciembre = ($mesActual == 12);
$quincenasRestantes = 0;
$mensajeAviso = "";

if ($esDiciembre) {
    // --- DICIEMBRE (LISTA DE ESPERA) ---
    // Calculamos para el próximo ciclo (Enero - Noviembre = 22 quincenas aprox)
    $quincenasRestantes = 22; 
    $mensajeAviso = "AVISO DE CIERRE: Estamos en corte de caja. Puedes enviar tu solicitud, pero entrará en <strong>LISTA DE ESPERA</strong> y será procesada por el administrador hasta <strong>ENERO</strong>.";
} else {
    // --- RESTO DEL AÑO (Hasta 30 Nov) ---
    // Meses completos faltantes hasta Noviembre (Mes 11)
    $mesesFaltantes = 11 - $mesActual; 
    $quincenasRestantes = $mesesFaltantes * 2;
    
    // Sumar quincenas del mes actual
    if ($diaActual <= 15) {
        $quincenasRestantes += 2; // Faltan la del 15 y la del 30
    } else {
        $quincenasRestantes += 1; // Solo falta la del 30
    }
}

// Tasa de interés fija: 30%
$tasa_interes = 0.30;
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Solicitud de Préstamo</title>
  <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/prestamo.css"> </head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light header">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">
          <img src="../img/LogoChico.png" width="50" height="50" class="d-inline-block align-items-center" alt=""> SETDITSX
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="panelAhorrador.php">Panel Principal</a>
            </li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Apartados (Ahorrador)
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/registrahorro.php">Solicitar Ahorro</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/solicitud_prestamo.php">Solicitar préstamo</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/movimientos.php">Ver movimientos</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/mis_solicitudes.php">Mis solicitudes</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/Estado_Prestamo.php">Estado de mi préstamo</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/historial_completo.php">Historial completo</a></li>
              </ul>
            </li>
          </ul>
        </div>

        <div class="d-flex align-items-center gap-3">
          <div class="user-details text-end d-none d-md-block">
            <p class="user-name mb-0 fw-bold"><?php echo get_user_name(); ?></p>
            <small class="text-muted"><?php echo get_user_role_text(); ?></small>
          </div>
          <a href="../logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
          </a>
        </div>
      </div>
    </nav>

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
              <input type="number" name="monto" id="monto" class="form-control form-control-lg input-amount" 
                     placeholder="Ej. 1000" min="100" step="0.01" required>
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
                    <span>Monto Solicitado:</span> <span class="fw-bold" id="lbl_solicitado">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Interés (30%):</span> <span class="fw-bold text-danger" id="lbl_interes">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span>Total a Pagar:</span> <span class="fw-bold text-dark" id="lbl_total">$0.00</span>
                </div>
                <div class="text-center">
                    <div class="small-muted text-secondary">Descuento quincenal estimado:</div>
                    <div class="estimate-amount fs-3 fw-bold text-success" id="lbl_descuento">$0.00 MXN</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                <label class="form-check-label" for="acceptTerms">
                  Acepto los términos y condiciones del préstamo.
                </label>
              </div>
            </div>

            <div class="col-12 d-flex gap-3 flex-wrap align-items-center mt-4">
                <a href="panelAhorrador.php" class="btn btn-outline-dark btn-cancel px-4">Cancelar</a>
                <button type="submit" class="btn btn-warning ms-auto px-5 fw-bold text-white" style="background-color: #fca311; border:none;">
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
      const TASA_INTERES = <?php echo $tasa_interes; ?>;
      const PLAZO = <?php echo $quincenasRestantes; ?>;
      const inputMonto = document.getElementById('monto');
      const lblSolicitado = document.getElementById('lbl_solicitado');
      const lblInteres = document.getElementById('lbl_interes');
      const lblTotal = document.getElementById('lbl_total');
      const lblDescuento = document.getElementById('lbl_descuento');
      const formato = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

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
              lblSolicitado.textContent = "$0.00"; lblInteres.textContent = "$0.00"; 
              lblTotal.textContent = "$0.00"; lblDescuento.textContent = "$0.00 MXN";
          }
      });
  </script>
</body>
</html>
