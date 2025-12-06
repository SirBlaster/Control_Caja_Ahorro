<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar Sesión - SETDITSX</title>

    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    </head>
    <body>

    <div class="page-header">

        <img src="img/NewLogo - 1.png" alt="logo" class="brand-logo" />
        <div>
            <div style="font-weight:700;color:#153b52;">SETDITSX - Sindicato ITSX</div>
        </div>
    </div>

    <main class="card card-login">
        <h1 class="title">Iniciar Sesión</h1>

    <form action="procesar_login.php" method="POST">
        <?php if(isset($_GET['error'])): ?>
            <p style="color: red; text-align: center;">Usuario o contraseña incorrectos</p>
        <?php endif; ?>

        <div class="mb-3">
            <input type="email" name="correo" class="form-control" id="email" placeholder="Correo electrónico" required />
        </div>

        <div class="mb-3">
            <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña" required />
        </div>

        <button type="submit" class="btn btn-golden">Ingresar</button>
        <a href="registro.php" class="register-link">Registrarme</a>
    </form>
    </main>

    
    <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
    </body>
</html>
