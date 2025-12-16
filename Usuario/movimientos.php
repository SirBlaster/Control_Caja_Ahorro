<?php
require_once '../includes/init.php';
secure_session_start();
check_login(1); // Rol 2: Ahorrador

// Cargar la lógica de datos
require_once '../includes/Usuario/logica_mov_ahorro.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movimientos de Ahorro - SETDITSX</title>
    
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/estilo.css">
    <link rel="stylesheet" href="../css/estilo_ahorrador.css">
    <style>
.navbar.header {
    position: fixed !important;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
}

body {
    padding-top: 70px !important;
}
    </style>

</head>
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
<body class="pt-5">
    <div class="container mt-5 pt-4 mb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-0">Cuenta de Ahorro</h3>
                <p class="text-muted mb-0">Historial exclusivo de depósitos y retiros</p>
            </div>
            <a href="panelAhorrador.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Regresar
            </a>
        </div>

        <div class="card card-ahorro bg-white">
            <div class="card-header header-ahorro d-flex justify-content-between align-items-center">
                <span><i class="bi bi-piggy-bank me-2"></i> Movimientos Registrados</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase small">Fecha</th>
                            <th class="py-3 text-secondary text-uppercase small">Concepto</th>
                            <th class="py-3 text-secondary text-uppercase small text-center">Tipo</th>
                            <th class="pe-4 py-3 text-secondary text-uppercase small text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($movimientos_ahorro)): ?>
                            <?php foreach ($movimientos_ahorro as $mov): ?>
                                <?php 
                                    // Lógica visual
                                    $es_deposito = ($mov['id_tipo_movimiento'] == 1);
                                    
                                    $clase_monto = $es_deposito ? 'text-success' : 'text-danger';
                                    $signo       = $es_deposito ? '+' : '-';
                                    $clase_badge = $es_deposito ? 'badge-deposito' : 'badge-retiro';
                                    $icono       = $es_deposito ? 'bi-arrow-up-circle' : 'bi-arrow-down-circle';
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">
                                        <?php echo date("d/m/Y", strtotime($mov['fecha'])); ?>
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi <?php echo $icono; ?> <?php echo $es_deposito ? 'text-success' : 'text-danger'; ?>"></i>
                                            <?php echo htmlspecialchars($mov['concepto']); ?>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center">
                                        <span class="badge <?php echo $clase_badge; ?> rounded-pill px-3">
                                            <?php echo htmlspecialchars($mov['tipo_movimiento']); ?>
                                        </span>
                                    </td>
                                    
                                    <td class="pe-4 text-end fw-bold <?php echo $clase_monto; ?>" style="font-size: 1.1rem;">
                                        <?php echo $signo . '$' . number_format($mov['monto'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted opacity-50 mb-2">
                                        <i class="bi bi-wallet2 fs-1"></i>
                                    </div>
                                    <h6 class="text-muted">No hay movimientos de ahorro</h6>
                                    <small class="text-secondary">Aquí aparecerán tus nóminas registradas y retiros.</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>