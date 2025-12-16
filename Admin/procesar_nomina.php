<?php
// Admin/procesar_nomina.php
require_once '../includes/init.php';
secure_session_start();


if (!isset($_SESSION['id_rol']) || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) {
    die("Acceso denegado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        $pdo->beginTransaction();


        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, sa.monto_solicitado
                FROM usuario u
                JOIN solicitud_ahorro sa ON u.id_usuario = sa.id_usuario
                WHERE sa.id_solicitud_ahorro = (
                    SELECT MAX(id_solicitud_ahorro) 
                    FROM solicitud_ahorro 
                    WHERE id_usuario = u.id_usuario AND id_estado = 2
                )";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $usuarios_activos = $stmt->fetchAll();

        $contador = 0;
        $fecha_hoy = date('Y-m-d H:i:s');
        $concepto = "Descuento Quincenal Nómina (" . date('d/m/Y') . ")";

        // 2. INSERTAR EL MOVIMIENTO PARA CADA UNO
        $sqlInsert = "INSERT INTO movimiento (fecha, concepto, monto, id_usuario, id_tipo_movimiento) 
                      VALUES (:fecha, :concepto, :monto, :id_usuario, 1)"; // 1 = Depósito
        
        $stmtInsert = $pdo->prepare($sqlInsert);

        foreach ($usuarios_activos as $user) {
            $stmtInsert->execute([
                ':fecha' => $fecha_hoy,
                ':concepto' => $concepto,
                ':monto' => $user['monto_solicitado'],
                ':id_usuario' => $user['id_usuario']
            ]);
            $contador++;
        }

        $pdo->commit();
        
        // Redirigir con mensaje de éxito
        header("Location: gestion_nominas.php?msg=Se procesaron $contador descuentos correctamente.");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: gestion_nominas.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>