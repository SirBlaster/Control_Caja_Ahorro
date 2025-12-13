<?php
// Usuario/logica_panel.php

// Verificar que ya tengamos sesión y conexión (init.php debe cargarse antes)
$id_usuario = $_SESSION['id_usuario'] ?? null;

// Inicializar variables en 0
$saldo_total = 0.00;
$saldo_prestamo = 0.00;
$movimientos = [];

if ($id_usuario) {

    try {
        $sqlSaldo = "SELECT 
                        SUM(CASE WHEN Id_TipoMovimiento = 1 THEN Monto ELSE 0 END) as TotalDepositos,
                        SUM(CASE WHEN Id_TipoMovimiento = 2 THEN Monto ELSE 0 END) as TotalRetiros
                        FROM Movimientos 
                        WHERE Id_Ahorrador = ?";
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
        $sqlPrest = "SELECT SUM(Total_A_Pagar) 
                        FROM Solicitud_Prestamo 
                        WHERE Id_Ahorrador = ? AND Id_Estado = 2"; 
        $stmtPrest = $pdo->prepare($sqlPrest);
        $stmtPrest->execute([$id_usuario]);
        $saldo_prestamo = $stmtPrest->fetchColumn() ?: 0.00;
    } catch (Exception $e) {
        $saldo_prestamo = 0.00;
    }

    try {
        $sqlMovs = "SELECT m.Fecha, m.Concepto, m.Monto, m.Id_TipoMovimiento, tm.Descripcion as Tipo
                    FROM Movimientos m
                    LEFT JOIN TipoMovimiento tm ON m.Id_TipoMovimiento = tm.Id_TipoMovimiento
                    WHERE m.Id_Ahorrador = ?
                    ORDER BY m.Fecha DESC 
                    LIMIT 5";
        $stmtMovs = $pdo->prepare($sqlMovs);
        $stmtMovs->execute([$id_usuario]);
        $movimientos = $stmtMovs->fetchAll();
    } catch (Exception $e) {
        $movimientos = [];
    }
}
?>