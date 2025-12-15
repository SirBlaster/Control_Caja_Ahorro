<?php
require_once '../includes/init.php';
secure_session_start();
require_once '../includes/Usuario/logica_panel.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Panel - Ahorrador</title>

  <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="../css/Bootstrap-icons/font/Bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/estilo_ahorrador.css">
</head>
<body>
  <?php if (isset($_GET['msg'])): ?>
    <div class="container mt-4">
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-left: 5px solid #28a745;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                <div>
                    <strong>Aviso del sistema</strong><br>
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="container mt-4">
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 5px solid #dc3545;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div>
                    <strong>Error</strong><br>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

    <!-- Image and text -->
  
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


    <br>
    <br>
    <br>
    <main class="container main-area mt-4">
    
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <div class="info-card p-4 shadow-sm">
          <h6 class="card-sub">CAJA DE AHORRO</h6>
          <div class="card-amount text-amount-success">
              $ <?php echo number_format($saldo_total ?? 0, 2); ?> <span class="fs-6 text-muted">MXN</span>
          </div>
          <div class="muted">Saldo total disponible</div>
          <a href="movimientos.php" class="btn btn-outline-primary mt-3 btn-sm"> Ver movimientos </a>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card p-4 shadow-sm">
          <h6 class="card-sub">PRÉSTAMO ACTIVO</h6>
          <div class="card-amount loan-amount">$15,000.00 MXN</div>
          <div class="muted">Saldo pendiente</div>
          <a href="Estado_Prestamo.php" class="btn btn-outline-primary mt-3 btn-sm"> Consultar estado de préstamo</a>
        </div>
      </div>
    </div>
    

    <section class="actions mb-4">
      <h6 class="section-title">ACCIONES DEL AHORRADOR</h6>
        <div class="d-flex justify-content-center gap-3 flex-wrap my-3">
          <a href="solicitud_prestamo.php" class="btn btn-warning px-4 rounded-pill"> Solicitar nuevo préstamo </a>
          <a href="registrahorro.php" class="btn btn-warning px-4 rounded-pill"> Solicitar ahorro / Registrar nómina </a>
          <a href="mis_solicitudes.php" class="btn btn-warning px-4 rounded-pill"> Consultar el estado de las solicitudes </a>
        </div>

    </section>

    <section class="movements">
      <h6 class="section-title">ÚLTIMOS MOVIMIENTOS - CAJA DE AHORRO</h6>

      <div class="table-wrap mt-2">
        <tbody class="table-body">
                    <?php if (!empty($movimientos)): ?>
                        <?php foreach ($movimientos as $mov): ?>
                            <?php 
                                // Determinar estilos según si es depósito (1) o retiro (2)
                                $es_ingreso = ($mov['Id_TipoMovimiento'] == 1); 
                                $color_texto = $es_ingreso ? 'text-success' : 'text-danger';
                                $signo = $es_ingreso ? '+' : '-';
                                $badge_bg = $es_ingreso ? 'bg-success' : 'bg-warning text-dark';
                            ?>
                            <tr>
                                <td class="ps-4 text-muted fw-bold">
                                    <?php echo date("d/m/Y", strtotime($mov['Fecha'])); ?>
                                </td>
                                <td><?php echo htmlspecialchars($mov['Concepto']); ?></td>
                                <td>
                                    <span class="badge badge-tipo <?php echo $badge_bg; ?>">
                                        <?php echo htmlspecialchars($mov['Tipo']); ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold <?php echo $color_texto; ?>">
                                    <?php echo $signo . '$' . number_format($mov['Monto'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                No hay movimientos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
        <div class="mt-2">
          <a href="historial_completo.php" class="text-primary fw-bold"> Ver historial completo</a>
        </div>

      </div>
    </section>
  </main>

<script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
