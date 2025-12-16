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



function actualizar_perfil_usuario($id_usuario, $data) {
    global $pdo;

    if ($id_usuario <= 0) {
        return ['success' => false, 'message' => 'ID de usuario inválido'];
    }

    try {
        $fields = [
            'nombre' => $data['nombre'] ?? '',
            'apellido_paterno' => $data['paterno'] ?? '',
            'apellido_materno' => $data['materno'] ?? '',
            'correo_personal' => $data['correo_personal'] ?? '',
            'correo_institucional' => $data['correo_institucional'] ?? '',
            'telefono' => $data['telefono'] ?? '',
            'rfc' => $data['rfc'] ?? '',
            'curp' => $data['curp'] ?? ''
        ];

        $set_parts = [];
        $params = [];
        foreach ($fields as $col => $val) {
            $set_parts[] = "$col = :$col";
            $params[":$col"] = $val;
        }

        // Contraseña opcional
        if (!empty($data['password'])) {
            $set_parts[] = "contrasena = :contrasena";
            $params[':contrasena'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($set_parts)) {
            return ['success' => false, 'message' => 'No hay datos para actualizar'];
        }

        $params[':id_usuario'] = $id_usuario;
        $sql = "UPDATE usuario SET " . implode(", ", $set_parts) . " WHERE id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return ['success' => true, 'message' => 'Perfil actualizado correctamente'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error al actualizar perfil: ' . $e->getMessage()];
    }
}

?>