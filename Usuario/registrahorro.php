<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Solicitud Ahorro - registrar nómina</title>

<!-- Bootstrap (opcional, ayuda con grid y utilidades) -->
<link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css">

<!-- Estilos locales -->
<link rel="stylesheet" href="../css/ahorro-nomina.css">

</head>
<body>

  <!-- Header (puedes ajustar rutas de logo y botones) -->
  <header class="pn-header">
    <div class="container d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <img src="../img/LogoHorizontal - 2.png" alt="logo" class="pn-logo">
        <span class="pn-brand">SETDITSX - Sindicato ITSX</span>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="pn-user">Sánchez Cortes Felipe Martin ▾</div>
        <button class="btn btn-sm btn-outline-primary">Cerrar Sesión</button>
      </div>
    </div>
  </header>

  <main class="container my-5">
    <!-- BOTÓN REGRESAR -->
      <a href="panelAhorrador.php" class="btn btn-secondary btn-sm mb-3">&larr; Regresar</a>
    <div class="row justify-content-center">
      <div class="col-lg-9">

        <div class="pn-card p-5 shadow-sm">
          <h1 class="pn-title text-center mb-4">Solicitar ahorro y registro de nómina</h1>

          <form class="row g-4">
            <!-- Monto -->
            <div class="col-12">
              <label class="form-label fw-bold">Monto de ahorro deseado (MXN)</label>
              <input type="text" class="form-control form-control-lg pn-input-amount" value="$2,500.00 MXN" aria-label="Monto de ahorro deseado">
            </div>

            <!-- Upload area -->
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold">Cargar comprobante de Nómina (PDF)</label>

              <div class="upload-box mt-2">
                <div class="upload-inner text-center p-4">
                  <i class="bi bi-cloud-upload upload-icon"></i>
                  <p class="mb-2">Arrastra y suelta tu archivo PDF aquí</p>

                  <!-- input type file: la interacción queda nativa sin JS -->
                  <input type="file" id="nominaFile" accept="application/pdf" class="d-none">
                  <label for="nominaFile" class="btn btn-sm btn-select">Seleccionar archivo</label>
                </div>
              </div>
            </div>

            <!-- Espacio en blanco (a la derecha del upload) -->
            <div class="col-12 col-md-6">
              <!-- Este espacio replica el cuadro vacío de la imagen original -->
            </div>

            <!-- Checkbox -->
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="confirmData">
                <label class="form-check-label" for="confirmData">
                  Certifico que los datos sean correctos
                </label>
              </div>
            </div>

            <!-- Botones (alineados a la izquierda como en imagen, pero con layout para tablet/desktop) -->
            <div class="col-12 d-flex gap-3 flex-wrap">
              <button type="button" class="btn btn-generate">Generar formato PDF</button>
              <button type="submit" class="btn btn-confirm ms-auto">Confirmar y enviar solicitud</button>
            </div>
          </form>

        </div> <!-- /pn-card -->

      </div>
    </div>
  </main>

<script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
