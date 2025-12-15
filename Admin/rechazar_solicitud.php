<?php
// Admin/rechazar_solicitud.php
require_once '../includes/init.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) { 
    header("Location: ../login.php"); 
    exit(); 
}

if (isset($_GET['id']) && isset($_GET['tipo'])) {
    
    $id = $_GET['id'];
    $tipo = $_GET['tipo'];

    try {
        if ($tipo == 'prestamo') {
            // Opción A: Eliminar físicamente
            // $sql = "DELETE FROM solicitud_prestamo WHERE id_solicitud_prestamo = :id";
            
            // Opción B (Mejor): Marcar como Rechazada (Estado 3) para historial
            $sql = "UPDATE solicitud_prestamo SET id_estado = 3 WHERE id_solicitud_prestamo = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $msg = "Solicitud de préstamo rechazada.";

        } elseif ($tipo == 'ahorro') {
            // Opción B: Marcar como Rechazada (Estado 3)
            $sql = "UPDATE solicitud_ahorro SET id_estado = 3 WHERE id_solicitud_ahorro = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $msg = "Solicitud de ahorro rechazada.";
        }

        header("Location: gestion_prestamos.php?msg=" . urlencode($msg));
        exit();

    } catch (Exception $e) { 
        die("Error: " . $e->getMessage()); 
    }
}
?>