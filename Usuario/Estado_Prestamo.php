<?php

// 1. LÓGICA PHP (BACKEND)
require_once '../includes/init.php';
secure_session_start();

// Validar que sea Ahorrador (Rol 1)
check_login(1); 

$id_usuario = $_SESSION['id_usuario'];
$nombreUsuario = get_user_name(); 

// --- CONSULTA PRINCIPAL: BUSCAR PRÉSTAMO (Activo o Pendiente) ---
$sql = "SELECT * FROM solicitud_prestamo 
        WHERE id_usuario = :id AND id_estado IN (1, 2) 
        ORDER BY id_estado DESC LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_usuario]);
$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

// Variables por defecto
$existeSolicitud = false;
$esActivo = false;
$esPendiente = false;
$archivoPagare = ""; // Variable para la ruta del PDF

// Datos para mostrar
$montoSolicitado = 0;
$totalAPagar = 0;
$saldoPendiente = 0;
$pagado = 0;
$progreso = 0;
$fechaSolicitud = "";
$plazo = 0;

if ($prestamo) {
    $existeSolicitud = true;
    
    // Asignar valores
    $montoSolicitado = floatval($prestamo['monto_solicitado']); 
    $totalAPagar = floatval($prestamo['total_a_pagar']);      
    $saldoPendiente = floatval($prestamo['saldo_pendiente']);  
    $plazo = intval($prestamo['plazo_quincenas']);
    $fechaSolicitud = date('d/m/Y', strtotime($prestamo['fecha_solicitud']));
    
    // Recuperamos la ruta del pagaré
    $archivoPagare = $prestamo['archivo_pagare']; 

    // Determinar estado
    if ($prestamo['id_estado'] == 2) {
        $esActivo = true; // Aprobado
        $pagado = $totalAPagar - $saldoPendiente;
        if ($totalAPagar > 0) {
            $progreso = ($pagado / $totalAPagar) * 100;
        }
    } elseif ($prestamo['id_estado'] == 1) {
        $esPendiente = true; // En revisión
    }
}

// --- CONSULTA SECUNDARIA: HISTORIAL DE PAGOS ---
$historialPagos = [];
if ($esActivo) {
    $sqlPagos = "SELECT * FROM movimiento 
                 WHERE id_usuario = :id AND id_tipo_movimiento = 3 
                 ORDER BY fecha DESC";
    $stmtPagos = $pdo->prepare($sqlPagos);
    $stmtPagos->execute([':id' => $id_usuario]);
    $historialPagos = $stmtPagos->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Estado de Préstamo - Ahorrador</title>

  <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="../css/Bootstrap-icons/font/Bootstrap-icons.min.css">
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

  <div class="main-container">
      
      <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
          <h2 class="page-title m-0 text-start fs-3">Estado de mi Préstamo</h2>
          <a href="panelAhorrador.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
              <i class="bi bi-arrow-left"></i> Volver al Panel
          </a>
      </div>

      <?php if ($esActivo): ?>
          
          <div class="dashboard-cards mb-4">
              <div class="info-card text-center">
                  <h6 class="card-label">TOTAL A PAGAR (DEUDA)</h6>
                  <div class="card-amount" style="color: var(--primary-blue);">
                      $ <?php echo number_format($totalAPagar, 2); ?>
                  </div>
                  <div class="text-muted small">Capital + Interés 30%</div>
              </div>

              <div class="info-card text-center" style="border: 1px solid #ffc107;">
                  <h6 class="card-label text-warning">SALDO PENDIENTE</h6>
                  <div class="card-amount amount-warning">
                      $ <?php echo number_format($saldoPendiente, 2); ?>
                  </div>
                  <div class="text-muted small fw-bold">Falta por liquidar</div>
              </div>

              <div class="info-card text-center">
                  <h6 class="card-label">MONTO PAGADO</h6>
                  <div class="card-amount amount-success">
                      $ <?php echo number_format($pagado, 2); ?>
                  </div>
                  <div class="text-muted small">Abonado vía nómina</div>
              </div>
          </div>

          <?php if (!empty($archivoPagare)): ?>
            <div class="text-center mb-4">
                <a href="../<?php echo htmlspecialchars($archivoPagare); ?>" target="_blank" 
                   class="btn btn-danger btn-custom d-inline-flex align-items-center gap-2 shadow-sm px-4">
                    <i class="bi bi-file-earmark-pdf-fill fs-5"></i> Descargar mi Pagaré (PDF)
                </a>
            </div>
          <?php endif; ?>

          <div class="card p-4 border-0 shadow-sm mb-5">
              <h6 class="form-label mb-3">Progreso de liquidación</h6>
              <div class="progress" style="height: 25px;">
                  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                       role="progressbar" 
                       style="width: <?php echo $progreso; ?>%">
                       <?php echo round($progreso); ?>%
                  </div>
              </div>
          </div>

          <section>
              <h6 class="section-title">Historial de Pagos</h6>
              <div class="table-container">
                  <table class="table">
                      <thead>
                          <tr>
                              <th>Fecha</th>
                              <th>Concepto</th>
                              <th class="text-end">Monto Abonado</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php if (!empty($historialPagos)): ?>
                              <?php foreach ($historialPagos as $pago): ?>
                              <tr>
                                  <td class="fw-bold text-muted">
                                      <?php echo date('d/m/Y', strtotime($pago['fecha'])); ?>
                                  </td>
                                  <td><?php echo htmlspecialchars($pago['concepto']); ?></td>
                                  <td class="text-end fw-bold text-success">
                                      - $ <?php echo number_format($pago['monto'], 2); ?>
                                  </td>
                              </tr>
                              <?php endforeach; ?>
                          <?php else: ?>
                              <tr>
                                  <td colspan="3" class="text-center py-5 text-muted">
                                      <i class="bi bi-clock-history fs-1 mb-2"></i><br>
                                      Aún no se registran pagos para este préstamo.
                                  </td>
                              </tr>
                          <?php endif; ?>
                      </tbody>
                  </table>
              </div>
          </section>

      <?php elseif ($esPendiente): ?>
          
          <div class="alert alert-warning shadow-sm p-4 rounded-3 border-0">
              <div class="d-flex align-items-center gap-3 mb-3">
                  <i class="bi bi-hourglass-split fs-1 text-warning"></i>
                  <div>
                      <h4 class="alert-heading fw-bold m-0">Solicitud en Revisión</h4>
                      <p class="mb-0">El administrador aún no aprueba tu préstamo.</p>
                  </div>
              </div>
              <hr>
              <p class="mb-2">Detalles de tu solicitud:</p>
              
              <div class="row g-3">
                  <div class="col-md-4">
                      <div class="p-3 bg-white rounded border">
                          <small class="text-muted d-block fw-bold">MONTO SOLICITADO</small>
                          <span class="fs-4 fw-bold text-dark">$ <?php echo number_format($montoSolicitado, 2); ?></span>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="p-3 bg-white rounded border border-warning">
                          <small class="text-muted d-block fw-bold">TOTAL A PAGAR (30% INT)</small>
                          <span class="fs-4 fw-bold text-primary">$ <?php echo number_format($totalAPagar, 2); ?></span>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="p-3 bg-white rounded border">
                          <small class="text-muted d-block fw-bold">PLAZO</small>
                          <span class="fs-4 fw-bold text-dark"><?php echo $plazo; ?> Quincenas</span>
                      </div>
                  </div>
              </div>
              
              <div class="mt-4 text-end">
                  <button class="btn btn-secondary disabled">Esperando aprobación...</button>
              </div>
          </div>

      <?php else: ?>
          
          <div class="card-form text-center py-5">
              <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
              <h3 class="mt-3 text-primary fw-bold">¡Sin deudas activas!</h3>
              <p class="text-muted mb-4">No tienes préstamos activos ni solicitudes pendientes.</p>
              <a href="solicitud_prestamo.php" class="btn btn-gold btn-custom d-inline-flex">
                  Solicitar Préstamo Ahora
              </a>
          </div>

      <?php endif; ?>

  </div>

  <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>