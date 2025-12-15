<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar Sesión - SETDITSX</title>

    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">

    <script>
    if (performance.navigation.type === 2) {
        location.reload(true);
    }
    </script>
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

        <form action="includes/procesar_login.php" method="POST">


            <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['logout'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Sesión cerrada exitosamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <input type="email" name="correo" class="form-control" id="email" placeholder="Correo electrónico"
                    required />
            </div>

            <div class="mb-3">
                <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña"
                    required />
            </div>

            <button type="submit" class="btn btn-golden">Ingresar</button>
            <a href="registro.php" class="register-link">Registrarme</a>
        </form>
    </main>

    <script src="js/bootstrap/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('email').focus();
    });
    </script>
</body>

</html>