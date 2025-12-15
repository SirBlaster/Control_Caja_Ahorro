<?php
// Administrador/Inicio.php
require_once '../includes/init.php';

secure_session_start();
check_login(2); // Nivel 2 = Administrador (ajusta si usas otro)

// Aquí después puedes traer datos reales desde BD
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Administrador - SETDITSX</title>

    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../css/admin.css" />
</head>

<body>
    <!-- HEADER -->
    <div class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="../img/logoChico.png" alt="SETDITSX" width="70" class="me-3" />
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

    <!-- CONTENIDO -->
    <div class="card-form">
        <h2>Panel de Administrador</h2>

        <div class="dashboard-cards">
            <!-- Gestión de Préstamos -->
            <div class="dashboard-card">
                <div class="card-title">Gestión de Préstamos</div>
                <div class="card-count">4</div>
                <div class="card-description">Solicitudes por revisar</div>
                <a href="gestion_prestamos.php" class="btn btn-manage">
                    Gestionar
                </a>
            </div>

            <!-- Gestión de Ahorradores -->
            <div class="dashboard-card">
                <div class="card-title">Gestión de usuarios</div>
                <div class="card-icon">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="card-description">
                    Consultar perfiles y registrar nómina
                </div>
                <a href="gestion_ahorradores.php" class="btn btn-manage">
                    Consultar
                </a>
            </div>

            <!-- Reportes -->
            <div class="dashboard-card">
                <div class="card-title">Reportes</div>
                <div class="card-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div class="card-description">Generar y descargar</div>
                <a href="reportes.php" class="btn btn-manage">
                    Emitir
                </a>
            </div>
        </div>

        <hr class="divider" />

        <!-- SOLICITUDES RECIENTES -->
        <h5 class="section-title">Solicitudes recientes</h5>

        <div class="table-container">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Solicitante</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1A</td>
                        <td>Joaquín Cázares</td>
                        <td>Préstamo</td>
                        <td>$2,300.00</td>
                        <td class="status-pending">Pendiente</td>
                        <td>
                            <button class="btn-details">Ver detalles</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <a href="#" class="view-all">
            <i class="bi bi-list-ul"></i> Ver todas las solicitudes
        </a>
    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>