<?php
function get_user_name() {
    if (!isset($_SESSION['nombre'])) {
        return 'Usuario';
    }
    return htmlspecialchars($_SESSION['nombre']);
}
function get_user_role_text() {
    if (!isset($_SESSION['id_rol'])) {
        return 'Usuario';
    }
    
    $roles = [
        1 => 'Ahorrador',
        2 => 'Administrador',
        3 => 'SuperUsuario'
    ];
    
    return isset($roles[$_SESSION['id_rol']]) ? $roles[$_SESSION['id_rol']] : 'Usuario';
}
?>
