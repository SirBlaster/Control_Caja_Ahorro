<?php
// includes/admin_user_handlers.php
// Funciones para la gestión general de usuarios

//Obtiene todos los usuarios excepto superusuarios (rol 3)

function obtener_usuarios_admin()
{
    global $pdo;

    $sql = "SELECT u.id_usuario as id, 
                   u.nombre, 
                   u.apellido_paterno as paterno, 
                   u.apellido_materno as materno, 
                   CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as nombre_completo,
                   u.correo_institucional as email,
                   u.correo_personal as email_personal,
                   u.telefono,
                   u.id_rol as rol_id,
                   COALESCE(r.rol, 'No asignado') as nombre_rol,
                   u.rfc,
                   u.curp,
                   u.tarjeta,
                   u.habilitado
            FROM usuario u
            LEFT JOIN rol r ON u.id_rol = r.id_rol
            WHERE u.id_rol IN (1, 2)  -- Solo administradores (1) y ahorradores (2)
            ORDER BY u.apellido_paterno, u.apellido_materno, u.nombre";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener usuarios: " . $e->getMessage());
        return [];
    }
}

//Cambia el estado de un usuario (habilitar/deshabilitar)
function cambiar_estado_usuario($id_usuario)
{
    global $pdo;

    try {
        // Obtener estado actual
        $sql_estado = "SELECT habilitado FROM usuario WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql_estado);
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        // Cambiar estado (1 = habilitado, 0 = deshabilitado)
        $nuevo_estado = $usuario['habilitado'] == 1 ? 0 : 1;
        $accion = $nuevo_estado == 1 ? 'habilitado' : 'deshabilitado';

        $sql_update = "UPDATE usuario SET habilitado = ? WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute([$nuevo_estado, $id_usuario]);

        // Registrar actividad
        if (function_exists('registrar_auditoria')) {
            $detalle = "Usuario ID: $id_usuario fue $accion";
            registrar_auditoria("Usuario $accion", $detalle, $_SESSION['id_usuario'] ?? null);
        }

        return [
            'success' => true,
            'message' => "Usuario $accion correctamente",
            'nuevo_estado' => $nuevo_estado
        ];

    } catch (PDOException $e) {
        error_log("Error al cambiar estado de usuario: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error en la base de datos'];
    }
}

//Cambia el rol de un usuario (Administrador ↔ Ahorrador)

function cambiar_rol_usuario($id_usuario)
{
    global $pdo;

    try {
        // Obtener rol actual
        $sql_rol = "SELECT id_rol FROM usuario WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql_rol);
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        // Verificar que no sea SuperUsuario (rol 3)
        if ($usuario['id_rol'] == 3) {
            return ['success' => false, 'message' => 'No se puede cambiar el rol de un SuperUsuario'];
        }

        // CORRECCIÓN AQUÍ: Cambiar entre rol 1 (Ahorrador) y 2 (Administrador)
        $nuevo_rol = $usuario['id_rol'] == 1 ? 2 : 1;
        // CORRECCIÓN: Asignar nombres correctos según tu tabla
        $nuevo_rol_nombre = $nuevo_rol == 1 ? 'Ahorrador' : 'Administrador';
        $rol_anterior = $usuario['id_rol'] == 1 ? 'Ahorrador' : 'Administrador';

        $sql_update = "UPDATE usuario SET id_rol = ? WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute([$nuevo_rol, $id_usuario]);

        // Registrar actividad
        if (function_exists('registrar_auditoria')) {
            $detalle = "Usuario ID: $id_usuario cambió de $rol_anterior a $nuevo_rol_nombre";
            registrar_auditoria("Rol cambiado", $detalle, $_SESSION['id_usuario'] ?? null);
        }

        return [
            'success' => true,
            'message' => "Rol cambiado a $nuevo_rol_nombre correctamente",
            'nuevo_rol' => $nuevo_rol,
            'nuevo_rol_nombre' => $nuevo_rol_nombre
        ];

    } catch (PDOException $e) {
        error_log("Error al cambiar rol de usuario: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error en la base de datos'];
    }
}
//Obtiene los datos de un usuario por ID (cualquier usuario excepto SuperUsuario)

function obtener_usuario_por_id($id_usuario)
{
    global $pdo;

    $sql = "SELECT u.*, COALESCE(r.rol, 'No asignado') as nombre_rol 
            FROM usuario u 
            LEFT JOIN rol r ON u.id_rol = r.id_rol 
            WHERE u.id_usuario = ? AND u.id_rol != 3";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si el rol viene vacío, asignar manualmente
        if ($usuario && empty($usuario['nombre_rol']) && isset($usuario['id_rol'])) {
            if ($usuario['id_rol'] == 1) {
                $usuario['nombre_rol'] = 'Administrador';
            } elseif ($usuario['id_rol'] == 2) {
                $usuario['nombre_rol'] = 'Ahorrador';
            }
        }
        
        return $usuario;
    } catch (PDOException $e) {
        error_log("Error al obtener usuario: " . $e->getMessage());
        return null;
    }
}

//Actualiza los datos de un usuario (versión mejorada)

function actualizar_usuario_general($id_usuario, $datos)
{
    global $pdo;

    try {
        // Validar datos requeridos
        if (empty($datos['nombre']) || empty($datos['paterno']) || 
            empty($datos['correo_institucional']) || !isset($datos['id_rol'])) {
            return ['success' => false, 'message' => 'Datos requeridos faltantes'];
        }

        // Verificar que no sea SuperUsuario
        $usuario_actual = obtener_usuario_por_id($id_usuario);
        if (!$usuario_actual) {
            return ['success' => false, 'message' => 'Usuario no encontrado o es SuperUsuario'];
        }

        // Verificar correo único si cambió
        if ($datos['correo_institucional'] != $usuario_actual['correo_institucional']) {
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE correo_institucional = ? AND id_usuario != ?");
            $stmt->execute([$datos['correo_institucional'], $id_usuario]);
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'El correo institucional ya está en uso'];
            }
        }

        // Construir SQL dinámico
        $campos = [
            'nombre = :nombre',
            'apellido_paterno = :paterno',
            'apellido_materno = :materno',
            'correo_institucional = :email',
            'correo_personal = :email_personal',
            'telefono = :telefono',
            'rfc = :rfc',
            'curp = :curp',
            'id_rol = :rol_id',
            'habilitado = :habilitado'
        ];

        $sql = "UPDATE usuario SET " . implode(', ', $campos) . " WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        
        // Ejecutar con valores por defecto si no existen
        $resultado = $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':paterno' => $datos['paterno'],
            ':materno' => $datos['materno'] ?? '',
            ':email' => $datos['correo_institucional'],
            ':email_personal' => $datos['correo_personal'] ?? '',
            ':telefono' => $datos['telefono'] ?? '',
            ':rfc' => $datos['rfc'] ?? '',
            ':curp' => $datos['curp'] ?? '',
            ':rol_id' => $datos['id_rol'],
            ':habilitado' => $datos['habilitado'] ?? 1,
            ':id' => $id_usuario
        ]);

        if (!$resultado) {
            return ['success' => false, 'message' => 'Error al ejecutar la consulta'];
        }

        // Registrar actividad
        if (function_exists('registrar_auditoria')) {
            $detalle = "Usuario ID: $id_usuario actualizado - " . 
                      "Nombre: " . $datos['nombre'] . " " . $datos['paterno'] . 
                      ", Rol: " . $datos['id_rol'] . 
                      ", Estado: " . ($datos['habilitado'] ?? 1);
            registrar_auditoria("Usuario actualizado", $detalle, $_SESSION['id_usuario'] ?? null);
        }

        return ['success' => true, 'message' => 'Usuario actualizado correctamente'];

    } catch (PDOException $e) {
        error_log("Error al actualizar usuario: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al actualizar usuario: ' . $e->getMessage()];
    }
}

//Función segura para obtener datos de usuario con todos los campos

function obtener_usuario_completo($id_usuario)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT 
                u.*, 
                COALESCE(r.rol, 'No asignado') as rol,
                COALESCE(u.telefono, '') as telefono,
                COALESCE(u.rfc, '') as rfc,
                COALESCE(u.curp, '') as curp,
                COALESCE(u.correo_personal, '') as correo_personal,
                COALESCE(u.correo_institucional, '') as correo_institucional,
                COALESCE(u.tarjeta, '') as tarjeta
            FROM usuario u 
            LEFT JOIN rol r ON u.id_rol = r.id_rol 
            WHERE u.id_usuario = ? AND u.id_rol != 3
        ");
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si no hay rol asignado en la BD, asignar manualmente
        if ($usuario && $usuario['rol'] == 'No asignado' && isset($usuario['id_rol'])) {
            if ($usuario['id_rol'] == 1) {
                $usuario['rol'] = 'Administrador';
            } elseif ($usuario['id_rol'] == 2) {
                $usuario['rol'] = 'Ahorrador';
            }
        }
        
        return $usuario;
        
    } catch (PDOException $e) {
        error_log("Error en obtener_usuario_completo: " . $e->getMessage());
        return null;
    }
}
//Función específica para obtener datos de SuperUsuario

function obtener_superusuario_completo($id_usuario)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT 
                u.*, 
                COALESCE(r.rol, 'Super Usuario') as rol,
                COALESCE(u.telefono, '') as telefono,
                COALESCE(u.rfc, '') as rfc,
                COALESCE(u.curp, '') as curp,
                COALESCE(u.correo_personal, '') as correo_personal,
                COALESCE(u.correo_institucional, '') as correo_institucional,
                COALESCE(u.tarjeta, '') as tarjeta
            FROM usuario u 
            LEFT JOIN rol r ON u.id_rol = r.id_rol 
            WHERE u.id_usuario = ?  -- NO excluye SuperUsuario
        ");
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $usuario;
        
    } catch (PDOException $e) {
        error_log("Error en obtener_superusuario_completo: " . $e->getMessage());
        return null;
    }
}
function obtener_usuarios_ahorrador($limit = 10, $offset = 0)
{
    global $pdo;

    $sql = "SELECT
            u.id_usuario AS id,
            u.nombre,
            u.apellido_paterno AS paterno,
            u.apellido_materno AS materno,
            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS nombre_completo,
            u.correo_institucional AS email,
            u.correo_personal AS email_personal,
            u.telefono,
            u.id_rol AS rol_id,
            COALESCE(r.rol, 'No asignado') AS nombre_rol,
            u.rfc,
            u.curp,
            u.tarjeta,
            u.habilitado
            FROM usuario u
            LEFT JOIN rol r ON u.id_rol = r.id_rol
            WHERE u.id_rol = 2  -- CAMBIADO: 2 = Ahorrador según tu tabla
            ORDER BY u.apellido_paterno, u.apellido_materno, u.nombre
            LIMIT :limit OFFSET :offset";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener usuarios: " . $e->getMessage());
        return [];
    }
}


function contar_usuarios_ahorrador()
{
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuario WHERE id_rol = 2"); // CAMBIADO: 2 = Ahorrador
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error al contar usuarios: " . $e->getMessage());
        return 0;
    }
}
?>