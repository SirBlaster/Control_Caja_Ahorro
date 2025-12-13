<?php
require_once '../includes/init.php';
secure_session_start();
check_login(1);

// Obtener actividades recientes
$actividades = obtener_actividades_recientes(10);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Panel - Ahorrador</title>

    <!-- Bootstrap (opcional, ayuda con grid y utilidades) -->
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <!-- Estilos locales -->
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>

    <header class="app-header">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <!-- RUTA LOCAL (entorno): /mnt/data/menuprincipal_Ahorrador.jpg
          En tu proyecto apunta a: img/logo.png o img/menuprincipal_Ahorrador.jpg según donde la guardes -->
                <img src="../img/LogoHorizontal - 2.png" alt="logo" class="header-logo">
                <span class="brand-name">SETDITSX - Sindicato ITSX</span>
            </div>

            <div class="user-actions d-flex align-items-center gap-3">
                <p class="user-name"><?php echo get_user_name(); ?></p>
                <small class="text-muted"><?php echo get_user_role_text(); ?></small>
            </div>
        </div>
    </header>

    <main class="container main-area mt-4">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="info-card p-4 shadow-sm">
                    <h6 class="card-sub">CAJA DE AHORRO</h6>
                    <div class="card-amount text-success">$16,751.00 MXN</div>
                    <div class="muted">Saldo total disponible</div>
                    <a href="movimientos.php" class="btn btn-outline-primary mt-3 btn-sm"> Ver movimientos </a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-card p-4 shadow-sm">
                    <h6 class="card-sub">PRÉSTAMO ACTIVO</h6>
                    <div class="card-amount loan-amount">$15,000.00 MXN</div>
                    <div class="muted">Saldo pendiente</div>
                    <a href="Estado_Prestamo.php" class="btn btn-outline-primary mt-3 btn-sm"> Consultar estado de
                        préstamo</a>
                </div>
            </div>
        </div>


        <section class="actions mb-4">
            <h6 class="section-title">ACCIONES DEL AHORRADOR</h6>
            <div class="d-flex justify-content-center gap-3 flex-wrap my-3">
                <a href="solicitud_prestamo.php" class="btn btn-warning px-4 rounded-pill"> Solicitar nuevo préstamo
                </a>
                <a href="registrahorro.php" class="btn btn-warning px-4 rounded-pill"> Solicitar ahorro / Registrar
                    nómina </a>
                <a href="estados_solicitudes.php" class="btn btn-warning px-4 rounded-pill"> Consultar el estado de las
                    solicitudes </a>
            </div>

        </section>

        <section class="movements">
            <h6 class="section-title">ÚLTIMOS MOVIMIENTOS - CAJA DE AHORRO</h6>

            <div class="table-wrap mt-2">
                <table class="table table-sm table-borderless">
                    <thead class="table-head">
                        <tr>
                            <th>ID</th>
                            <th>Fecha y Hora</th>
                            <th>Concepto</th>
                            <th class="text-end">Monto</th>
                            <th>Tipo de Movimiento</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1A</td>
                            <td>28/10/25 6:34 P.M.</td>
                            <td>Depósito Nómina</td>
                            <td class="text-end text-success">+$7,500.00</td>
                            <td>Depósito</td>
                            <td class="text-end">$17,500.00</td>
                        </tr>
                        <tr>
                            <td>2B</td>
                            <td>20/10/25 12:54 P.M.</td>
                            <td>Retiro Emergencia</td>
                            <td class="text-end text-danger">-$1,000.00</td>
                            <td>Retiro</td>
                            <td class="text-end">$16,500.00</td>
                        </tr>
                        <tr>
                            <td>3C</td>
                            <td>29/11/25 2:48 P.M.</td>
                            <td>Préstamo</td>
                            <td class="text-end text-danger">-$450.00</td>
                            <td>Pago</td>
                            <td class="text-end">$16,050.00</td>
                        </tr>
                        <tr>
                            <td>4D</td>
                            <td>25/12/25 7:00 A.M.</td>
                            <td>Suscripción</td>
                            <td class="text-end text-danger">-$299.00</td>
                            <td>Pago</td>
                            <td class="text-end">$15,751.00</td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-2">
                    <a href="historial-completo.php" class="text-primary fw-bold"> Ver historial completo</a>
                </div>

            </div>
        </section>
    </main>

    <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>