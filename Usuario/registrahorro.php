<?php
session_start();
require_once '../includes/conexion.php';

// 1. Seguridad
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// 2. Obtener datos del usuario
$stmt = $pdo->prepare("SELECT Nombre, Paterno, Materno FROM Usuarios WHERE Id_Ahorrador = ?");
$stmt->execute([$id_usuario]);
$user = $stmt->fetch();
$nombre_completo = $user['Nombre'] . " " . $user['Paterno'] . " " . $user['Materno'];

// ==========================================
// 3. VERIFICACIÓN DE ESTADO (Lógica Nueva)
// ==========================================
$puede_solicitar = true;
$mensaje_estado = "";
$clase_alerta = "";

// Buscamos la ÚLTIMA solicitud hecha por este usuario
$sqlEstado = "SELECT Id_Estado, Fecha FROM Solicitud_Ahorro 
              WHERE Id_Ahorrador = ? 
              ORDER BY Id_SolicitudAhorro DESC LIMIT 1";
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
    // Estado 2 = Aprobado
    elseif ($estado == 2) {
        $puede_solicitar = false;
        $mensaje_estado = "¡Felicidades! Tu solicitud de ahorro ya fue APROBADA y está activa. No es necesario enviar otra.";
        $clase_alerta = "alert-success";
    }
    // Estado 3 = Rechazado (Permitimos solicitar de nuevo)
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
<link rel="stylesheet" href="../css/ahorro-nomina.css">
</head>
<body>

  <header class="pn-header">
    <div class="container d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <img src="../img/NewLogo - 1.png" alt="logo" class="pn-logo" width="50">
        <span class="pn-brand">SETDITSX - Sindicato ITSX</span>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="pn-user"><?php echo htmlspecialchars($nombre_completo); ?> ▾</div>
        <a href="../logout.php" class="btn btn-sm btn-outline-primary">Cerrar Sesión</a>
      </div>
    </div>
  </header>

  <main class="container my-5">
      <a href="panelAhorrador.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar</a>
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

              <form class="row g-4" id="formAhorro" action="procesar_ahorro.php" method="POST" enctype="multipart/form-data">
                
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
                  <button type="submit" id="btnEnviar" class="btn btn-confirm ms-auto btn-success">Confirmar y enviar solicitud</button>
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