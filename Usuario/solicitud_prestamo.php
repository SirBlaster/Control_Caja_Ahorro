<?php
// =========================================================
// LÓGICA PHP (SEGURIDAD Y VARIABLES)
// =========================================================
require_once '../includes/init.php';

// Verificar que sea Ahorrador (Rol 2) o SuperUsuario (Rol 3)
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['id_rol'], [2, 3])) {
    header("Location: ../login.php");
    exit();
}

// Obtener nombre para mostrar en el header
$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] . " " . ($_SESSION['paterno'] ?? '') : 'Usuario';

// --- CONFIGURACIÓN AUTOMÁTICA ---
// Plazo fijo calculado (1 quincena restante del año)
$plazo_quincenas = 1; 
// Tasa de interés (30%)
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
  <link rel="stylesheet" href="../css/prestamo.css">
</head>
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
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card main-card p-5 shadow-sm border-0">
          <h1 class="card-title text-center mb-4 fw-bold" style="color: #1a237e;">Solicitud de Préstamo</h1>

          <form class="row g-4" action="procesar_prestamo.php" method="POST">
            
            <div class="col-12">
              <label class="form-label fw-bold">Monto de préstamo deseado (MXN)</label>
              <input type="number" name="monto" id="monto" class="form-control form-control-lg input-amount" 
                     placeholder="Ej. 5000" min="100" step="0.01" required>
            </div>

            <div class="col-12">
              <label class="form-label fw-bold">Plazo (Quincenas)</label>
              <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="term-pill bg-primary text-white p-2 rounded px-4 fw-bold">
                    <?php echo $plazo_quincenas; ?> Quincena(s)
                </div>
                
                <div class="text-warning-note text-warning">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>
                  <small>El plazo se establece automáticamente (cierre de año).</small>
                </div>
              </div>
              <input type="hidden" name="plazo" value="<?php echo $plazo_quincenas; ?>">
            </div>

            <div class="col-12">
              <div class="estimate-box p-3 bg-light rounded border">
                <div class="small-muted text-secondary">Tu pago quincenal estimado (Capital + 30% Interés):</div>
                <div class="estimate-amount fs-4 fw-bold text-success" id="pago_estimado">$0.00 MXN</div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                <label class="form-check-label" for="acceptTerms">
                  Acepto los términos y condiciones
                </label>
              </div>
            </div>

            <div class="col-12 d-flex gap-3 flex-wrap align-items-center mt-4">
                <a href="panelAhorrador.php" class="btn btn-outline-dark btn-cancel px-4">Cancelar</a>
                
                <button type="submit" class="btn btn-warning ms-auto px-5 fw-bold text-white" 
                        style="background-color: #fca311; border:none;">
                    Enviar solicitud
                </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>

  <script>
      // Variables desde PHP
      const TASA_INTERES = <?php echo $tasa_interes; ?>; // 0.30
      const PLAZO = <?php echo $plazo_quincenas; ?>;     // 1

      const inputMonto = document.getElementById('monto');
      const divPago = document.getElementById('pago_estimado');

      // Función que calcula en tiempo real
      inputMonto.addEventListener('input', function() {
          let monto = parseFloat(this.value);

          if (monto > 0) {
              // 1. Calcular total con interés (Monto + 30%)
              let totalConInteres = monto * (1 + TASA_INTERES);
              
              // 2. Dividir entre las quincenas
              let pagoQuincenal = totalConInteres / PLAZO;

              // 3. Formatear a Moneda ($1,300.00 MXN)
              let formato = new Intl.NumberFormat('es-MX', {
                  style: 'currency',
                  currency: 'MXN'
              }).format(pagoQuincenal);

              divPago.textContent = formato + " MXN";
          } else {
              divPago.textContent = "$0.00 MXN";
          }
      });
  </script>

</body>
</html>