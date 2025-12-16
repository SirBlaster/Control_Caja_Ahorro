<?php
// Usuario/logica_panel.php

// Verificar que ya tengamos sesión y conexión (init.php debe cargarse antes)
$id_usuario = $_SESSION['id_usuario'] ?? null;

// Inicializar variables en 0
$saldo_total = 0.00;
$saldo_prestamo = 0.00;
$movimientos = [];

$porcentaje_Rendimiento = 0;

try {
    $sql = "SELECT rendimiento_anual_ahorros FROM datos_sistema LIMIT 1";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $porcentaje_Rendimiento = $resultado['rendimiento_anual_ahorros'];
    }

} catch (PDOException $e) {
    error_log("Error al obtener rendimiento: " . $e->getMessage());
}


if ($id_usuario) {


    try {
        $sqlSaldo = "SELECT 
                        SUM(CASE WHEN id_tipo_movimiento = 1 THEN monto ELSE 0 END) as TotalDepositos,
                        SUM(CASE WHEN id_tipo_movimiento = 2 THEN monto ELSE 0 END) as TotalRetiros
                    FROM movimiento 
                    WHERE id_usuario = ?";
        
        $stmtSaldo = $pdo->prepare($sqlSaldo);
        $stmtSaldo->execute([$id_usuario]);
        $resSaldo = $stmtSaldo->fetch();

        if ($resSaldo) {
            $saldo_total = $resSaldo['TotalDepositos'] - $resSaldo['TotalRetiros'];
        }
    } catch (Exception $e) {
        $saldo_total = 0.00;
    }


    try {
        $sqlPrest = "SELECT SUM(saldo_pendiente) 
                    FROM solicitud_prestamo 
                     WHERE id_usuario = ? AND id_estado = 2"; // 2 = Aprobado

        $stmtPrest = $pdo->prepare($sqlPrest);
        $stmtPrest->execute([$id_usuario]);
        $saldo_prestamo = $stmtPrest->fetchColumn() ?: 0.00;
    } catch (Exception $e) {
        $saldo_prestamo = 0.00;
    }


    try {
        
        $sqlMovs = "SELECT 
                        m.fecha, 
                        m.concepto, 
                        m.monto, 
                        m.id_tipo_movimiento, 
                        tm.tipo_movimiento AS etiqueta_tipo 
                    FROM movimiento m
                    LEFT JOIN tipo_movimiento tm ON m.id_tipo_movimiento = tm.id_tipo_movimiento
                    WHERE m.id_usuario = ?
                    ORDER BY m.fecha DESC 
                    LIMIT 5";
                    
        $stmtMovs = $pdo->prepare($sqlMovs);
        $stmtMovs->execute([$id_usuario]);
        $movimientos = $stmtMovs->fetchAll();
    } catch (Exception $e) {
        $movimientos = [];
    }
}
?>