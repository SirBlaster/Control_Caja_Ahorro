<?php
require_once __DIR__ . '/../../includes/init.php';

// Estado pendiente
$estado_pendiente = 1;

/* ===============================
   CONSULTA PRINCIPAL (PAGINADA)
================================ */
$sql = "
SELECT
    sp.id_solicitud_prestamo,
    CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS solicitante,
    sp.fecha_solicitud,
    sp.monto_solicitado,
    sp.plazo_quincenas,
    sp.monto_pago_quincenal,
    sp.total_a_pagar,
    e.estado
FROM solicitud_prestamo sp
INNER JOIN usuario u ON sp.id_usuario = u.id_usuario
INNER JOIN estado e ON sp.id_estado = e.id_estado
WHERE sp.id_estado = :estado
ORDER BY sp.fecha_solicitud ASC
LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':estado', $estado_pendiente, PDO::PARAM_INT);
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$prestamos = $stmt->fetchAll();

/* ===============================
   TOTAL DE REGISTROS
================================ */
$sql_total = "
SELECT COUNT(*)
FROM solicitud_prestamo
WHERE id_estado = :estado
";

$stmt_total = $pdo->prepare($sql_total);
$stmt_total->bindValue(':estado', $estado_pendiente, PDO::PARAM_INT);
$stmt_total->execute();

$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $por_pagina);