<?php
// Admin/aprobar_solicitud.php
require_once '../includes/init.php';
require_once '../includes/fpdf/fpdf.php';

// Verificar Admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    die("Acceso denegado.");
}

if (isset($_GET['id'])) {
    $id_solicitud = $_GET['id'];

    try {
        // 1. OBTENER DATOS (Nueva estructura de tablas)
        $sql = "SELECT s.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.telefono 
                FROM solicitud_prestamo s
                JOIN usuario u ON s.id_usuario = u.id_usuario
                WHERE s.id_solicitud_prestamo = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_solicitud]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$datos) {
            die("Solicitud no encontrada.");
        }

        // Datos Prestamo (Nombres de columna actualizados)
        $monto_base = $datos['monto_solicitado'];      // Capital (Ej: 5000)
        $total_deuda = $datos['total_a_pagar'];       // Total a pagar (Ej: 6500)
        $plazo = $datos['plazo_quincenas'];
        
        // CÁLCULO DE INTERÉS GENERADO para el desglose en el PDF
        $interes_generado = $total_deuda - $monto_base; // Interés (Ej: 1500)
        
        // Datos Personales
        $nombreDeudorCompleto = $datos['nombre'] . ' ' . $datos['apellido_paterno'] . ' ' . $datos['apellido_materno'];
        $nombreDeudorFirma = mb_strtoupper($datos['nombre'] . ' ' . $datos['apellido_paterno'] . ' ' . $datos['apellido_materno']);
        $telefono = $datos['telefono'];

        // --- FORMATOS ---
        setlocale(LC_TIME, 'es_ES.UTF-8', 'esp');
        $fechaActual = date('d/m/Y');
        
        $monto_base_fmt = number_format($monto_base, 2);
        $total_deuda_fmt = number_format($total_deuda, 2);
        $interes_generado_fmt = number_format($interes_generado, 2);

        // --- GENERAR PDF ---
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Logo
        $rutaLogo = '../img/NewLogo - 1.png';
        if(file_exists($rutaLogo)) $pdf->Image($rutaLogo, 10, 10, 30);
        
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode('PAGARÉ'), 0, 1, 'C');
        $pdf->Ln(15); 

        // Cabecera
        $pdf->SetFont('Arial', '', 12);
        // Bueno por: (Usamos el total de la deuda)
        $pdf->Cell(100, 10, utf8_decode("Bueno por: $" . $total_deuda_fmt), 0, 0, 'L');
        $pdf->Cell(0, 10, utf8_decode("En Xalapa, Ver. a " . $fechaActual), 0, 1, 'R');
        $pdf->Ln(15);

        // CUERPO DEL PAGARÉ (CON LA REDACCIÓN SOLICITADA)
        $pdf->SetFont('Arial', '', 12);

        $textoCuerpo = "Debe(mos) y pagaré(mos) en forma incondicional este Pagaré a la orden del Sindicato de Trabajadores del Instituto Tecnológico Superior de Xalapa (SETDITSX) en Xalapa, Veracruz.\n\n";
        
        $textoCuerpo .= "La cantidad de: $" . $total_deuda_fmt . " MXN.\n";
        
        // DESGLOSE SOLICITADO
        $textoCuerpo .= "(Correspondiente a un préstamo de $" . $monto_base_fmt . " MXN como capital, más $" . $interes_generado_fmt . " MXN por concepto de intereses, equivalentes al 30% del monto prestado).\n\n";
        
        $textoCuerpo .= "Valor recibido en mi (nuestra) satisfacción, pagadero en $plazo quincenas consecutivas, liquidando antes del 30 de Noviembre.";
        
        $pdf->MultiCell(0, 8, utf8_decode($textoCuerpo));
        $pdf->Ln(20);

        // Datos Deudor
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 10, utf8_decode("DATOS DEL DEUDOR"), 0, 1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 8, utf8_decode("Nombre: " . $nombreDeudorCompleto), 0, 1);
        $pdf->Cell(0, 8, utf8_decode("Teléfono: " . $telefono), 0, 1);
        $pdf->Ln(10);

        // FIRMAS AJUSTADAS
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(90, 5, "________________________________", 0, 0, 'C');
        $pdf->Cell(90, 5, "________________________________", 0, 1, 'C');
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(90, 5, utf8_decode("FIRMA DEUDOR (SOCIO)"), 0, 0, 'C');
        $pdf->Cell(90, 5, utf8_decode("FIRMA ACEPTADO"), 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 9);
        // Firma del Ahorrador: Solo el nombre
        $pdf->Cell(90, 5, utf8_decode($nombreDeudorFirma), 0, 0, 'C');
        // Firma del Administrador: Solo la palabra "ADMINISTRADOR"
        $pdf->Cell(90, 5, utf8_decode("ADMINISTRADOR"), 0, 1, 'C');

        // Guardar PDF
        $rutaDir = "../uploads/pagares/";
        if (!file_exists($rutaDir)) mkdir($rutaDir, 0777, true);
        
        $nombreArchivo = "pagare_" . $id_solicitud . "_" . time() . ".pdf";
        $pdf->Output('F', $rutaDir . $nombreArchivo);

        // ACTUALIZAR BD
        $sqlUp = "UPDATE solicitud_prestamo SET id_estado = 2, archivo_pagare = :ruta WHERE id_solicitud_prestamo = :id";
        $stmtUp = $pdo->prepare($sqlUp);
        $stmtUp->execute([':ruta' => "uploads/pagares/" . $nombreArchivo, ':id' => $id_solicitud]);

        header("Location: gestion_prestamos.php?msg=Solicitud Aprobada y Pagaré generado exitosamente.");
        exit();

    } catch (Exception $e) { 
        die("Error al aprobar: " . $e->getMessage()); 
    }
}
?>