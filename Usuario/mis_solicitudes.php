<?php
require_once '../includes/init.php'; 
secure_session_start(); 

// CORRECCIÓN 1: Ruta correcta (están en la misma carpeta)
require_once '..\includes\Usuario\logica_solicitudes.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Solicitudes</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/estilo.css">
    
    <style>
        body { 
            display: block !important; 
            background-color: #f8f9fa;
            height: auto !important;
        }
        .navbar-top {
            background-color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            height: 70px;
        }
        .table-custom th { background-color: #153b52; color: white; }
        .card-header-ahorro { background-color: #d18819; color: white; font-weight: bold; }
        .card-header-prestamo { background-color: #153b52; color: white; font-weight: bold; }
    </style>
</head>
<body class="pt-5"> 
    
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

    <div class="container my-5 pt-4">
        <a href="panelAhorrador.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar al Panel</a>
        
        <div class="card shadow-sm mb-5 border-0">
            <div class="card-header card-header-ahorro">
                <i class="bi bi-piggy-bank me-2"></i> Mis Solicitudes de Ahorro
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-center align-middle">
                        <thead class="table-custom">
                            <tr>
                                <th># Solicitud</th>
                                <th>Fecha</th>
                                <th>Monto Ahorro</th>
                                <th>Estado</th>
                                <th>Documento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ahorros)): ?>
                                <?php foreach ($ahorros as $row): ?>
                                    <tr>
                                        <td><strong>#<?php echo $row['id_solicitud_ahorro']; ?></strong></td>
                                        <td><?php echo date("d/m/Y", strtotime($row['fecha'])); ?></td>
                                        
                                        <td class="fw-bold text-success">$ <?php echo number_format($row['monto_solicitado'], 2); ?></td>
                                        
                                        <td>
                                            <span class="badge rounded-pill <?php echo colorEstado($row['id_estado']); ?>">
                                                <?php echo $row['estado']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['archivo_solicitud']) && $row['archivo_solicitud'] != 'GENERANDO...' && $row['archivo_solicitud'] != 'pendiente_de_generar.pdf'): ?>
                                                <a href="../uploads/solicitudes/<?php echo $row['archivo_solicitud']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">Procesando...</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-muted py-4">No has realizado solicitudes de ahorro.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header card-header-prestamo">
                <i class="bi bi-cash-coin me-2"></i> Mis Solicitudes de Préstamo
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-center align-middle">
                        <thead class="table-custom">
                            <tr>
                                <th># Solicitud</th>
                                <th>Fecha</th>
                                <th>Monto Solicitado</th>
                                <th>Total a Pagar</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($prestamos)): ?>
                                <?php foreach ($prestamos as $row): ?>
                                    <tr>
                                        <td><strong>#<?php echo $row['id_solicitud_prestamo']; ?></strong></td>
                                        <td><?php echo date("d/m/Y", strtotime($row['fecha_solicitud'])); ?></td>
                                        <td class="text-primary fw-bold">$ <?php echo number_format($row['monto_solicitado'], 2); ?></td>
                                        <td>$ <?php echo number_format($row['total_a_pagar'], 2); ?></td>
                                        <td>
                                            <span class="badge rounded-pill <?php echo colorEstado($row['id_estado']); ?>">
                                                <?php echo $row['estado']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-muted py-4">No has solicitado ningún préstamo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>