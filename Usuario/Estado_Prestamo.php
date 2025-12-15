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
  <title>Estado de Préstamo</title>

  <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">

  <link rel="stylesheet" href="../css/estado_prestamo.css">
    <link rel="stylesheet" href="../css/estilo_ahorrador.css">
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
                        <li><h6 class="dropdown-header text-primary">Ahorro</h6></li>
                        <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/registrahorro.php">Solicitar Ahorro</a></li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <li><h6 class="dropdown-header text-primary">Préstamos</h6></li>
                        <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/solicitud_prestamo.php">Solicitar préstamo</a></li>
                        <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/Estado_Prestamo.php">Estado de mi préstamo</a></li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <li><h6 class="dropdown-header text-primary">Movimientos y Consultas</h6></li>
                        <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/movimientos.php">Ver movimientos</a></li>
                        <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/mis_solicitudes.php">Mis solicitudes</a></li>
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
  <div class="card main-card p-4">

    <h1 class="mb-4 titulo-pagina">Estado de préstamo</h1>

    <!-- Bloque de Totales -->
    <div class="resumen-box p-4 mb-4">
      <div class="item-total">
        <span class="label">Monto total:</span>
        <span class="valor rojo">$22,500.00 MXM</span>
      </div>

      <div class="item-total saldo-box">
        <span class="label">Saldo Pendiente</span>
        <span class="valor dorado">$15,000.00 MXM</span>
      </div>

      <div class="item-total">
        <span class="label">Monto pagado:</span>
        <span class="valor verde">$7,500.00 MXM</span>
      </div>
    </div>

    <h5 class="fw-bold mb-2 historial-title">Historial de pagos</h5>

    <div class="table-wrap">
      <table class="table table-sm mb-0 table-borderless">
        <thead class="table-head">
          <tr>
            <th style="width:10%;">No.Cuota</th>
            <th style="width:20%;">Fecha</th>
            <th>Monto total</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>2810</td>
            <td>20/10/25</td>
            <td>$4,500.00</td>
          </tr>

          <tr>
            <td>2549</td>
            <td>8/8/25</td>
            <td>$2,250.00</td>
          </tr>

          <tr>
            <td>9224</td>
            <td>24/6/25</td>
            <td>$3,800.00</td>
          </tr>

          <tr>
            <td>5432</td>
            <td>12/3/25</td>
            <td>$999.00</td>
          </tr>

          <tr>
            <td>4262</td>
            <td>27/2/25</td>
            <td>$2,760.00</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</main>

<script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
