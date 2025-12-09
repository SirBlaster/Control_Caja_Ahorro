<?php
// SuperUsuario/usuarios.php
require_once '../includes/init.php';
secure_session_start();
check_login(3); // Solo SuperUsuario

// Manejar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cambiar_estado']) && isset($_POST['id_usuario'])) {
        $resultado = cambiar_estado_usuario($_POST['id_usuario']);
        $_SESSION['mensaje'] = $resultado['message'];
        $_SESSION['tipo_mensaje'] = $resultado['success'] ? 'success' : 'danger';
        header("Location: usuarios.php");
        exit();
    }
    
    if (isset($_POST['cambiar_rol']) && isset($_POST['id_usuario'])) {
        $resultado = cambiar_rol_usuario($_POST['id_usuario']);
        $_SESSION['mensaje'] = $resultado['message'];
        $_SESSION['tipo_mensaje'] = $resultado['success'] ? 'success' : 'danger';
        header("Location: usuarios.php");
        exit();
    }
}

// Obtener usuarios usando la nueva función
$usuarios = obtener_usuarios_admin();

// Obtener actividades recientes
$actividades = obtener_actividades_recientes(10);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - SETDITSX</title>
    <link rel="stylesheet" href="../../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/Super.css">
</head>
<body>
    <div class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="../../img/NewLogo - 1.png" alt="SETDITSX" width="70" class="me-3">
            <h4 class="mb-0">SETDITSX - Sindicato ITSX</h4>
        </div>
        
        <div class="user-info">
            <i class="bi bi-person-square user-icon"></i>
            <div class="user-details">
                <p class="user-name"><?php echo get_user_name(); ?></p>
                <small class="text-muted"><?php echo get_user_role_text(); ?></small>
            </div>
            <form action="../logout.php" method="POST" style="display: inline;">
                <button type="submit" class="btn btn-logout">
                    <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
    
    <div class="card-form">
        <div class="nav-actions">
            <a href="../SuperUsuario/inicio.php" class="nav-link">
                <i class="bi bi-arrow-left"></i> Volver al menú principal
            </a>
        </div>
        
        <h2>Gestión de Usuarios</h2>
        
        <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show" role="alert">
            <i class="bi <?php echo $_SESSION['tipo_mensaje'] == 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'; ?> me-1"></i>
            <?php echo $_SESSION['mensaje']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        endif; ?>
        
        <div class="search-container mb-3">
            <div class="search-label">Buscar usuario:</div>
            <input type="text" id="buscarUsuario" class="search-input" 
                   placeholder="Nombre, correo o ID" 
                   onkeyup="filtrarUsuarios()">
        </div>
        
        <hr class="divider">
        
        <h5 class="section-title">Lista de usuarios (Administradores y Ahorradores)</h5>
        
        <div class="table-container">
            <table class="table table-hover" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre completo</th>
                        <th>Correo institucional</th>
                        <th>Rol actual</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td>
                                <span class="badge <?php echo $usuario['rol_id'] == 1 ? 'badge-admin' : 'badge-ahorrador'; ?>">
                                    <i class="bi <?php echo $usuario['rol_id'] == 1 ? 'bi-shield-check' : 'bi-person-check'; ?> me-1"></i>
                                    <?php echo htmlspecialchars($usuario['nombre_rol']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($usuario['habilitado'] == 1): ?>
                                    <span class="status-active">
                                        <i class="bi bi-check-circle me-1"></i>Habilitado
                                    </span>
                                <?php else: ?>
                                    <span class="status-inactive">
                                        <i class="bi bi-x-circle me-1"></i>Deshabilitado
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Botón Editar -->
                                    
                                    <!-- Botón Habilitar/Deshabilitar -->
                                    <form method="POST" action="usuarios.php" style="display: inline;">
                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                                        <?php if ($usuario['habilitado'] == 1): ?>
                                            <button type="submit" name="cambiar_estado" 
                                                    class="btn btn-warning btn-action"
                                                    onclick="return confirm('¿Deshabilitar este usuario? No podrá iniciar sesión.')"
                                                    title="Deshabilitar usuario">
                                                <i class="bi bi-person-x"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="cambiar_estado" 
                                                    class="btn btn-success btn-action"
                                                    onclick="return confirm('¿Habilitar este usuario? Podrá iniciar sesión nuevamente.')"
                                                    title="Habilitar usuario">
                                                <i class="bi bi-person-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                    
                                    <!-- Botón Cambiar Rol -->
                                    <form method="POST" action="usuarios.php" style="display: inline;">
                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                                        <?php if ($usuario['rol_id'] == 1): ?>
                                            <!-- Si es Administrador, cambiar a Ahorrador -->
                                            <button type="submit" name="cambiar_rol" 
                                                    class="btn btn-info btn-action"
                                                    onclick="return confirm('¿Cambiar a Ahorrador? Perderá privilegios de administración.')"
                                                    title="Cambiar a Ahorrador">
                                                <i class="bi bi-person-down"></i>
                                            </button>
                                        <?php else: ?>
                                            <!-- Si es Ahorrador, cambiar a Administrador -->
                                            <button type="submit" name="cambiar_rol" 
                                                    class="btn btn-primary btn-action"
                                                    onclick="return confirm('¿Cambiar a Administrador? Tendrá privilegios de administración.')"
                                                    title="Cambiar a Administrador">
                                                <i class="bi bi-person-up"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    No hay usuarios registrados (excluyendo SuperUsuarios)
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Total: <?php echo count($usuarios); ?> usuario(s)
            </small>
            <div class="legend">
                <small class="text-muted me-3">
                    <span class="status-active me-1"></span> Habilitado
                </small>
                <small class="text-muted me-3">
                    <span class="status-inactive me-1"></span> Deshabilitado
                </small>
                <small class="text-muted me-2">
                    <span class="badge badge-admin me-1"></span> Administrador
                </small>
                <small class="text-muted">
                    <span class="badge badge-ahorrador me-1"></span> Ahorrador
                </small>
            </div>
        </div>
    </div>

    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
    <script>
    function filtrarUsuarios() {
        var input = document.getElementById("buscarUsuario");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("tablaUsuarios");
        var tr = table.getElementsByTagName("tr");
        
        // Ocultar/mostrar filas
        for (var i = 1; i < tr.length; i++) {
            var tdNombre = tr[i].getElementsByTagName("td")[1];
            var tdEmail = tr[i].getElementsByTagName("td")[2];
            var tdId = tr[i].getElementsByTagName("td")[0];
            
            if (tdNombre || tdEmail || tdId) {
                var txtValueNombre = tdNombre.textContent || tdNombre.innerText;
                var txtValueEmail = tdEmail.textContent || tdEmail.innerText;
                var txtValueId = tdId.textContent || tdId.innerText;
                
                if (txtValueNombre.toUpperCase().indexOf(filter) > -1 || 
                    txtValueEmail.toUpperCase().indexOf(filter) > -1 ||
                    txtValueId.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    
    // Mostrar tooltips para los botones
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    </script>
</body>
</html>
