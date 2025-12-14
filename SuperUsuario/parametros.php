<?php
// SuperUsuario/parametros.php
require_once '../includes/init.php';
secure_session_start();
check_login(3);

$parametros = obtener_parametros_sistema();
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'tasa_general' => floatval($_POST['tasa_general']),
        'rendimiento' => floatval($_POST['rendimiento']),
        'correo_soporte' => trim($_POST['correo_soporte']),
        'nombre_director' => trim($_POST['nombre_director']),
        'periodo' => trim($_POST['periodo']),
        'nombre_enc_personal' => trim($_POST['nombre_enc_personal'])
    ];
    
    // Obtener nombre e ID del usuario
    $usuario_nombre = get_user_name();
    $usuario_id = $_SESSION['id_usuario'] ?? null;
    
    if (actualizar_parametros_sistema($datos, $usuario_nombre)) {
        $mensaje = '✅ Parámetros actualizados correctamente';
        $tipo_mensaje = 'success';
        $parametros = obtener_parametros_sistema();
    } else {
        $mensaje = '❌ Error al actualizar parámetros';
        $tipo_mensaje = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Parámetros - SETDITSX</title>
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
                <p class="user-name"><?php echo htmlspecialchars(get_user_name()); ?></p>
                <small class="text-muted"><?php echo htmlspecialchars(get_user_role_text()); ?></small>
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
        
        <h2>Configuración de parámetros</h2>
        
        <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
            <?php echo $mensaje; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="parameter-group">
                <h5 class="section-title">Préstamos</h5>
                
                <div class="parameter-item">
                    <div class="col-md-6">Tasa de interés general (%):</div>
                    <input type="number" name="tasa_general" class="form-control" 
                           step="0.01" min="0.01" max="100" 
                           value="<?php echo htmlspecialchars($parametros['tasa_interes_general']); ?>" 
                           required>
                    <small class="text-muted">Tasa anual para usuarios sin tasa personalizada</small>
                </div>
            </div>
            
            <div class="parameter-group">
                <h5 class="section-title">Ahorros</h5>
                
                <div class="parameter-item">
                    <div class="col-md-6">Rendimiento anual (%):</div>
                    <input type="number" name="rendimiento" class="form-control" 
                           step="0.01" min="0.01" max="100" 
                           value="<?php echo htmlspecialchars($parametros['rendimiento_anual_ahorros']); ?>" 
                           required>
                    <small class="text-muted">Rendimiento anual para todos los ahorradores</small>
                </div>
            </div>
            
            <div class="parameter-group">
                <h5 class="section-title">General</h5>
                
                <div class="parameter-item">
                    <div class="col-md-6">Correo de contacto soporte:</div>
                    <input type="email" name="correo_soporte" class="form-control" 
                           value="<?php echo htmlspecialchars($parametros['correo_soporte']); ?>" 
                           required>
                </div>
                <div class="parameter-item">
                    <div class="col-md-6">Nombre de Administrativo:</div>
                    <input type="text" name="nombre_director" class="form-control" 
                           value="<?php echo htmlspecialchars($parametros['nombre_director']); ?>" 
                           required>
                </div>
                <div class="parameter-item">
                    <div class="col-md-6">Nombre del Encargado de personal:</div>
                    <input type="text" name="nombre_enc_personal" class="form-control" 
                           value="<?php echo htmlspecialchars($parametros['nombre_enc_personal']); ?>" 
                           required>
                </div>
                <div class="parameter-item">
                    <div class="col-md-6">Periodo</div>
                    <input type="text" name="periodo" class="form-control" 
                           value="<?php echo htmlspecialchars($parametros['periodo']); ?>" 
                           required>
                </div>
            </div>
            <br>
            <div class="action-buttons">
                <a href="../SuperUsuario/inicio.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
        
        <?php if (!empty($parametros['fecha_actualizacion'])): ?>
        <div class="mt-3 text-end text-muted">
            <small>
                Última actualización: <?php echo date('d/m/Y H:i', strtotime($parametros['fecha_actualizacion'])); ?>
                <?php if (!empty($parametros['usuario_actualizacion'])): ?>
                por <?php echo htmlspecialchars($parametros['usuario_actualizacion']); ?>
                <?php endif; ?>
            </small>
        </div>
        <?php endif; ?>
    </div>

    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
