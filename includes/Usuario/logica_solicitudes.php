<?php
// Usuario/logica_solicitudes.php

// Verificar si hay usuario logueado
$id_usuario = $_SESSION['id_usuario'] ?? null;

$ahorros = [];
$prestamos = [];

if ($id_usuario) {
    // 1. CONSULTAR AHORROS
    // Corrección: Nombres de tablas en minúsculas y columna e.estado
    $sqlAhorro = "SELECT s.id_solicitud_ahorro, s.fecha, s.monto_solicitado, s.id_estado, e.estado, s.archivo_solicitud
                    FROM solicitud_ahorro s
                    JOIN estado e ON s.id_estado = e.id_estado
                    WHERE s.id_usuario = ?
                    ORDER BY s.fecha DESC";
    $stmtAhorro = $pdo->prepare($sqlAhorro);
    $stmtAhorro->execute([$id_usuario]);
    $ahorros = $stmtAhorro->fetchAll();

    // 2. CONSULTAR PRÉSTAMOS
    // Corrección: Nombres de tablas en minúsculas y e.estado
    $sqlPrestamo = "SELECT p.id_solicitud_prestamo, p.fecha_solicitud, p.monto_solicitado, p.total_a_pagar, p.id_estado, e.estado
                    FROM solicitud_prestamo p
                    JOIN estado e ON p.id_estado = e.id_estado
                    WHERE p.id_usuario = ?
                    ORDER BY p.fecha_solicitud DESC";
    $stmtPrestamo = $pdo->prepare($sqlPrestamo);
    $stmtPrestamo->execute([$id_usuario]);
    $prestamos = $stmtPrestamo->fetchAll();
}

// 3. FUNCIÓN DE COLORES (Semaforización)
// Define el color de la etiqueta según el ID del estado en tu BD
function colorEstado($id) {
    switch ($id) {
        case 1: return 'bg-warning text-dark'; // Pendiente
        case 2: return 'bg-success';           // Aprobado
        case 3: return 'bg-danger';            // Rechazado
        case 4: return 'bg-info text-dark';    // Pagado
        case 5: return 'bg-secondary';         // Cancelado (Agregado según tu BD)
        default: return 'bg-light text-dark';  // Desconocido
    }
}
?>