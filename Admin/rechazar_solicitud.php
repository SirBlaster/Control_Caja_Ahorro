<?php
// Admin/rechazar_solicitud.php
require_once '../includes/init.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) { header("Location: ../login.php"); exit(); }

if (isset($_GET['id'])) {
    try {
        $sql = "DELETE FROM solicitud_prestamo WHERE id_solicitud_prestamo = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $_GET['id']]);

        header("Location: gestion_prestamos.php?msg=Solicitud eliminada.");
        exit();
    } catch (Exception $e) { die("Error: " . $e->getMessage()); }
}
?>