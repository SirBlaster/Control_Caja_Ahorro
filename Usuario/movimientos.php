<?php
require_once '../includes/init.php';
secure_session_start();
check_login(2);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Movimientos - Caja de Ahorro</title>

  <!-- Bootstrap (local en tu proyecto) -->
  <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
  <!-- Estilos locales -->
  <link rel="stylesheet" href="../css/movimientos.css">

</head>
<body>

  <!-- Header -->
  <header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="#">
                <img src="../img/NewLogo - 1.png" alt="logo" height="40">
                <span class="fw-bold text-dark">Historial de Solicitudes</span>
            </a>

            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-block text-end">
                    <div class="fw-bold" style="font-size: 0.9rem; color: #153b52;">
                        <?php echo get_user_name(); ?>
                    </div>
                    <small class="text-muted"><?php echo get_user_role_text(); ?></small>
                </div>
                <form action="../logout.php" method="POST" style="display: inline;">
                <button type="submit" class="btn btn-logout">
                    <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                </button>
                </form>
                
            </div>
        </div>
    </nav>
  </header>

  <!-- Main -->
  <main class="container my-5">
    <br>
    <a href="panelAhorrador.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar</a>
    <div class="card main-card p-4">
      <h1 class="mb-4 movimientos-title">Movimientos</h1>

      <!-- Filtros -->
      <div class="row align-items-center mb-4 g-3">
        <div class="col-lg-4 col-md-6">
          <label class="form-label fw-bold small">Filtrar por Tipo</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo" id="tipo1" checked>
            <label class="form-check-label" for="tipo1">Depósito</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo" id="tipo2">
            <label class="form-check-label" for="tipo2">Retiro</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo" id="tipo3">
            <label class="form-check-label" for="tipo3">Transferencia</label>
          </div>
        </div>

        <div class="col-lg-5 col-md-6">
          <label class="form-label fw-bold small">Rango de Fechas</label>
          <div class="d-flex gap-2">
            <input type="date" class="form-control form-control-sm">
            <input type="date" class="form-control form-control-sm">
            <button class="btn btn-apply btn-sm">Aplicar Filtros</button>
          </div>
        </div>
      </div>

      <!-- Tabla -->
      <div class="table-wrap">
        <table class="table table-sm table-borderless mb-0">
          <thead class="table-head">
            <tr>
              <th style="width:6%;">ID</th>
              <th style="width:18%;">Fecha y Hora</th>
              <th>Concepto</th>
              <th class="text-end" style="width:12%;">Monto</th>
              <th style="width:18%;">Tipo de Movimiento</th>
              <th class="text-end" style="width:12%;">Saldo</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1A</td>
              <td>20/11/25 2:13 PM</td>
              <td>Depósito Nómina</td>
              <td class="text-end text-success">+$7,500.00</td>
              <td>Depósito</td>
              <td class="text-end">$17,500.00</td>
            </tr>
            <tr>
              <td>2B</td>
              <td>7/11/25 12:54 PM</td>
              <td>Retiro Emergencia</td>
              <td class="text-end text-danger">-$1,000.00</td>
              <td>Retiro</td>
              <td class="text-end">$16,500.00</td>
            </tr>
            <tr>
              <td>3C</td>
              <td>2/11/25 2:48 PM</td>
              <td>Préstamo</td>
              <td class="text-end text-danger">-$450.00</td>
              <td>Pago</td>
              <td class="text-end">$16,050.00</td>
            </tr>
            <tr>
              <td>4D</td>
              <td>28/10/25 6:34 PM</td>
              <td>Suscripción</td>
              <td class="text-end text-danger">-$299.00</td>
              <td>Pago</td>
              <td class="text-end">$15,751.00</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </main>

<script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
