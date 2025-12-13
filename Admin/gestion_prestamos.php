<?php
// Admin/gestion_prestamos.php
require_once '../includes/init.php';

// Verificar Admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Regla Diciembre
$mesActual = 11;//intval(date('m'));
$bloqueoCierre = ($mesActual == 12);

// Consulta SQL con NUEVOS NOMBRES DE TABLAS
$sql = "SELECT s.id_solicitud_prestamo, s.monto_solicitado, s.plazo_quincenas, s.fecha_solicitud, 
               u.nombre, u.apellido_paterno, u.apellido_materno 
        FROM solicitud_prestamo s
        JOIN usuario u ON s.id_usuario = u.id_usuario
        WHERE s.id_estado = 1 ORDER BY s.fecha_solicitud ASC";

$stmt = $pdo->query($sql);
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Préstamos</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="bg-light">
    
    <?php if ($bloqueoCierre): ?>
        <div class="alert alert-warning text-center m-0 rounded-0">
            <strong><i class="bi bi-lock-fill"></i> MODO CIERRE:</strong> Aprobaciones bloqueadas hasta Enero.
        </div>
    <?php endif; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Solicitudes Pendientes</h2>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Folio</th>
                            <th>Solicitante</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Plazo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $sol): ?>
                        <tr>
                            <td>#<?php echo $sol['id_solicitud_prestamo']; ?></td>
                            <td><?php echo $sol['nombre'] . ' ' . $sol['apellido_paterno']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($sol['fecha_solicitud'])); ?></td>
                            <td class="fw-bold">$<?php echo number_format($sol['monto_solicitado'], 2); ?></td>
                            <td><?php echo $sol['plazo_quincenas']; ?> Q</td>
                            <td class="text-center">
                                <?php if ($bloqueoCierre): ?>
                                    <button class="btn btn-secondary btn-sm" disabled>🔒</button>
                                <?php else: ?>
                                    <a href="aprobar_solicitud.php?id=<?php echo $sol['id_solicitud_prestamo']; ?>" 
                                       class="btn btn-success btn-sm" onclick="return confirm('¿Aprobar y generar Pagaré?')">✅ Aprobar</a>
                                    <a href="rechazar_solicitud.php?id=<?php echo $sol['id_solicitud_prestamo']; ?>" 
                                       class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar solicitud?')">❌ Rechazar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($solicitudes)): ?>
                            <tr><td colspan="6" class="text-center py-4">No hay solicitudes.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4"><a href="Inicio.php" class="btn btn-secondary">Volver</a></div>
    </div>
</body>
</html>