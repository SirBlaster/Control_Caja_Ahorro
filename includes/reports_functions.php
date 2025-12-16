<?php
require_once __DIR__ . '/init.php';

/**
 * Genera un archivo CSV con los reportes de Ahorros y Préstamos de una quincena específica
 *
 * @param PDO $pdo Conexión a la base de datos
 * @param string $mes Mes en formato YYYY-MM
 * @param int $quincena 1 o 2
 */
function generarReporteQuincenalCSV(PDO $pdo, string $mes, int $quincena)
{
    // Fechas de inicio y fin según quincena
    $dia_inicio = ($quincena == 1) ? '01' : '16';
    $dia_fin = ($quincena == 1) ? '15' : date("t", strtotime($mes.'-01'));

    $fecha_inicio = $mes.'-'.$dia_inicio.' 00:00:00';
    $fecha_fin = $mes.'-'.$dia_fin.' 23:59:59';

    // ==============================
    // CSV de Ahorros
    // ==============================
    $sql_ahorros = "
        SELECT
            u.id_usuario,
            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS nombre_completo,
            SUM(CASE WHEN tm.tipo_movimiento = 'Depósito' THEN m.monto ELSE 0 END) AS total_depositos,
            SUM(CASE WHEN tm.tipo_movimiento = 'Retiro' THEN m.monto ELSE 0 END) AS total_retiros,
            SUM(CASE WHEN tm.tipo_movimiento = 'Depósito' THEN m.monto ELSE 0 END)
                - SUM(CASE WHEN tm.tipo_movimiento = 'Retiro' THEN m.monto ELSE 0 END) AS saldo_quincenal
        FROM usuario u
        LEFT JOIN movimiento m 
            ON m.id_usuario = u.id_usuario 
            AND m.fecha BETWEEN :fecha_inicio AND :fecha_fin
        LEFT JOIN tipo_movimiento tm ON m.id_tipo_movimiento = tm.id_tipo_movimiento
            AND tm.tipo_movimiento IN ('Depósito','Retiro')
        GROUP BY u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno
        ORDER BY u.id_usuario
    ";

    $stmt = $pdo->prepare($sql_ahorros);
    $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
    $ahorros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ==============================
    // CSV de Préstamos
    // ==============================
    $sql_prestamos = "
        SELECT
            u.id_usuario,
            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS nombre_completo,
            SUM(sp.monto_solicitado) AS total_solicitado,
            SUM(sp.total_a_pagar) AS total_a_pagar,
            SUM(sp.saldo_pendiente) AS saldo_pendiente
        FROM usuario u
        LEFT JOIN solicitud_prestamo sp 
            ON sp.id_usuario = u.id_usuario
            AND sp.id_estado IN (2,4)
            AND sp.fecha_solicitud BETWEEN :fecha_inicio AND :fecha_fin
        GROUP BY u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno
        ORDER BY u.id_usuario
    ";

    $stmt2 = $pdo->prepare($sql_prestamos);
    $stmt2->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
    $prestamos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // ==============================
    // Descargar CSV
    // ==============================
    $filename = "Reporte_Quincenal_{$mes}_Q{$quincena}.csv";
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $output = fopen('php://output', 'w');

    // --- Ahorros ---
    fputcsv($output, ['AHORROS']);
    fputcsv($output, ['ID Usuario', 'Nombre Completo', 'Total Depósitos', 'Total Retiros', 'Saldo Quincenal']);
    foreach ($ahorros as $row) {
        fputcsv($output, $row);
    }

    fputcsv($output, []); // línea vacía

    // --- Préstamos ---
    fputcsv($output, ['PRÉSTAMOS']);
    fputcsv($output, ['ID Usuario', 'Nombre Completo', 'Total Solicitado', 'Total a Pagar', 'Saldo Pendiente']);
    foreach ($prestamos as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}