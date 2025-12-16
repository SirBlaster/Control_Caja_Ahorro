<?php
// Admin/aprobar_solicitud.php
require_once '../includes/init.php';
require_once '../includes/fpdf/fpdf.php';

// 1. SEGURIDAD: Verificar Admin (Rol 2)
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    die("Acceso denegado.");
}

// 2. VALIDAR PARAMETROS
if (isset($_GET['id']) && isset($_GET['tipo'])) {
    
    $id = $_GET['id'];
    $tipo = $_GET['tipo']; // 'prestamo' o 'ahorro'

    try {
        // ==========================================
        // CASO 1: APROBACIÓN DE PRÉSTAMO (TU PAGARÉ)
        // ==========================================
        if ($tipo == 'prestamo') {
            
            // A. OBTENER DATOS DE LA BD
            $sql = "SELECT s.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.telefono 
                    FROM solicitud_prestamo s 
                    JOIN usuario u ON s.id_usuario = u.id_usuario 
                    WHERE s.id_solicitud_prestamo = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$datos) die("Error: Préstamo no encontrado.");

            // B. PREPARAR VARIABLES (CÁLCULOS Y FORMATOS)
            // Esto es vital para que no salgan vacíos en el PDF
            
            $monto_base = floatval($datos['monto_solicitado']);      // Capital
            $total_deuda = floatval($datos['total_a_pagar']);        // Total
            $plazo = $datos['plazo_quincenas'];
            $interes_generado = $total_deuda - $monto_base;          // Interés
            
            // Datos personales
            $nombreDeudorCompleto = $datos['nombre'] . ' ' . $datos['apellido_paterno'] . ' ' . $datos['apellido_materno'];
            $nombreDeudorFirma = mb_strtoupper($nombreDeudorCompleto, 'UTF-8'); // Para la firma
            $telefono = !empty($datos['telefono']) ? $datos['telefono'] : 'No registrado';

            // Formatos de moneda y fecha para imprimir
            $monto_base_fmt = number_format($monto_base, 2);
            $total_deuda_fmt = number_format($total_deuda, 2);
            $interes_generado_fmt = number_format($interes_generado, 2);
            $fechaActual = date('d/m/Y');

            // C. GENERAR EL PAGARÉ (TU DISEÑO EXACTO)
            $pdf = new FPDF();
            $pdf->AddPage();
            
            // Logo
            $rutaLogo = '../img/NewLogo - 1.png'; // Asegúrate de que esta imagen exista
            if(file_exists($rutaLogo)) {
                $pdf->Image($rutaLogo, 10, 10, 30);
            }
            
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, utf8_decode('PAGARÉ'), 0, 1, 'C');
            $pdf->Ln(15); 

            // Cabecera "Bueno por..."
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(100, 10, utf8_decode("Bueno por: $" . $total_deuda_fmt), 0, 0, 'L');
            $pdf->Cell(0, 10, utf8_decode("En Xalapa, Ver. a " . $fechaActual), 0, 1, 'R');
            $pdf->Ln(15);

            // Cuerpo del Pagaré
            $textoCuerpo = "Debe(mos) y pagaré(mos) en forma incondicional este Pagaré a la orden del Sindicato de Trabajadores del Instituto Tecnológico Superior de Xalapa (SETDITSX) en Xalapa, Veracruz.\n\n";
            
            $textoCuerpo .= "La cantidad de: $" . $total_deuda_fmt . " MXN.\n";
            
            // Desglose
            $textoCuerpo .= "(Correspondiente a un préstamo de $" . $monto_base_fmt . " MXN como capital, más $" . $interes_generado_fmt . " MXN por concepto de intereses, equivalentes al 30% del monto prestado).\n\n";
            
            $textoCuerpo .= "Valor recibido en mi (nuestra) satisfacción, pagadero en $plazo quincenas consecutivas, liquidando antes del 30 de Noviembre.";
            
            $pdf->MultiCell(0, 8, utf8_decode($textoCuerpo));
            $pdf->Ln(20);

            // Datos Deudor
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 10, utf8_decode("DATOS DEL DEUDOR"), 0, 1);
            $pdf->SetFont('Arial', '', 11);
            // Usamos utf8_decode para evitar caracteres raros en nombres con acentos
            $pdf->Cell(0, 8, utf8_decode("Nombre: " . $nombreDeudorCompleto), 0, 1);
            $pdf->Cell(0, 8, utf8_decode("Teléfono: " . $telefono), 0, 1);
            $pdf->Ln(15); // Un poco más de espacio para firmar

            // Firmas
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(90, 5, "________________________________", 0, 0, 'C');
            $pdf->Cell(90, 5, "________________________________", 0, 1, 'C');
            
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(90, 5, utf8_decode("FIRMA DEUDOR (SOCIO)"), 0, 0, 'C');
            $pdf->Cell(90, 5, utf8_decode("FIRMA ACEPTADO"), 0, 1, 'C');
            
            $pdf->SetFont('Arial', '', 9);
            // Firma del Ahorrador
            $pdf->Cell(90, 5, utf8_decode($nombreDeudorFirma), 0, 0, 'C');
            // Firma del Administrador
            $pdf->Cell(90, 5, utf8_decode("ADMINISTRADOR"), 0, 1, 'C');

            // D. GUARDAR ARCHIVO
            $rutaDir = "../uploads/pagares/";
            if (!file_exists($rutaDir)) mkdir($rutaDir, 0777, true);
            
            $nombreArchivo = "pagare_" . $id . "_" . time() . ".pdf";
            $pdf->Output('F', $rutaDir . $nombreArchivo);

            // E. ACTUALIZAR BASE DE DATOS
            $sqlUp = "UPDATE solicitud_prestamo SET id_estado = 2, archivo_pagare = :ruta WHERE id_solicitud_prestamo = :id";
            $stmtUp = $pdo->prepare($sqlUp);
            $stmtUp->execute([':ruta' => "uploads/pagares/" . $nombreArchivo, ':id' => $id]);

            // Redirigir
            header("Location: gestion_prestamos.php?msg=Préstamo aprobado y Pagaré generado.");
            exit();

        } 
        // ==========================================
        // CASO 2: APROBACIÓN DE AHORRO
        // ==========================================
        elseif ($tipo == 'ahorro') {
            
            // A. OBTENER DATOS
            $sql = "SELECT a.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.rfc, u.telefono 
                    FROM solicitud_ahorro a 
                    JOIN usuario u ON a.id_usuario = u.id_usuario 
                    WHERE a.id_solicitud_ahorro = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$datos) die("Error: Solicitud de ahorro no encontrada.");

            $nombreCompleto = mb_strtoupper($datos['nombre'] . ' ' . $datos['apellido_paterno'] . ' ' . $datos['apellido_materno'], 'UTF-8');
            $monto = number_format($datos['monto_solicitado'], 2);
            $fecha = date('d/m/Y');
            $rfc = $datos['rfc'];

            // B. GENERAR CONTRATO AHORRO (PDF)
            $pdf = new FPDF();
            $pdf->AddPage();
            
            // Logo
            $rutaLogo = '../img/NewLogo - 1.png';
            if(file_exists($rutaLogo)) $pdf->Image($rutaLogo, 10, 10, 30);
            
            $pdf->Ln(20);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, utf8_decode('SOLICITUD DE AHORRO ACEPTADA'), 0, 1, 'C');
            $pdf->Ln(10);
            
            $pdf->SetFont('Arial', '', 12);
            $texto = "Por medio de la presente, se certifica que el socio $nombreCompleto con RFC $rfc " . 
                     "ha sido aceptado en el programa de Ahorro Voluntario del SETDITSX.\n\n" .
                     "Monto autorizado a descontar quincenalmente: $$monto MXN.\n\n" .
                     "Fecha de aprobación: $fecha.\n\n" .
                     "Atentamente,\nLa Administración.";
            
            $pdf->MultiCell(0, 8, utf8_decode($texto));

            // C. GUARDAR PDF
            $rutaDir = "../uploads/solicitudes/";
            if (!file_exists($rutaDir)) mkdir($rutaDir, 0777, true);
            
            $nombreArchivo = "ahorro_" . $id . "_" . time() . ".pdf";
            $pdf->Output('F', $rutaDir . $nombreArchivo);

            // D. ACTUALIZAR BD Y CREAR CUENTA AHORRO
            // 1. Marcar solicitud como aprobada
            $sqlUp = "UPDATE solicitud_ahorro SET id_estado = 2, archivo_solicitud = :ruta WHERE id_solicitud_ahorro = :id";
            $stmtUp = $pdo->prepare($sqlUp);
            $stmtUp->execute([':ruta' => $nombreArchivo, ':id' => $id]); 

            // 2. Inicializar cuenta de ahorro si no existe
            $sqlCheck = "SELECT id_ahorro FROM ahorro WHERE id_usuario = :uid";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->execute([':uid' => $datos['id_usuario']]);
            
            if (!$stmtCheck->fetch()) {
                $sqlIns = "INSERT INTO ahorro (id_usuario, monto_ahorrado, fecha_ultima_actualizacion) VALUES (:uid, 0.00, NOW())";
                $stmtIns = $pdo->prepare($sqlIns);
                $stmtIns->execute([':uid' => $datos['id_usuario']]);
            }

            header("Location: gestion_prestamos.php?msg=Solicitud de Ahorro Aprobada.");
            exit();
        }

    } catch (Exception $e) { 
        die("Error del sistema: " . $e->getMessage()); 
    }
} else {
    die("Parámetros incompletos.");
}
?>