<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Solicitud de Préstamo</title>

  <!-- Bootstrap (local en tu proyecto) -->
  <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
  <!-- Estilos locales -->
  <link rel="stylesheet" href="../css/prestamo.css">
</head>
<body>

  <!-- Header -->
  <header class="app-header">
    <div class="container d-flex align-items-center justify-content-between py-3">
      <div class="d-flex align-items-center gap-3">
        <img src="../img/LogoHorizontal - 2.png" alt="logo" class="header-logo">
        <span class="brand-name">SETDITSX - Sindicato ITSX</span>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="user-name">Sánchez Cortes Felipe Martin ▾</div>
        <button class="btn btn-sm btn-outline-primary">Cerrar Sesión</button>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card main-card p-5">
          <h1 class="card-title text-center mb-4">Solicitud de Préstamo</h1>

          <form class="row g-4">
            <!-- Monto -->
            <div class="col-12">
              <label class="form-label fw-bold">Monto de préstamo deseado (MXN)</label>
              <input type="text" class="form-control form-control-lg input-amount" value="$1,000.00 MXN" aria-label="Monto de préstamo deseado">
            </div>

            <!-- Plazo -->
            <div class="col-12">
              <label class="form-label fw-bold">Plazo (Quincenas)</label>
              <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="term-pill">8 Quincenas</div>
                <div class="text-warning-note">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>
                  El plazo se establece dependiendo del número de quincenas pendientes del año
                </div>
              </div>
            </div>

            <!-- Pago estimado -->
            <div class="col-12">
              <div class="estimate-box p-3">
                <div class="small-muted">Tu pago quincenal estimado:</div>
                <div class="estimate-amount">$162.50 MXN</div>
              </div>
            </div>

            <!-- Checkbox -->
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="acceptTerms">
                <label class="form-check-label" for="acceptTerms">
                  Acepto los términos y condiciones
                </label>
              </div>
            </div>

            <!-- Buttons -->
            <div class="col-12 d-flex gap-3 flex-wrap align-items-center">
                <a href="panelAhorrador.php" class="btn btn-outline-dark btn-cancel">Cancelar</a>
              <button type="submit" class="btn btn-submit ms-auto">Enviar solicitud</button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
