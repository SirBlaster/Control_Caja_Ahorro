<?php
// Navbar reutilizable para administrador
// Define la página actual
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <?php
            // Lista de items del navbar
            $menu = [
                'inicio.php' => ['icon' => 'bi-house-door-fill', 'label' => 'Inicio', 'href' => './inicio.php'],
                'gestion_prestamos.php' => ['icon' => 'bi-cash-stack', 'label' => 'Gestión de préstamos y ahorros', 'href' => './gestion_prestamos.php'],
                'gestion_ahorradores.php' => ['icon' => 'bi-people-fill', 'label' => 'Usuarios', 'href' => './gestion_ahorradores.php'],
                'reportes.php' => ['icon' => 'bi-file-earmark-text-fill', 'label' => 'Reportes', 'href' => './reportes.php'],
                'editar_perfil.php' => ['icon' => 'bi-gear-fill', 'label' => 'Configuración', 'href' => './editar_perfil.php'],
            ];

            foreach ($menu as $file => $item) {
                $active = ($current_page === $file) ? 'active' : '';
                echo '<li class="nav-item">';
                echo '<a class="nav-link ' . $active . '" href="' . $item['href'] . '">';
                echo '<i class="bi ' . $item['icon'] . ' me-1"></i>' . $item['label'];
                echo '</a>';
                echo '</li>';
            }
            ?>
        </ul>
    </div>
</nav>