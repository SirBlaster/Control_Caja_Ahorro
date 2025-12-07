<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Historial de Movimientos</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
  </head>

  <body>
    <!-- Encabezado igual al menú principal -->
    <header class="app-header">
      <div class="container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <img src="../img/LogoHorizontal - 2.png" alt="logo" class="header-logo">
          <span class="brand-name">SETDITSX - Sindicato ITSX</span>
        </div>

        <div class="user-actions d-flex align-items-center gap-3">
          <div class="user-name">Sánchez Cortes Felipe Martin ▾</div>
          <button class="btn btn-outline-primary btn-sm">Cerrar Sesión</button>
        </div>
      </div>
    </header>

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
