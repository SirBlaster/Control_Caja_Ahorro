<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Préstamos - Administrador</title>
    <link rel="stylesheet" href="Prestamos.css" />
  </head>
  <body>
    <div class="header d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center brand-area">
        <img
          src="imagenes/NewLogo - 1.png"
          alt="SETDITSX"
          width="60"
          class="me-2"
        />
        <h6 class="mb-0 text-white fw-bold">SETDITSX - Sindicato ITSX</h6>
      </div>

      <div class="header-center-title">
        <h2 class="mb-0">Administrador</h2>
      </div>

      <div class="user-info d-flex align-items-center">
        <i class="bi bi-person-circle user-icon me-2"></i>
        <span class="user-name me-3">Sánchez Cortes Felipe Martin</span>
        <button class="btn btn-logout">Cerrar Sesión</button>
      </div>
    </div>

    <div class="container-fluid main-content">
      
      <h1 class="page-title">Gestión de Préstamos</h1>

      <div class="card-container">
        <div class="mb-3">
            <a href="dashboard.html" class="back-link">
                <i class="bi bi-arrow-left-square-fill"></i> Volver al menú principal
            </a>
        </div>

        <div class="content-card">
            <h4 class="card-heading">Solicitudes de préstamos pendientes</h4>

            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Solicitante</th>
                            <th>Fecha de solicitud</th>
                            <th>Monto solicitado</th>
                            <th>Plazo (Quincenas)</th>
                            <th>Capacidad de pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1A</td>
                            <td>Joaquín Cázares</td>
                            <td>24/11/2025</td>
                            <td>$5,000.00</td>
                            <td>10</td>
                            <td>Suficiente</td>
                            <td><span class="badge status-pending">Pendiente</span></td>
                            <td>
                                <div class="action-buttons">
                                    <div class="d-flex gap-1 mb-1 justify-content-center">
                                        <button class="btn-action btn-approve">Aprobar</button>
                                        <button class="btn-action btn-reject">Rechazar</button>
                                    </div>
                                    <button class="btn-action btn-details w-100">Ver detalles</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2B</td>
                            <td>Diego Huerta</td>
                            <td>23/11/2025</td>
                            <td>$4,000.00</td>
                            <td>5</td>
                            <td>Límite</td>
                            <td><span class="badge status-pending">Pendiente</span></td>
                            <td>
                                <div class="action-buttons">
                                    <div class="d-flex gap-1 mb-1 justify-content-center">
                                        <button class="btn-action btn-approve">Aprobar</button>
                                        <button class="btn-action btn-reject">Rechazar</button>
                                    </div>
                                    <button class="btn-action btn-details w-100">Ver detalles</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3C</td>
                            <td>Ángel Hernández</td>
                            <td>22/11/2025</td>
                            <td>$3,000.00</td>
                            <td>20</td>
                            <td>Suficiente</td>
                            <td><span class="badge status-pending">Pendiente</span></td>
                            <td>
                                <div class="action-buttons">
                                    <div class="d-flex gap-1 mb-1 justify-content-center">
                                        <button class="btn-action btn-approve">Aprobar</button>
                                        <button class="btn-action btn-reject">Rechazar</button>
                                    </div>
                                    <button class="btn-action btn-details w-100">Ver detalles</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>4D</td>
                            <td>Juan Bello</td>
                            <td>21/11/2025</td>
                            <td>$2,000.00</td>
                            <td>15</td>
                            <td>Suficiente</td>
                            <td><span class="badge status-pending">Pendiente</span></td>
                            <td>
                                <div class="action-buttons">
                                    <div class="d-flex gap-1 mb-1 justify-content-center">
                                        <button class="btn-action btn-approve">Aprobar</button>
                                        <button class="btn-action btn-reject">Rechazar</button>
                                    </div>
                                    <button class="btn-action btn-details w-100">Ver detalles</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                <button class="btn-page"><i class="bi bi-caret-left-fill"></i>⏪</button>
                <button class="btn-page active">1</button>
                <button class="btn-page"><i class="bi bi-caret-right-fill"></i>⏩</button>
            </div>

        </div>
      </div>
    </div>

    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
  </body>
</html>
