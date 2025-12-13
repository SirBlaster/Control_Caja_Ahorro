<?php
// Usuario/solicitud_prestamo.php
require_once '../includes/init.php';

// 1. Seguridad: Verificar sesión y rol (Ahorrador o SuperUsuario)
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['id_rol'], [2, 3])) {
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

  <header class="app-header bg-white border-bottom">
    <div class="container d-flex align-items-center justify-content-between py-3">
      <div class="d-flex align-items-center gap-3">
        <img src="../img/LogoHorizontal - 2.png" alt="logo" class="header-logo" style="height: 50px;">
        <span class="brand-name fw-bold text-primary">SETDITSX - Sindicato ITSX</span>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="user-name fw-bold"><?php echo htmlspecialchars($nombreUsuario); ?> ▾</div>
        <a href="../logout.php" class="btn btn-sm btn-outline-danger">Cerrar Sesión</a>
      </div>
    </div>
  </header>

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