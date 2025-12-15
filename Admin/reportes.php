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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reportes - Administrador</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../css/admin.css" />
</head>

<body>
    <div class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="../img/NewLogo - 1.png" alt="SETDITSX" width="70" class="me-3" />
            <h4 class="mb-0">SETDITSX - Sindicato ITSX</h4>
        </div>

        <div class="header-center-title">
            <h2 class="mb-0">Administrador</h2>
        </div>

        <div class="user-info d-flex align-items-center">
            <i class="bi bi-person-circle user-icon me-2"></i>
            <span class="user-name me-3">Sánchez Cortes Felipe Martin</span>
            <button class="btn btn-logout">Cerrar Sesión</button>
        </div>
    </div>

    <div class="container-fluid main-content">

        <h1 class="page-title">Reportes</h1>

        <div class="card-container">
            <div class="mb-3">
                <a href="./inicio.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar</a>
            </div>

            <div class="content-card">
                <h4 class="card-heading">Generar reportes del sistema</h4>

                <form>
                    <div class="form-section">
                        <label class="form-label section-label">1. Seleccionar tipo de reporte</label>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <label for="reportType" class="col-form-label fw-bold">Tipo:</label>
                            </div>
                            <div class="col">
                                <select class="form-select custom-input bg-light-gray" id="reportType">
                                    <option selected>Reporte de préstamos</option>
                                    <option value="2">Reporte de ahorros (saldos)</option>
                                    <option value="3">Reporte de movimientos</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <label class="form-label section-label">2. Filtrar por fecha</label>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <label for="dateFrom" class="form-label me-3 mb-0 fw-bold"
                                        style="min-width: 50px;">Desde:</label>
                                    <input type="date" class="form-control custom-input" id="dateFrom">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <label for="dateTo" class="form-label me-3 mb-0 fw-bold"
                                        style="min-width: 50px;">Hasta:</label>
                                    <input type="date" class="form-control custom-input" id="dateTo">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <label class="form-label section-label">3. Filtros adicionales</label>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="statusFilter" class="form-label fw-bold mb-1">Estado:</label>
                                <select class="form-select custom-input bg-light-gray" id="statusFilter">
                                    <option selected value="all">Todos</option>
                                    <option value="active">Activos</option>
                                    <option value="pending">Pendientes</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="userId" class="form-label fw-bold mb-1">ID del Usuario :</label>
                                <input type="text" class="form-control custom-input" id="userId">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-5">
                        <button type="button" class="btn btn-export-large">Exportar a PDF</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>