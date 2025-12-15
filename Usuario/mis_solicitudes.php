<?php
require_once '../includes/init.php'; 
secure_session_start();
check_login(1); // Rol 1 = Ahorrador

$id_usuario = $_SESSION['id_usuario'];

// --- 1. FUNCIÓN HELPER PARA COLORES Y TEXTOS (Local) ---
function obtenerDetallesEstado($id_estado) {
    switch ($id_estado) {
        case 1: return ['texto' => 'Pendiente', 'clase' => 'bg-warning text-dark'];
        case 2: return ['texto' => 'Aprobado',  'clase' => 'bg-success'];
        case 3: return ['texto' => 'Rechazado', 'clase' => 'bg-danger'];
        default: return ['texto' => 'Desconocido', 'clase' => 'bg-secondary'];
    }
}

// --- 2. CONSULTA DE AHORROS (TODOS LOS ESTADOS) ---
// Obtenemos todas las solicitudes sin filtrar por estado (para ver historial completo)
$sqlAhorro = "SELECT * FROM solicitud_ahorro 
              WHERE id_usuario = :id 
              ORDER BY fecha DESC";
$stmtA = $pdo->prepare($sqlAhorro);
$stmtA->execute([':id' => $id_usuario]);
$ahorros = $stmtA->fetchAll(PDO::FETCH_ASSOC);

// --- 3. CONSULTA DE PRÉSTAMOS (TODOS LOS ESTADOS) ---
$sqlPrestamo = "SELECT * FROM solicitud_prestamo 
                WHERE id_usuario = :id 
                ORDER BY fecha_solicitud DESC";
$stmtP = $pdo->prepare($sqlPrestamo);
$stmtP->execute([':id' => $id_usuario]);
$prestamos = $stmtP->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Solicitudes</title>
    
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/estilo_ahorrador.css">
    
    <style>
        body { 
            display: block !important; 
            background-color: #f8f9fa;
            height: auto !important;
        }
        .table-custom th { background-color: #153b52; color: white; }
        .card-header-ahorro { background-color: #d18819; color: white; font-weight: bold; }
        .card-header-prestamo { background-color: #153b52; color: white; font-weight: bold; }
        .badge { font-size: 0.85rem; padding: 0.5em 0.8em; }
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

    <div class="container my-5 pt-4">
        <a href="panelAhorrador.php" class="btn btn-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Regresar al Panel
        </a>
        
        <div class="card shadow-sm mb-5 border-0">
            <div class="card-header card-header-ahorro">
                <i class="bi bi-piggy-bank me-2"></i> Historial de Solicitudes de Ahorro
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
                                <?php foreach ($ahorros as $row): 
                                    $estadoData = obtenerDetallesEstado($row['id_estado']);
                                ?>
                                    <tr>
                                        <td><strong>#<?php echo $row['id_solicitud_ahorro']; ?></strong></td>
                                        <td><?php echo date("d/m/Y", strtotime($row['fecha'])); ?></td>
                                        
                                        <td class="fw-bold text-success">$ <?php echo number_format($row['monto_solicitado'], 2); ?></td>
                                        
                                        <td>
                                            <span class="badge rounded-pill <?php echo $estadoData['clase']; ?>">
                                                <?php echo $estadoData['texto']; ?>
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <?php if ($row['id_estado'] == 3): ?>
                                                <span class="text-danger small fw-bold">
                                                    <i class="bi bi-x-circle"></i> Cancelado
                                                </span>
                                            <?php elseif (!empty($row['archivo_solicitud']) && $row['archivo_solicitud'] != 'GENERANDO...' && $row['archivo_solicitud'] != 'pendiente_de_generar.pdf'): ?>
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
                                <tr><td colspan="5" class="text-muted py-4">No hay historial de solicitudes de ahorro.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header card-header-prestamo">
                <i class="bi bi-cash-coin me-2"></i> Historial de Solicitudes de Préstamo
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
                                <?php foreach ($prestamos as $row): 
                                    $estadoData = obtenerDetallesEstado($row['id_estado']);
                                ?>
                                    <tr>
                                        <td><strong>#<?php echo $row['id_solicitud_prestamo']; ?></strong></td>
                                        <td><?php echo date("d/m/Y", strtotime($row['fecha_solicitud'])); ?></td>
                                        <td class="text-primary fw-bold">$ <?php echo number_format($row['monto_solicitado'], 2); ?></td>
                                        <td>$ <?php echo number_format($row['total_a_pagar'], 2); ?></td>
                                        
                                        <td>
                                            <span class="badge rounded-pill <?php echo $estadoData['clase']; ?>">
                                                <?php echo $estadoData['texto']; ?>
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