<?php
// Usuario/panelAhorrador.php
require_once '../includes/init.php';

// 1. SEGURIDAD: Verificar sesión y ROL (1 = Ahorrador)
secure_session_start();
check_login(1); // Si no es rol 1, va pa' fuera.

require_once '../includes/Usuario/logica_panel.php'; // Si usas este archivo externo, asegúrate que no choque variables.

// --- RECARGAMOS DATOS LOCALMENTE PARA ASEGURARNOS QUE ESTÉN FRESCOS ---
$id_usuario = $_SESSION['id_usuario'];
$nombreUsuario = get_user_name(); 

// A. Obtener Saldo Ahorro
$stmt = $pdo->prepare("SELECT monto_ahorrado FROM ahorro WHERE id_usuario = :id");
$stmt->execute([':id' => $id_usuario]);
$ahorro = $stmt->fetch(PDO::FETCH_ASSOC);
$saldo_total = $ahorro ? $ahorro['monto_ahorrado'] : 0.00;

// B. CONSULTA CORREGIDA: BUSCAR PRÉSTAMO (PENDIENTE O ACTIVO)
// Buscamos estado 1 (Pendiente) o 2 (Aprobado). Ordenamos DESC para priorizar el más reciente.
$sqlPrestamo = "SELECT * FROM solicitud_prestamo 
                WHERE id_usuario = :id AND id_estado IN (1, 2) 
                ORDER BY id_estado DESC LIMIT 1";
$stmtPrestamo = $pdo->prepare($sqlPrestamo);
$stmtPrestamo->execute([':id' => $id_usuario]);
$miPrestamo = $stmtPrestamo->fetch(PDO::FETCH_ASSOC);

// Variables de control para la vista
$tieneSolicitud = false;
$esPendiente = false;
$montoMostrar = 0;
$textoEstado = "Sin préstamo activo";
$claseEstado = "text-muted"; // Gris por defecto

if ($miPrestamo) {
    $tieneSolicitud = true;
    $montoMostrar = $miPrestamo['total_a_pagar']; // Mostramos la deuda total proyectada
    
    if ($miPrestamo['id_estado'] == 1) {
        // CASO: PENDIENTE
        $esPendiente = true;
        $textoEstado = "Solicitud en Revisión";
        $claseEstado = "text-primary"; // Azul
        $montoMostrar = $miPrestamo['total_a_pagar']; // Deuda futura
    } elseif ($miPrestamo['id_estado'] == 2) {
        // CASO: ACTIVO
        $textoEstado = "Saldo Pendiente";
        $claseEstado = "text-warning"; // Amarillo/Dorado
        $montoMostrar = $miPrestamo['saldo_pendiente']; // Lo que debe hoy
    }
}

// C. Movimientos recientes
$stmtMovs = $pdo->prepare("
    SELECT m.*, tm.tipo_movimiento as Tipo 
    FROM movimiento m 
    JOIN tipo_movimiento tm ON m.id_tipo_movimiento = tm.id_tipo_movimiento 
    WHERE m.id_usuario = :id 
    ORDER BY m.fecha DESC LIMIT 5
");
$stmtMovs->execute([':id' => $id_usuario]);
$movimientos = $stmtMovs->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Panel - Ahorrador</title>

    <link rel="stylesheet" href="../css/estilo_ahorrador.css">
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/Bootstrap-icons/font/Bootstrap-icons.min.css">
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

  <?php if (isset($_GET['msg'])): ?>
    <div class="container mt-4">
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Aviso:</strong> <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
  <?php endif; ?>


  <div style="color: #1a237e;" class="alert d-flex align-items-center" role="alert">
    <div>
      El porcentaje de la caja se asigna al corte de la caja, despues del 30 de noviembre de cada año.
    </div>
  </div>
  <div class="main-container">

    
    <div class="dashboard-cards">
      <div class="info-card">
         <h6 class="card-label">CAJA DE AHORRO</h6>
         <div class="card-amount amount-success">
             $ <?php echo number_format($saldo_total, 2); ?> <span class="fs-6 text-muted">MXN</span>
             <h6 class="card-label">Rendimiento:</h6>
             $ <?php echo number_format($porcentaje_Rendimiento); ?> <span class="fs-6 text-muted">%</span>
         </div>
         <div class="text-muted small">Saldo total disponible</div>
        <a href="movimientos.php" class="btn btn-outline-primary mt-3 btn-sm w-100">Ver movimientos</a>
        </div>

      <div class="info-card" style="<?php echo $esPendiente ? 'border: 1px solid #0d6efd;' : ''; ?>">
         <h6 class="card-label">
             <?php echo $esPendiente ? 'ESTADO SOLICITUD' : 'PRÉSTAMO ACTIVO'; ?>
         </h6>

         <div class="card-amount <?php echo $esPendiente ? 'text-primary' : 'amount-warning'; ?>">
             $ <?php echo number_format($montoMostrar, 2); ?> <span class="fs-6 text-muted">MXN</span>
         </div>
         
         <div class="small fw-bold <?php echo $claseEstado; ?>">
             <?php echo $textoEstado; ?>
         </div>

         <?php if ($tieneSolicitud): ?>
             <a href="Estado_Prestamo.php" class="btn btn-outline-primary mt-3 btn-sm w-100">
                 <?php echo $esPendiente ? 'Ver estado de revisión' : 'Consultar detalles'; ?>
             </a>
         <?php else: ?>
             <button class="btn btn-light mt-3 btn-sm w-100" disabled>Sin préstamo activo</button>
         <?php endif; ?>
      </div>

    </div>

    <section class="mb-5">
      <h6 class="section-title">ACCIONES RÁPIDAS</h6>
      <div class="actions-container justify-content-center">
         
         <?php if (!$tieneSolicitud): ?>
             <a href="solicitud_prestamo.php" class="btn-custom btn-gold text-decoration-none">
                 <i class="bi bi-cash-stack"></i> Solicitar nuevo préstamo
             </a>
         <?php else: ?>
             <button class="btn-custom btn-cancel text-decoration-none" disabled style="opacity: 0.6; cursor: not-allowed;">
                 <i class="bi bi-lock-fill"></i> Solicitud en curso
             </button>
         <?php endif; ?>

         <a href="registrahorro.php" class="btn-custom btn-gold text-decoration-none">
             <i class="bi bi-piggy-bank"></i> Registrar Ahorro / Nómina
         </a>
         <a href="mis_solicitudes.php" class="btn-custom btn-gold text-decoration-none">
             <i class="bi bi-clock-history"></i> Ver Historial Solicitudes
         </a>
      </div>
    </section>

    <section>
       <h6 class="section-title">ÚLTIMOS MOVIMIENTOS</h6>
       <div class="table-container">
           <table class="table">
               <thead>
                   <tr>
                       <th>Fecha</th>
                       <th>Concepto</th>
                       <th>Tipo</th>
                       <th class="text-end">Monto</th>
                   </tr>
               </thead>
               <tbody>
                   <?php if (!empty($movimientos)): ?>
                       <?php foreach ($movimientos as $mov): 
                           $es_ingreso = ($mov['Id_TipoMovimiento'] == 1); 
                           $clase_monto = $es_ingreso ? 'text-success' : 'text-danger';
                           $signo = $es_ingreso ? '+' : '-';
                           $badge_bg = $es_ingreso ? 'bg-success' : 'bg-warning text-dark';
                       ?>
                       <tr>
                           <td><?php echo date("d/m/Y", strtotime($mov['Fecha'])); ?></td>
                           <td><?php echo htmlspecialchars($mov['Concepto']); ?></td>
                           <td><span class="badge <?php echo $badge_bg; ?>"><?php echo htmlspecialchars($mov['Tipo']); ?></span></td>
                           <td class="text-end fw-bold <?php echo $clase_monto; ?>">
                               <?php echo $signo . '$' . number_format($mov['Monto'], 2); ?>
                           </td>
                       </tr>
                       <?php endforeach; ?>
                   <?php else: ?>
                       <tr><td colspan="4" class="text-center py-4 text-muted">No hay movimientos recientes.</td></tr>
                   <?php endif; ?>
               </tbody>
           </table>
       </div>
       <div class="mt-3 text-end">
          <a href="historial_completo.php" class="text-primary fw-bold text-decoration-none"> Ver historial completo &rarr;</a>
       </div>
    </section>

  </div>

  <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>