<link rel="stylesheet" href="../css/estilo_ahorrador.css">
<?php
require_once '../includes/init.php';
secure_session_start();
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
