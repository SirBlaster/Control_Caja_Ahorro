<?php
// Usuario/logica_movimientos.php

$id_usuario = $_SESSION['id_usuario'] ?? null;
$lista_movimientos = [];

if ($id_usuario) {
    try {
        // Consultamos la tabla 'movimiento' unida con 'tipo_movimiento'
        // Ordenamos por FECHA DESCENDENTE (Lo más nuevo arriba)
        $sql = "SELECT m.id_movimiento, m.fecha, m.concepto, m.monto, m.id_tipo_movimiento, tm.tipo_movimiento
                FROM movimiento m
                JOIN tipo_movimiento tm ON m.id_tipo_movimiento = tm.id_tipo_movimiento
                WHERE m.id_usuario = ?
                ORDER BY m.fecha DESC, m.id_movimiento DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        $lista_movimientos = $stmt->fetchAll();

    } catch (PDOException $e) {
        $error_db = "Error al cargar movimientos: " . $e->getMessage();
    }
}
?>