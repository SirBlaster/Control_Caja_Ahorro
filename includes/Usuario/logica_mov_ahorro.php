<?php
// Usuario/logica_movimientos_ahorro.php

$id_usuario = $_SESSION['id_usuario'] ?? null;
$movimientos_ahorro = [];

if ($id_usuario) {
    try {
        // CONSULTA FILTRADA
        // Traemos movimiento + tipo_movimiento
        // FILTRO: id_tipo_movimiento IN (1, 2) 
        // -> 1 = Depósito (Entra dinero al ahorro)
        // -> 2 = Retiro (Sale dinero del ahorro)
        // (El 3 es Pago de Préstamo, así que lo ignoramos aquí)
        
        $sql = "SELECT m.id_movimiento, m.fecha, m.concepto, m.monto, m.id_tipo_movimiento, tm.tipo_movimiento
                FROM movimiento m
                JOIN tipo_movimiento tm ON m.id_tipo_movimiento = tm.id_tipo_movimiento
                WHERE m.id_usuario = ? 
                AND m.id_tipo_movimiento IN (1, 2)
                ORDER BY m.fecha DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        $movimientos_ahorro = $stmt->fetchAll();

    } catch (PDOException $e) {
        // En producción puedes quitar esto, ayuda a depurar
        error_log("Error DB: " . $e->getMessage());
    }
}
?>