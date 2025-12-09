<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro - SETDITSX</title>
    <link rel="stylesheet" href="css/registrar.css">
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <div class="header d-flex align-items-center p-3">
        <img src="img/NewLogo - 1.png" alt="SETDITSX" width="70" class="me-3">
        <h4>SETDITSX - Sindicato ITSX</h4>
    </div>

    <div class="card card-form container mt-4 p-4 shadow-sm" style="max-width: 800px;">
        
        <h2 class="text-center mb-4">Registro de Nuevo Socio</h2>

        <form action="includes/procesar_registro.php" method="POST">

            <h5 class="section-title border-bottom pb-2 mb-3">Datos Personales</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre(s)" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="paterno" class="form-control" placeholder="Apellido Paterno" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="materno" class="form-control" placeholder="Apellido Materno" required>
                </div>
                
                <div class="col-md-6">
                    <input type="text" name="curp" class="form-control" placeholder="CURP" maxlength="18" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="rfc" class="form-control" placeholder="RFC" maxlength="13" required>
                </div>
            </div>

            <h5 class="section-title border-bottom pb-2 mb-3">Información de Contacto y Laboral</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <input type="email" name="correo_personal" class="form-control" placeholder="Correo personal" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="telefono" class="form-control" placeholder="Número celular (10 dígitos)" maxlength="15" required>
                </div>
                <div class="col-md-6">
                    <input type="email" name="correo_institucional" class="form-control" placeholder="Correo institucional" required>
                </div>
            </div>

            <h5 class="section-title border-bottom pb-2 mb-3">Información de Depósito</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Selecciona método de depósito</label>
                    <select class="form-select" name="tipo_cuenta" id="tipoCuenta" required>
                        <option value="" selected disabled>Selecciona una opción</option>
                        <option value="clabe">CLABE Interbancaria (18 dígitos)</option>
                        <option value="tarjeta">Número de Tarjeta (16 dígitos)</option>
                    </select>
                </div>
                
                <div class="col-md-6" id="contenedorCampo" style="display:none;">
                    <label class="form-label" id="labelCampo">Número</label>
                    <input type="text" name="dato_bancario" class="form-control" id="inputCampo" maxlength="18">
                    <small class="text-danger d-none" id="mensajeError">Longitud incorrecta</small>
                </div>

                <div class="col-12">
                    <small class="d-flex align-items-center p-2 bg-light border rounded" style="color:#d18819; font-weight:500;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Verifique que la cuenta esté correcta. No nos hacemos responsables por errores de captura.
                    </small>
                </div>
            </div>

            <h5 class="section-title border-bottom pb-2 mb-3">Seguridad</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                </div>
                <div class="col-md-6">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirmar contraseña" required>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terminos" required>
                        <label class="form-check-label" for="terminos">
                            Acepto los términos y condiciones del sindicato.
                        </label>
                    </div>
                </div>

                <div class="col-md-12 text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg w-50" style="background-color: #d18819; border: none;">REGISTRARME</button>
                </div>
                <div class="col-md-12 text-center">
                    <a href="login.php" class="text-decoration-none text-muted">¿Ya tienes cuenta? Ingresa aquí</a>
                </div>
            </div>
        </form>
    </div>

    <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="js/registro.js"></script>
</body>
</html>



