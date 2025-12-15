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
    <title>Gestión de Ahorradores - Administrador</title>
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
            <button class="btn btn-logout" id="btnLogout">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </button>

            <script>
            document.getElementById("btnLogout").addEventListener("click", function() {
                if (confirm("¿Deseas cerrar sesión?")) {
                    window.location.href = "../logout.php";
                }
            });
            </script>
        </div>
    </div>

    <div class="container-fluid main-content">

        <h1 class="page-title">Gestión de Ahorradores</h1>

        <div class="card-container">
            <div class="mb-3">
                <a href="./inicio.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar</a>
            </div>

            <div class="content-card search-card mb-3">
                <label class="search-label">Buscar ahorrador:</label>
                <div class="d-flex gap-0">
                    <input type="text" class="form-control search-input" placeholder="Nombre o ID">
                    <button class="btn-search">Buscar</button>
                </div>
            </div>

            <div class="content-card">
                <h4 class="card-heading">Lista de ahorradores</h4>

                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>ID Ahorrador</th>
                                <th>Nombre completo</th>
                                <th>RFC</th>
                                <th>Saldo total ahorrado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1A</td>
                                <td class="text-start fw-bold">Joaquín Enrique<br>Cázares Betanzos</td>
                                <td>CABJ031028HVZZTQA3</td>
                                <td>$10,200.00</td>
                                <td><span class="badge status-active">Activo</span></td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button class="btn-action btn-nomina">Registrar nómina</button>
                                        <button class="btn-action btn-profile">Ver perfil</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2B</td>
                                <td class="text-start fw-bold">Diego Huerta<br>Rodriguez</td>
                                <td>EXT990101NI1</td>
                                <td>$8,500.00</td>
                                <td><span class="badge status-inactive">Inactivo</span></td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button class="btn-action btn-nomina">Registrar nómina</button>
                                        <button class="btn-action btn-profile">Ver perfil</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3C</td>
                                <td class="text-start fw-bold">Ángel de Jesús<br>Hernández Aparicio</td>
                                <td>XAXX010101000</td>
                                <td>$4,350.00</td>
                                <td><span class="badge status-active">Activo</span></td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button class="btn-action btn-nomina">Registrar nómina</button>
                                        <button class="btn-action btn-profile">Ver perfil</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4D</td>
                                <td class="text-start fw-bold">Juan Manuel<br>Bello Zuñiga</td>
                                <td>XEXX010101000</td>
                                <td>$2,770.00</td>
                                <td><span class="badge status-active">Activo</span></td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button class="btn-action btn-nomina">Registrar nómina</button>
                                        <button class="btn-action btn-profile">Ver perfil</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="pagination-container">
                    <button class="btn-page"><i class="bi bi-caret-left-fill"></i>⏪</button>
                    <button class="btn-page active">1</button>
                    <button class="btn-page"><i class="bi bi-caret-right-fill"></i>⏩</button>
                </div>

            </div>
        </div>
    </div>
    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>