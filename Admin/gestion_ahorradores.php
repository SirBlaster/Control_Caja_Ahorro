<?php
require_once '../includes/init.php';
secure_session_start();
check_login(2); 

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

// ================== PAGINACIÓN ==================
$usuarios_por_pagina = 10;

$pagina_actual = isset($_GET['page']) && is_numeric($_GET['page'])
    ? (int)$_GET['page']
    : 1;

$offset = ($pagina_actual - 1) * $usuarios_por_pagina;

// Total de usuarios
$total_usuarios = contar_usuarios_ahorrador();
$total_paginas = ceil($total_usuarios / $usuarios_por_pagina);

// Obtener usuarios paginados
$usuarios = obtener_usuarios_ahorrador($usuarios_por_pagina, $offset);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - SETDITSX</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../css/admin.css">
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
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='./gestion_prestamos.php') echo 'active'; ?>"
                        href="./gestion_prestamos.php">
                        <i class="bi bi-cash-stack me-1"></i>Gestión de prestamos y ahorros
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='nominas.php') echo 'active'; ?>"
                        href="./gestion_nominas.php">
                        <i class="bi bi-cash-stack me-1"></i>Gestión de nominas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='./gestion_ahorradores.php') echo 'active'; ?>"
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
                        href="./editar_perfil.php">
                        <i class="bi bi-gear-fill me-1"></i>Editar perfil
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- CONTENIDO -->

    <div class="card-form">
        <a href="./inicio.php" class="btn btn-secondary mb-4">
            <i class="bi bi-arrow-left"></i> Volver al menú principal
        </a>

        <h2>Gestión de Usuarios</h2>

        <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show" role="alert">
            <i
                class="bi <?php echo $_SESSION['tipo_mensaje'] == 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'; ?> me-1"></i>
            <?php echo $_SESSION['mensaje']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        endif; ?>

        <div class="search-container mb-3">
            <div class="search-label">Buscar usuario:</div>
            <input type="text" id="buscarUsuario" class="search-input" placeholder="Nombre, RFC ó correo"
                onkeyup="filtrarUsuarios()">
            <hr class="diviser">
        </div>

        <hr class="divider">

        <h5 class="section-title">Lista de usuarios (Ahorradores)</h5>

        <div class="table-container">
            <table class="table table-hover" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>RFC</th>
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
                        <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['rfc']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td>
                            <span
                                class="badge <?php echo $usuario['rol_id'] == 1 ? 'badge-admin' : 'badge-ahorrador'; ?>">
                                <i
                                    class="bi <?php echo $usuario['rol_id'] == 1 ? 'bi-shield-check' : 'bi-person-check'; ?> me-1"></i>
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
                                <span>
                                    <a href="../admin/editar_usuario.php?id=<?php echo $usuario['id']; ?>"
                                        class="btn-editar-usuario" title="Editar usuario">
                                        Editar
                                    </a>
                                </span>
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
                Total: <?php echo $total_usuarios; ?> usuario(s)
            </small>
        </div>
        <nav aria-label="Paginación de usuarios">
            <ul class="pagination justify-content-center mt-3">

                <!-- Anterior -->
                <li class="page-item <?php echo $pagina_actual <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $pagina_actual - 1; ?>">
                        &laquo;
                    </a>
                </li>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?php echo $i == $pagina_actual ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>

                <!-- Siguiente -->
                <li class="page-item <?php echo $pagina_actual >= $total_paginas ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $pagina_actual + 1; ?>">
                        &raquo;
                    </a>
                </li>

            </ul>
        </nav>
    </div>

    <script>
    function filtrarUsuarios() {
        var input = document.getElementById("buscarUsuario");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("tablaUsuarios");
        var tr = table.getElementsByTagName("tr");

        for (var i = 1; i < tr.length; i++) {
            var tdNombre = tr[i].getElementsByTagName("td")[0];
            var tdRFC = tr[i].getElementsByTagName("td")[1];
            var tdEmail = tr[i].getElementsByTagName("td")[2];

            if (tdNombre || tdRFC || tdEmail) {
                var txtNombre = tdNombre.textContent || tdNombre.innerText;
                var txtRFC = tdRFC.textContent || tdRFC.innerText;
                var txtEmail = tdEmail.textContent || tdEmail.innerText;

                if (
                    txtNombre.toUpperCase().includes(filter) ||
                    txtRFC.toUpperCase().includes(filter) ||
                    txtEmail.toUpperCase().includes(filter)
                ) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    </script>


</body>

</html>