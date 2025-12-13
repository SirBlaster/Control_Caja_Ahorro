<?php
// =========================================================
// LÓGICA PHP (BACKEND)
// =========================================================
require_once '../includes/init.php';

// Verificar Admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    header("Location: ../login.php");
    exit();
}

// Regla Diciembre
$mesActual = intval(date('m'));
$bloqueoCierre = ($mesActual == 12);

// Consulta SQL
$sql = "SELECT s.id_solicitud_prestamo, s.monto_solicitado, s.plazo_quincenas, s.fecha_solicitud, 
               u.nombre, u.apellido_paterno, u.apellido_materno 
        FROM solicitud_prestamo s
        JOIN usuario u ON s.id_usuario = u.id_usuario
        WHERE s.id_estado = 1 ORDER BY s.fecha_solicitud DESC";

$stmt = $pdo->query($sql);
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nombre del admin para el header
$nombreAdmin = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Administrador';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Préstamos - SETDITSX</title>

    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css" />

    <style>
    body {
        background: #f5f7fa;
        font-family: Arial, sans-serif;
    }

    .header {
        background: linear-gradient(to bottom, #ffffff, #e8edf5);
        padding: 20px 40px;
        border-bottom: 2px solid #2a3472;
    }

    .header h4 {
        color: #2a3472;
        font-weight: bold;
    }

    .card-form {
        max-width: 1200px;
        margin: 40px auto;
        background: white;
        border-radius: 12px;
        padding: 30px 40px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        font-weight: bold;
        color: #2a3472;
        margin-bottom: 25px;
    }

    .section-title {
        font-weight: bold;
        color: #2a3472;
        margin-top: 20px;
        margin-bottom: 15px;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-details {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .user-name {
        font-weight: 500;
        margin: 0;
        color: #2a3472;
    }

    .user-icon {
        font-size: 1.8rem;
        color: #2a3472;
    }

    .btn-logout {
        border: 1px solid #dc3545;
        color: #dc3545;
        background: none;
        padding: 5px 10px;
        border-radius: 5px;
        transition: 0.3s;
    }

    .btn-logout:hover {
        background: #dc3545;
        color: white;
    }

    /* TABLA */
    .table-container {
        overflow-x: auto;
        margin-top: 20px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background-color: #2a3472;
        color: white;
        font-weight: 600;
        padding: 12px 15px;
        text-align: left;
    }

    .table td {
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .table tr:hover {
        background-color: #f8f9fa;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* BOTONES DE ACCIÓN */
    .btn-action {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        border: none;
        display: inline-block;
        margin-right: 5px;
    }

    .btn-approve {
        background-color: #28a745;
        color: white;
    }

    .btn-approve:hover {
        background-color: #218838;
        color: white;
    }

    .btn-reject {
        background-color: #dc3545;
        color: white;
    }

    .btn-reject:hover {
        background-color: #c82333;
        color: white;
    }

    .btn-back {
        color: #2a3472;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 20px;
    }

    .btn-back:hover {
        text-decoration: underline;
        color: #1e2660;
    }
    </style>
</head>

<body>

    <div class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="../img/NewLogo - 1.png" alt="SETDITSX" width="70" class="me-3" />
            <h4 class="mb-0">SETDITSX - Sindicato ITSX</h4>
        </div>

        <div class="user-info">
            <i class="bi bi-person-square user-icon"></i>
            <div class="user-details">
                <p class="user-name"><?php echo htmlspecialchars($nombreAdmin); ?></p>
            </div>
            <a href="../logout.php" class="btn btn-logout text-decoration-none">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>
    </div>

    <div class="card-form">

        <?php if ($bloqueoCierre): ?>
        <div class="alert alert-warning text-center fw-bold shadow-sm">
            <i class="bi bi-lock-fill"></i> MODO CIERRE DE CAJA ACTIVADO (DICIEMBRE)
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <h2>Gestión de Préstamos</h2>

        <h5 class="section-title">Solicitudes pendientes de revisión</h5>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Solicitante</th>
                        <th>Fecha Solicitud</th>
                        <th>Monto</th>
                        <th>Plazo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($solicitudes)): ?>
                    <?php foreach ($solicitudes as $sol): ?>
                    <tr>
                        <td><strong>#<?php echo $sol['id_solicitud_prestamo']; ?></strong></td>
                        <td><?php echo $sol['nombre'] . ' ' . $sol['apellido_paterno']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($sol['fecha_solicitud'])); ?></td>
                        <td class="fw-bold text-dark">$<?php echo number_format($sol['monto_solicitado'], 2); ?></td>
                        <td><?php echo $sol['plazo_quincenas']; ?> Q</td>
                        <td><span class="status-pending">Pendiente</span></td>
                        <td>
                            <?php if ($bloqueoCierre): ?>
                            <button class="btn btn-secondary btn-sm" disabled title="Bloqueado por cierre">
                                <i class="bi bi-lock"></i>
                            </button>
                            <?php else: ?>
                            <a href="aprobar_solicitud.php?id=<?php echo $sol['id_solicitud_prestamo']; ?>"
                                class="btn-action btn-approve"
                                onclick="return confirm('¿Confirmar aprobación y generar pagaré?')">
                                <i class="bi bi-check-lg"></i> Aprobar
                            </a>
                            <a href="rechazar_solicitud.php?id=<?php echo $sol['id_solicitud_prestamo']; ?>"
                                class="btn-action btn-reject"
                                onclick="return confirm('¿Seguro que deseas rechazar y eliminar esta solicitud?')">
                                <i class="bi bi-x-lg"></i> Rechazar
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            No hay solicitudes pendientes por el momento.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="Inicio.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Volver al menú principal
        </a>
    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>