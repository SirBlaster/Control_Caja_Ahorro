<?php
require_once '../includes/init.php';
secure_session_start();

// Validar Rol Admin
if (!isset($_SESSION['id_rol']) || ( $_SESSION['id_rol'] != 2)) {
    header("Location: ../login.php");
    exit();
}

// Consultar PREVISTA de quiénes se les va a descontar (Misma lógica que el procesador)
$sqlPreview = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.correo_institucional, sa.monto_solicitado, sa.fecha
                FROM usuario u
                JOIN solicitud_ahorro sa ON u.id_usuario = sa.id_usuario
                WHERE sa.id_solicitud_ahorro = (
                    SELECT MAX(id_solicitud_ahorro) 
                    FROM solicitud_ahorro 
                    WHERE id_usuario = u.id_usuario AND id_estado = 2
                )";
$stmt = $pdo->prepare($sqlPreview);
$stmt->execute();
$lista_descuentos = $stmt->fetchAll();

$total_global = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Nómina - Admin</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-wallet-fill text-primary"></i> Gestión de Descuentos Quincenales</h2>
            <a href="inicio.php" class="btn btn-secondary">Volver</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-white p-4">
                <h5 class="mb-0">Prevista del Corte Quincenal</h5>
                <p class="text-muted small mb-0">Esta tabla muestra la última cantidad aprobada de cada ahorrador activo.</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Empleado</th>
                                <th>Correo</th>
                                <th>Fecha Solicitud</th>
                                <th class="text-end pe-4">Monto a Descontar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($lista_descuentos)): ?>
                                <?php foreach($lista_descuentos as $row): ?>
                                    <?php $total_global += $row['monto_solicitado']; ?>
                                    <tr>
                                        <td class="ps-4 fw-bold">
                                            <?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido_paterno']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['correo_institucional']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></td>
                                        <td class="text-end pe-4 text-success fw-bold">
                                            $ <?php echo number_format($row['monto_solicitado'], 2); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-4">No hay ahorradores activos.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold pt-3">TOTAL A RECAUDAR ESTA QUINCENA:</td>
                                <td class="text-end pe-4 fw-bold fs-5 text-primary pt-3">
                                    $ <?php echo number_format($total_global, 2); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white p-3 text-end">
                <?php if($total_global > 0): ?>
                    <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalConfirmar">
                        <i class="bi bi-check-circle"></i> Aplicar Descuentos
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Operación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de ejecutar el descuento masivo?</p>
                    <p class="fw-bold text-danger">Esta acción registrará un ingreso en la cuenta de <?php echo count($lista_descuentos); ?> usuarios.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="procesar_nomina.php" method="POST">
                        <button type="submit" class="btn btn-success">Sí, Ejecutar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>