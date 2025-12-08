<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css" />
    <link
      rel="stylesheet" href="../css/bootstrap-icons/font/bootstrap-icons.css"
    />
    <link rel="stylesheet" href="../css/Super.css" />
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
          <p class="user-name">Usuario</p>
          <!-- Cambiar para colocar el nombre del inicio de sesión -->
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
      <h2>Editar Usuario ("Nombre")</h2>
      <h2>Configuración de parámetros</h2>

      <div class="parameter-group">
        <h5 class="section-title">Préstamos</h5>

        <div class="parameter-item">
          <div class="col-md-6">Tasas de interés anual(%):</div>
          <input type="text" class="form-control" value="12.0 %" />
        </div>

        <div class="parameter-item">
          <div class="col-md-6">Monto máximo de préstamo ($ MXM):</div>
          <input type="text" class="form-control" value="50,000.00" />
        </div>

        <div class="parameter-item">
          <div class="col-md-6">Plazo máximo (Quincenas):</div>
          <input type="text" class="form-control" value="48" />
        </div>
      </div>

      <div class="parameter-group">
        <h5 class="section-title">Ahorros</h5>

        <div class="parameter-item">
          <div class="col-md-6">Porcentaje mínimo de ahorro(%):</div>
          <input type="text" class="form-control" value="5.0 %" />
        </div>

        <div class="parameter-item">
          <div class="col-md-6">Monto mínimo de retiro ($ MXM):</div>
          <input type="text" class="form-control" value="500.00" />
        </div>
      </div>
      <br />
      <div class="action-buttons">
        <button class="btn btn-secondary">Cancelar</button>
        <button class="btn btn-primary">Guardar cambios</button>
      </div>
    </div>
  </body>
</html>
