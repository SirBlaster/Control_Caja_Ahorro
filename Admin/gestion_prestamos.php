<?php
// Admin/gestion_prestamos.php
require_once '../includes/init.php';

// Verificar Admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    header("Location: ../login.php");
    exit();
}

// Regla Diciembre (Bloqueo solo para préstamos, pero lo dejamos como variable global)
$mesActual = intval(date('m'));
$bloqueoCierre = ($mesActual == 12);

// FILTRO: ¿Qué quiere ver el admin? (prestamo, ahorro, todos)
$filtroTipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos';

// CONSULTA UNIFICADA (UNION)
// Usamos alias 'tipo_solicitud' para diferenciar: 'prestamo' o 'ahorro'
// Estado 1 = Pendiente
$sql = "
    SELECT 
        'prestamo' as tipo_solicitud,
        s.id_solicitud_prestamo as id,
        s.monto_solicitado as monto,
        s.plazo_quincenas as plazo,
        s.fecha_solicitud as fecha,
        s.id_estado,
        u.nombre, u.apellido_paterno, u.apellido_materno, u.rfc
    FROM solicitud_prestamo s
    JOIN usuario u ON s.id_usuario = u.id_usuario
    WHERE s.id_estado = 1

    UNION ALL

    SELECT 
        'ahorro' as tipo_solicitud,
        a.id_solicitud_ahorro as id,
        a.monto_solicitado as monto,
        0 as plazo,  -- Ahorro no tiene plazo en quincenas
        a.fecha as fecha,
        a.id_estado,
        u.nombre, u.apellido_paterno, u.apellido_materno, u.rfc
    FROM solicitud_ahorro a
    JOIN usuario u ON a.id_usuario = u.id_usuario
    WHERE a.id_estado = 1
";

// Aplicar ordenamiento
$sql .= " ORDER BY fecha DESC";

$stmt = $pdo->query($sql);
$todasLasSolicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrar array en PHP según lo seleccionado (más sencillo que modificar el SQL dinámico complejo)
$solicitudesFiltradas = [];
foreach ($todasLasSolicitudes as $sol) {
    if ($filtroTipo == 'todos' || $sol['tipo_solicitud'] == $filtroTipo) {
        $solicitudesFiltradas[] = $sol;
    }
}

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
    <link rel="stylesheet" href="../css/admin.css" />

    <style>
        .badge-prestamo { background-color: #ffc107; color: #000; } /* Amarillo */
        .badge-ahorro { background-color: #0d6efd; color: #fff; }   /* Azul */
    </style>

</head>

<body>

    <!-- HEADER -->
    <div class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="../img/LogoChico.png" alt="SETDITSX" width="70" class="me-3" />
            <h4 class="mb-0">SETDITSX - Sindicato ITSX</h4>
        </div>

        <div class="user-info">
            <i class="bi bi-person-square user-icon"></i>

            <div class="user-details">
                <p class="user-name">
                    <?php echo htmlspecialchars(get_user_name()); ?>
                </p>
                <small class="text-muted">
                    <?php echo htmlspecialchars(get_user_role_text()); ?>
                </small>
            </div>

            <!-- CERRAR SESIÓN -->
            <form action="../logout.php" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-logout" onclick="return confirm('¿Deseas cerrar sesión?')">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <div class="card-form">
        <a href="Inicio.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Volver al menú principal
        </a>
        <?php if ($bloqueoCierre): ?>
        <div class="alert alert-warning text-center fw-bold shadow-sm">
            <i class="bi bi-lock-fill"></i> CIERRE DE CAJA ACTIVADO
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold" style="color: #2a3472;">Gestión de Solicitudes</h3>
            
            <form method="GET" class="d-flex gap-2 align-items-center">
                <label class="fw-bold">Ver:</label>
                <select name="tipo" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 150px;">
                    <option value="todos" <?php echo $filtroTipo == 'todos' ? 'selected' : ''; ?>>Todos</option>
                    <option value="prestamo" <?php echo $filtroTipo == 'prestamo' ? 'selected' : ''; ?>>Préstamos</option>
                    <option value="ahorro" <?php echo $filtroTipo == 'ahorro' ? 'selected' : ''; ?>>Ahorros</option>
                </select>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>RFC</th>
                        <th>Tipo Movimiento</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Plazo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($solicitudesFiltradas)): ?>
                        <?php foreach ($solicitudesFiltradas as $sol): 
                            $esPrestamo = ($sol['tipo_solicitud'] == 'prestamo');
                            $etiqueta = $esPrestamo ? 'PRÉSTAMO' : 'AHORRO';
                            $claseBadge = $esPrestamo ? 'badge-prestamo' : 'badge-ahorro';
                            // URL para aprobar/rechazar enviando ID y TIPO
                            $urlAprobar = "./aprobar_solicitud.php?id=" . $sol['id'] . "&tipo=" . $sol['tipo_solicitud'];
                            $urlRechazar = "./rechazar_solicitud.php?id=" . $sol['id'] . "&tipo=" . $sol['tipo_solicitud'];
                        ?>
                        <tr>
                            <td><strong>#<?php echo $sol['id']; ?></strong></td>
                            <td><?php echo $sol['nombre'] . ' ' . $sol['apellido_paterno']; ?></td>
                            <td><?php echo $sol['rfc']; ?></td>
                            <td><span class="badge <?php echo $claseBadge; ?>"><?php echo $etiqueta; ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($sol['fecha'])); ?></td>
                            <td class="fw-bold text-success">$<?php echo number_format($sol['monto'], 2); ?></td>
                            <td><?php echo $esPrestamo ? $sol['plazo'] . ' Q' : 'N/A'; ?></td>
                            <td><span class="badge bg-secondary">Pendiente</span></td>
                            <td>
                                <?php if ($esPrestamo && $bloqueoCierre): ?>
                                    <button class="btn btn-secondary btn-sm" disabled title="Préstamos cerrados en Dic">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                <?php else: ?>
                                    <a href="<?php echo $urlAprobar; ?>" class="btn btn-success btn-action" onclick="return confirm('¿Aprobar solicitud de <?php echo $etiqueta; ?>?')">
                                        <i class="bi bi-check-lg"></i>
                                    </a>
                                    <a href="<?php echo $urlRechazar; ?>" class="btn btn-danger btn-action" onclick="return confirm('¿Rechazar solicitud de <?php echo $etiqueta; ?>?')">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No hay solicitudes pendientes con este filtro.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>