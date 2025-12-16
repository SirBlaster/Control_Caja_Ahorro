<?php
// Usuario/procesar_prestamo.php
require_once '../includes/init.php';

secure_session_start();
check_login(1);

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $monto_solicitado = floatval($_POST['monto']);
    $plazo_quincenas = intval($_POST['plazo']);
    $id_usuario = $_SESSION['id_usuario'];

    if ($monto_solicitado <= 0) {
        header("Location: solicitud_prestamo.php?error=Monto inválido");
        exit();
    }

    try {
        // --- CÁLCULOS FINANCIEROS (30% Interés) ---
        $tasa_interes = 0.30;
        $interes_generado = $monto_solicitado * $tasa_interes;
        $total_a_pagar = $monto_solicitado + $interes_generado;
        
        // Saldo pendiente inicial = Total Deuda
        $saldo_pendiente = $total_a_pagar; 
        
        $id_estado = 1; // 1 = Pendiente
        $archivo_pagare = "pendiente_aprobacion"; // Temporal hasta que Admin apruebe

        // INSERT CON NOMBRES NUEVOS DE LA BASE DE DATOS
        $sql = "INSERT INTO solicitud_prestamo 
                (fecha_solicitud, monto_solicitado, plazo_quincenas, total_a_pagar, saldo_pendiente, archivo_pagare, id_usuario, id_estado) 
                VALUES (NOW(), :monto, :plazo, :total, :saldo, :pagare, :usuario, :estado)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':monto' => $monto_solicitado,
            ':plazo' => $plazo_quincenas,
            ':total' => $total_a_pagar,
            ':saldo' => $saldo_pendiente,
            ':pagare' => $archivo_pagare,
            ':usuario' => $id_usuario,
            ':estado' => $id_estado
        ]);

        // Mensaje según mes
        $esDiciembre = (intval(date('m')) == 12);
        if ($esDiciembre) {
            $msg = "Solicitud en lista de espera. Se procesará la solicitud en Enero.";
        } else {
            $msg = "Solicitud envidada. Espera a la aprobación del administrador.";
        }

        header("Location: panelAhorrador.php?msg=" . urlencode($msg));
        exit();

    } catch (PDOException $e) {
        $error = "Error BD: " . $e->getMessage();
        header("Location: solicitud_prestamo.php?error=" . urlencode($error));
        exit();
    }
} else {
    header("Location: solicitud_prestamo.php");
    exit();
}
?>