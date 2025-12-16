<?php
require_once '../includes/init.php';

secure_session_start();
check_login(2);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar_excel'])) {
    $mes = $_POST['mes'];
    $quincena = intval($_POST['quincena']);
    generarReporteQuincenalCSV($pdo, $mes, $quincena);
}
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
    <div class="header d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
        <div class="d-flex align-items-center">
            <img src="../img/logoChico.png" alt="SETDITSX" width="70" class="me-3" />
            <h4 class="mb-0">SETDITSX - Sindicato ITSX</h4>
        </div>

        <div class="user-info d-flex align-items-center">
            <i class="bi bi-person-square user-icon me-2"></i>

            <div class="user-details me-3">
                <p class="user-name mb-0"><?php echo htmlspecialchars(get_user_name()); ?></p>
                <small class="text-muted"><?php echo htmlspecialchars(get_user_role_text()); ?></small>
            </div>

            <form action="../logout.php" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-logout" onclick="return confirm('¿Deseas cerrar sesión?')">
                    <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <!-- NAVBAR DE ADMINISTRADOR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='inicio.php') echo 'active'; ?>"
                        href="./inicio.php">
                        <i class="bi bi-house-door-fill me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='ahorros.php') echo 'active'; ?>"
                        href="./gestion_prestamos.php">
                        <i class="bi bi-cash-stack me-1"></i>Gestión de prestamos y ahorros
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='usuarios.php') echo 'active'; ?>"
                        href="./gestion_ahorradores.php">
                        <i class="bi bi-people-fill me-1"></i>Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='reportes.php') echo 'active'; ?>"
                        href="./reportes.php">
                        <i class="bi bi-file-earmark-text-fill me-1"></i>Reportes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='configuracion.php') echo 'active'; ?>"
                        href="./configuracion.php">
                        <i class="bi bi-gear-fill me-1"></i>Configuración
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="container my-4">

        <a href="./inicio.php" class="btn btn-secondary mb-4">
            <i class="bi bi-arrow-left"></i> Volver al menú principal
        </a>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Generar reportes del sistema</h5>
                <form method="post" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="mes" class="form-label">Seleccionar mes</label>
                        <input type="month" class="form-control" name="mes" id="mes" required>
                    </div>
                    <div class="col-md-4">
                        <label for="quincena" class="form-label">Seleccionar quincena</label>
                        <select class="form-select" name="quincena" id="quincena" required>
                            <option value="1">Quincena 1 (Días 1-15)</option>
                            <option value="2">Quincena 2 (Días 16-fin de mes)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-grid">
                        <button type="submit" name="generar_excel" class="btn btn-primary">
                            <i class="bi bi-file-earmark-text me-1"></i> Generar Reporte
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>

    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>