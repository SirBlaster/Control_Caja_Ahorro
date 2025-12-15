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
    <title>Historial de Movimientos</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
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

    <main class="container mt-4">
      <h4 class="mb-3 fw-bold">Historial completo de movimientos</h4>

      <!-- BOTÓN REGRESAR -->
      <a href="panelAhorrador.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar</a>

      <!-- TABLA COMPLETA -->
      <div class="table-wrap mt-2">
        <table class="table table-sm table-borderless">
          <thead class="table-head">
            <tr>
              <th>ID</th>
              <th>Fecha y Hora</th>
              <th>Concepto</th>
              <th class="text-end">Monto</th>
              <th>Tipo de Movimiento</th>
              <th class="text-end">Saldo</th>
            </tr>
          </thead>

          <tbody>
            <!-- EJEMPLOS (AUMENTA LOS QUE NECESITES) -->
            <tr>
              <td>1A</td>
              <td>28/10/25 6:34 P.M.</td>
              <td>Depósito Nómina</td>
              <td class="text-end text-success">+$7,500.00</td>
              <td>Depósito</td>
              <td class="text-end">$17,500.00</td>
            </tr>
            <tr>
              <td>2B</td>
              <td>20/10/25 12:54 P.M.</td>
              <td>Retiro Emergencia</td>
              <td class="text-end text-danger">-$1,000.00</td>
              <td>Retiro</td>
              <td class="text-end">$16,500.00</td>
            </tr>
            <tr>
              <td>3C</td>
              <td>29/11/25 2:48 P.M.</td>
              <td>Préstamo</td>
              <td class="text-end text-danger">-$450.00</td>
              <td>Pago</td>
              <td class="text-end">$16,050.00</td>
            </tr>
            <tr>
              <td>4D</td>
              <td>25/12/25 7:00 A.M.</td>
              <td>Suscripción</td>
              <td class="text-end text-danger">-$299.00</td>
              <td>Pago</td>
              <td class="text-end">$15,751.00</td>
            </tr>

            <!-- Puedes duplicar y agregar más registros -->
          </tbody>
        </table>
      </div>
    </main>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
  </body>
</html>
