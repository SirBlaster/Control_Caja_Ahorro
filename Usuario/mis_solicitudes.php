<?php
require_once '../includes/init.php'; 
secure_session_start(); 

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
        /* FORZAR QUE EL CUERPO SEA BLOQUE (Uno abajo del otro) */
        body { 
            display: block !important; 
            background-color: #f8f9fa;
            height: auto !important;
        }

        /* Estilo de la Barra Superior */
        .navbar-top {
            background-color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            height: 70px;
        }

        /* Colores de tablas */
        .table-custom th { background-color: #153b52; color: white; }
        .card-header-ahorro { background-color: #d18819; color: white; font-weight: bold; }
        .card-header-prestamo { background-color: #153b52; color: white; font-weight: bold; }
    </style>
</head>
<body class="pt-5"> 
    <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="#">
                <img src="../img/NewLogo - 1.png" alt="logo" height="40">
                <span class="fw-bold text-dark">Historial de Solicitudes</span>
            </a>

            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-block text-end">
                    <div class="fw-bold" style="font-size: 0.9rem; color: #153b52;">
                        <?php echo get_user_name(); ?>
                    </div>
                    <small class="text-muted"><?php echo get_user_role_text(); ?></small>
                </div>
                <form action="../logout.php" method="POST" style="display: inline;">
                <button type="submit" class="btn btn-logout">
                    <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                </button>
                </form>
                
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <a href="panelAhorrador.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar</a>
    </div>
    <div class="container mt-5 pt-4"> <div class="card shadow-sm mb-5 border-0">
                
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
                                        <td><strong>#<?php echo $row['Id_SolicitudAhorro']; ?></strong></td>
                                        <td><?php echo date("d/m/Y", strtotime($row['Fecha'])); ?></td>
                                        <td class="fw-bold text-success">$ <?php echo number_format($row['Monto'], 2); ?></td>
                                        <td>
                                            <span class="badge rounded-pill <?php echo colorEstado($row['Id_Estado']); ?>">
                                                <?php echo $row['Estado']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['ArchivoSolicitud']) && $row['ArchivoSolicitud'] != 'GENERANDO...' && $row['ArchivoSolicitud'] != 'pendiente_de_generar.pdf'): ?>
                                                <a href="../uploads/solicitudes/<?php echo $row['ArchivoSolicitud']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">Procesando...</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-muted py-4">No hay solicitudes.</td></tr>
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
                                        <td><strong>#<?php echo $row['Id_SolicitudPrestamo']; ?></strong></td>
                                        <td><?php echo date("d/m/Y", strtotime($row['FechaSolicitud'])); ?></td>
                                        <td class="text-primary fw-bold">$ <?php echo number_format($row['MontoSolicitado'], 2); ?></td>
                                        <td>$ <?php echo number_format($row['Total_A_Pagar'], 2); ?></td>
                                        <td>
                                            <span class="badge rounded-pill <?php echo colorEstado($row['Id_Estado']); ?>">
                                                <?php echo $row['Estado']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-muted py-4">No hay préstamos.</td></tr>
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