<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="../css/Super.css">
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css ">
</head>
<body>
    <div class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="../img/NewLogo - 1.png" alt="SETDITSX" width="70" class="me-3">
            <h4 class="mb-0">SETDITSX - Sindicato ITSX</h4>
        </div>
        
        <div class="user-info">
            <i class="bi bi-person-square user-icon"></i>
            <div class="user-details">
                <p class="user-name">Nombre Usuario</p> <!-- Cambiar para colocar el nombre del inicio de sesión -->
            </div>
            <button class="btn btn-logout">
                <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
            </button>
        </div>
    </div>
<div class="card card-form">
        <div >
            <h2 class="mb-0">Panel de SuperUsuario</h2>
        </div>
        <div class="card-body p-4">
            <!-- Gestión de Usuarios -->
            <div class="management-card">
                <h5 class="section-title">Gestión de Usuarios</h5>
                <p class="mb-3">Administrar roles, permisos y cuentas</p>
                <button class="btn btn-manage">Gestionar</button>
            </div>
            
            <!-- Parámetros del sistema -->
            <div class="management-card">
                <h5 class="section-title">Parámetros del sistema</h5>
                <p class="mb-3">Configurar tasas, montos, límite y variables</p>
                <button class="btn btn-manage">Gestionar</button>
            </div>
            
            <hr class="divider">
            
            <!-- Actividad reciente del sistema -->
            <h5 class="section-title">Actividad reciente del sistema</h5>
            <div class="table-container">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
            
            <a href="#" class="view-history">
                <i class="bi bi-clock-history me-1"></i> Ver historial completo
            </a>
        </div>
    </div>

    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
