<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Administrador - SETDITSX</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="../css/admin.css" />
    <style></style>
  </head>
  <body>
    <div class="header d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <img
          src="../img/NewLogo - 1.png"
          alt="SETDITSX"
          width="70"
          class="me-3"
        />
        <h4 class="mb-0">SETDITSX - Sindicato ITSX</h4>
      </div>

      <div class="user-info">
        <i class="bi bi-person-square user-icon"></i>
        <div class="user-details">
          <p class="user-name">Administrador</p>
        </div>
        <button class="btn btn-logout">
          <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </button>
      </div>
    </div>

    <div class="card-form">
      <h2>Administrador</h2>

      <div class="dashboard-cards">
        <div class="dashboard-card">
          <div class="card-title">Gestión de Préstamos</div>
          <div class="card-count">4</div>
          <div class="card-description">Solicitudes por revisar</div>
          <button class="btn btn-manage">Gestionar</button>
        </div>

        <div class="dashboard-card">
          <div class="card-icon">
            <i class="bi bi-person-fill"></i>
          </div>
          <div class="card-title">Gestión de Ahorradores</div>
          <div class="card-description">
            Consultar perfiles y registrar nómina
          </div>
          <button class="btn btn-manage">Consultar</button>
        </div>

        <div class="dashboard-card">
          <div class="card-icon">
            <i class="bi bi-receipt-cutoff"></i>
          </div>
          <div class="card-title">Reportes</div>
          <div class="card-description">Generar y descargar</div>
          <button class="btn btn-manage">Emitir</button>
        </div>
      </div>

      <hr class="divider" />

      <h5 class="section-title">Solicitudes recientes</h5>

      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Solicitante</th>
              <th>Tipo</th>
              <th>Monto</th>
              <th>Estado</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1A</td>
              <td>Joaquín Cázares</td>
              <td>Préstamo</td>
              <td>$2.300.00</td>
              <td class="status-pending">Pendiente</td>
              <td>
                <button class="btn-details">Ver detalles</button>
              </td>
            </tr>
            
          </tbody>
        </table>
      </div>

      <a href="#" class="view-all">
        <i class="bi bi-list-ul"></i> Ver todas las solicitudes
      </a>
    </div>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
  </body>
</html>
