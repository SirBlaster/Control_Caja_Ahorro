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

    <header class="header">
        <div class="brand-container">
            <img src="../img/LogoHorizontal - 2.png" alt="Logo" style="height: 50px;">
            <h4>SETDITSX</h4>
        </div>
        <div class="user-info">
            <div class="user-details">
            </div>
            <a href="../logout.php" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>
    </header>

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
