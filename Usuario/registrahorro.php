<?php
require_once '../includes/init.php';
secure_session_start();
check_login(1);

// 1. Seguridad
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// 2. Obtener datos del usuario
$stmt = $pdo->prepare("SELECT nombre, apellido_paterno, apellido_materno FROM usuario WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$user = $stmt->fetch();
$nombre_completo = $user['nombre'] . " " . $user['apellido_paterno'] . " " . $user['apellido_materno'];


$puede_solicitar = true;
$mensaje_estado = "";
$clase_alerta = "";

// Buscamos la ÚLTIMA solicitud hecha por este usuario
$sqlEstado = "SELECT Id_Estado, Fecha FROM Solicitud_Ahorro 
              WHERE id_usuario = ? 
              ORDER BY id_solicitud_ahorro DESC LIMIT 1";
$stmtEstado = $pdo->prepare($sqlEstado);
$stmtEstado->execute([$id_usuario]);
$ultima_solicitud = $stmtEstado->fetch();

if ($ultima_solicitud) {
    $estado = $ultima_solicitud['Id_Estado'];
    
    // Estado 1 = Pendiente
    if ($estado == 1) {
        $puede_solicitar = false;
        $mensaje_estado = "Ya tienes una solicitud en espera de revisión. Por favor espera a que sea atendida.";
        $clase_alerta = "alert-warning";
    }

        elseif ($estado == 2) {
        $puede_solicitar = true; // Lo dejamos pasar
        $mensaje_estado = "Tu solicitud anterior fue aceptada. Si haces otra solicitud remplazara a la anterior (solo puede aumentar el monto ahorrar si es en el mismo ciclo administrativo).";
        $clase_alerta = "alert-danger"; // Rojo para avisar, pero mostramos el form
    }

    // Estado 2 = Rechazado (Permitimos solicitar de nuevo)
    elseif ($estado == 3) {
        $puede_solicitar = true; // Lo dejamos pasar
        $mensaje_estado = "Tu solicitud anterior fue rechazada. Puedes corregir tus datos y enviar una nueva solicitud aquí.";
        $clase_alerta = "alert-danger"; // Rojo para avisar, pero mostramos el form
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Solicitud Ahorro - registrar nómina</title>
<link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="../css/estilo_ahorrador.css">
<link rel="stylesheet" href="../css/ahorro-nomina.css">
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-light bg-light header">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">
          <img src="../img/LogoChico.png" width="50" height="50" class="d-inline-block align-items-center" alt=""> SETDITSX
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="panelAhorrador.php">Panel Principal</a>
            </li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Apartados (Ahorrador)
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><h6 class="dropdown-header text-primary">Ahorro</h6></li>
                        <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/registrahorro.php">Solicitar Ahorro</a></li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <li><h6 class="dropdown-header text-primary">Préstamos</h6></li>
                        <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/solicitud_prestamo.php">Solicitar préstamo</a></li>
                        <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/Estado_Prestamo.php">Estado de mi préstamo</a></li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <li><h6 class="dropdown-header text-primary">Movimientos y Consultas</h6></li>
                        <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/movimientos.php">Ver movimientos</a></li>
                        <li><a class="dropdown-item" href="/ControlCajadeAhorro/Usuario/mis_solicitudes.php">Mis solicitudes</a></li>
              </ul>
            </li>
          </ul>
        </div>

        <div class="d-flex align-items-center gap-3">
          <div class="user-details text-end d-none d-md-block">
            <p class="user-name mb-0 fw-bold"><?php echo get_user_name(); ?></p>
            <small class="text-muted"><?php echo get_user_role_text(); ?></small>
          </div>
          <a href="../logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
          </a>
        </div>
      </div>
    </nav>


  <main class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-9">

        <div class="pn-card p-5 shadow-sm">
          <h1 class="pn-title text-center mb-4">Solicitar ahorro y registro de nómina</h1>

          <?php if (!empty($mensaje_estado)): ?>
            <div class="alert <?php echo $clase_alerta; ?> text-center mb-4" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>
                <?php echo $mensaje_estado; ?>
            </div>
          <?php endif; ?>

          <?php if ($puede_solicitar): ?>

              <form class="row g-4" id="formAhorro" action="../includes/Usuario/procesar_ahorro.php" method="POST" enctype="multipart/form-data">
                
                <div class="col-12">
                  <label class="form-label fw-bold">Registre su nómina (Sueldo Neto MXN)</label>
                  <input type="number" step="0.01" name="sueldo" id="sueldo" class="form-control form-control-lg pn-input-amount" placeholder="Inserte su nómina" required>
                </div>

                <div class="col-12">
                  <label class="form-label fw-bold">Monto de ahorro deseado (MXN)</label>
                  <input type="number" step="0.01" name="monto" id="monto" class="form-control form-control-lg pn-input-amount" placeholder="Inserte el monto a ahorrar" required>
                  <small class="text-danger mt-1" id="errorMonto" style="display:none; font-weight:bold;">
                     El monto no puede superar el 30% de tu nómina ($<span id="topeMax">0.00</span>)
                  </small>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Cargar comprobante de Nómina (PDF)</label>
                  <div class="upload-box mt-2">
                    <div class="upload-inner text-center p-4">
                      <i class="bi bi-cloud-upload upload-icon" style="font-size: 2rem;"></i>
                      <p class="mb-2">Arrastra y suelta tu archivo PDF aquí</p>
                      <input type="file" name="archivo_nomina" id="nominaFile" accept="application/pdf" class="d-none" required>
                      <label for="nominaFile" class="btn btn-sm btn-select btn-primary">Seleccionar archivo</label>
                      <div id="fileNameDisplay" class="mt-2 text-muted small"></div>
                    </div>
                  </div>
                </div>

                <div class="col-12 d-flex gap-3 flex-wrap">
                <div class="col-12 d-flex gap-3 flex-wrap align-items-center mt-4">
                  <a href="panelAhorrador.php" class="btn btn-outline-dark btn-cancel px-4">Cancelar</a>
                  <button type="submit" id="btnEnviar" class="btn btn-confirm ms-auto btn-success">Confirmar y enviar solicitud</button>
                </div>
                </div>
              </form>

          <?php else: ?>
              <div class="text-center">
                <p class="text-muted">No puedes realizar una nueva solicitud en este momento.</p>
                <a href="panelAhorrador.php" class="btn btn-primary">Volver al Panel Principal</a>
              </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </main>

<script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
<?php if ($puede_solicitar): ?>
    <script src="../js/validar_ahorro.js"></script>
    <script>
        document.getElementById('nominaFile').addEventListener('change', function(e) {
            var fileName = e.target.files[0] ? e.target.files[0].name : '';
            document.getElementById('fileNameDisplay').textContent = fileName;
        });
    </script>
<?php endif; ?>
</body>
</html>
