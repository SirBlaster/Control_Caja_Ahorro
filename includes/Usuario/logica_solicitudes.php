<?php
// Usuario/logica_solicitudes.php

// Verificar si hay usuario logueado
$id_usuario = $_SESSION['id_usuario'] ?? null;

$ahorros = [];
$prestamos = [];

if ($id_usuario) {
    // 1. CONSULTAR AHORROS
    // Traemos Id, Fecha, Monto, Estado y el nombre del PDF
    $sqlAhorro = "SELECT s.Id_SolicitudAhorro, s.Fecha, s.Monto, s.Id_Estado, e.Estado, s.ArchivoSolicitud
                FROM Solicitud_Ahorro s
                JOIN Estado e ON s.Id_Estado = e.Id_Estado
                WHERE s.Id_Ahorrador = ?
                ORDER BY s.Fecha DESC";
    $stmtAhorro = $pdo->prepare($sqlAhorro);
    $stmtAhorro->execute([$id_usuario]);
    $ahorros = $stmtAhorro->fetchAll();

    // 2. CONSULTAR PRÉSTAMOS
    // Traemos datos del préstamo
    $sqlPrestamo = "SELECT p.Id_SolicitudPrestamo, p.FechaSolicitud, p.MontoSolicitado, p.Total_A_Pagar, p.Id_Estado, e.Estado
                    FROM Solicitud_Prestamo p
                    JOIN Estado e ON p.Id_Estado = e.Id_Estado
                    WHERE p.Id_Ahorrador = ?
                    ORDER BY p.FechaSolicitud DESC";
    $stmtPrestamo = $pdo->prepare($sqlPrestamo);
    $stmtPrestamo->execute([$id_usuario]);
    $prestamos = $stmtPrestamo->fetchAll();
}

// 3. FUNCIÓN DE COLORES (Semaforización)
// Define el color de la etiqueta según el estado
function colorEstado($id) {
    switch ($id) {
        case 1: return 'bg-warning text-dark'; // Pendiente (Amarillo)
        case 2: return 'bg-success';           // Aprobado (Verde)
        case 3: return 'bg-danger';            // Rechazado (Rojo)
        case 4: return 'bg-info text-dark';    // Pagado (Azul)
        default: return 'bg-secondary';        // Gris
    }
}
?>