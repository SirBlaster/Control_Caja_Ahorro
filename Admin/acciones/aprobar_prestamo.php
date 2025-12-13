<?php
require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: ../gestion_prestamos.php');
    exit;
}

$id = (int) $_POST['id'];
$estado_aprobado = 2;

$sql = "
UPDATE solicitud_prestamo
SET id_estado = :estado
WHERE id_solicitud_prestamo = :id
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':estado', $estado_aprobado, PDO::PARAM_INT);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

header('Location: ../gestion_prestamos.php');
exit;