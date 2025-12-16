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
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css"> 
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

    <main class="container mt-4">
      <h4 class="mb-3 fw-bold">Historial completo de movimientos</h4>

      <!-- BOTÓN REGRESAR -->
      <a href="panelAhorrador.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar</a>

      <!-- TABLA COMPLETA -->
<div class="container mt-5 pt-4 mb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark mb-0">Movimientos Generales</h2>
            <a href="historial_completo.php" class="btn btn-primary btn-sm rounded-pill px-4">
                <i class="bi bi-calculator"></i> Ver con Saldos
            </a>
        </div>

        <div class="card card-custom">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Descripción / Concepto</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-end pe-4">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($lista_movimientos)): ?>
                            <?php foreach ($lista_movimientos as $mov): ?>
                                <?php 
                                    // Lógica visual: ID 1 es Depósito (Verde), el resto (Retiro/Pago) es Rojo
                                    $es_ingreso = ($mov['id_tipo_movimiento'] == 1);
                                    
                                    $clase_texto = $es_ingreso ? 'text-success' : 'text-danger';
                                    $signo       = $es_ingreso ? '+' : '-';
                                    
                                    // Color de la etiqueta (Badge)
                                    $clase_badge = 'bg-secondary';
                                    if($mov['id_tipo_movimiento'] == 1) $clase_badge = 'bg-success'; // Depósito
                                    if($mov['id_tipo_movimiento'] == 2) $clase_badge = 'bg-warning text-dark'; // Retiro
                                    if($mov['id_tipo_movimiento'] == 3) $clase_badge = 'bg-info text-dark'; // Pago
                                ?>
                                <tr>
                                    <td class="ps-4 text-muted fw-bold">
                                        <?php echo date("d/m/Y", strtotime($mov['fecha'])); ?>
                                        <br>
                                        <small class="fw-normal"><?php echo date("h:i A", strtotime($mov['fecha'])); ?></small>
                                    </td>
                                    
                                    <td class="fw-bold text-dark">
                                        <?php echo htmlspecialchars($mov['concepto']); ?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <span class="badge badge-tipo <?php echo $clase_badge; ?>">
                                            <?php echo htmlspecialchars($mov['tipo_movimiento']); ?>
                                        </span>
                                    </td>
                                    
                                    <td class="text-end pe-4 fw-bold <?php echo $clase_texto; ?>" style="font-size: 1.1rem;">
                                        <?php echo $signo . '$' . number_format($mov['monto'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <div class="my-3">
                                        <i class="bi bi-receipt fs-1 text-secondary opacity-50"></i>
                                    </div>
                                    <h5>Sin movimientos registrados</h5>
                                    <p class="small">Aún no tienes actividad en tu cuenta.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <small class="text-muted">Mostrando todos los movimientos registrados en el sistema.</small>
        </div>

    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>