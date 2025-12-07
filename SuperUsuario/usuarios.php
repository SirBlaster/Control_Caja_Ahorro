<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - SETDITSX</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/Super.css">
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
                <p class="user-name">Usuario</p> <!-- Cambiar para colocar el nombre del inicio de sesión -->
            </div>
            <button class="btn btn-logout">
                <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
            </button>
        </div>
    </div>
    
    <div class="card-form">
        <div class="nav-actions">
            <a href="#" class="nav-link">
                <i class="bi bi-arrow-left"></i> Volver al menú principal
            </a>
        </div>
        
        <h2>Gestión de Usuarios</h2>
        
        <div class="search-container">
            <div class="search-label">Buscar usuario:</div>
            <input type="text" class="search-input" placeholder="Nombre o ID">
        </div>
        
        <hr class="divider">
        
        <h5 class="section-title">Lista de usuarios</h5>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Usuario</th>
                        <th>Nombre completo</th>
                        <th>Correo electrónico</th>
                        <th>Rol actual</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1A</td>
                        <td>Joaquín Enrique Cázares Betanzos</td>
                        <td>joaq@gmail.com</td>
                        <td>Ahorrador</td>
                        <td class="status-active">Activo</td>
                        <td>
                            <button class="btn btn-secondary">Administrador</button>
                            <button class="btn btn-primary">Desactivar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
