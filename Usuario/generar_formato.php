<?php
// Usuario/generar_formato.php
ob_start(); // Limpieza de buffer
require('../includes/fpdf/fpdf.php');
require('../includes/conexion.php');
session_start();

// Ocultar warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

if (!isset($_GET['id'])) die("Falta ID");
$id_solicitud = $_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

// 1. OBTENER DATOS REALES DE LA BD (ACTUALIZADO)
// Agregamos 'd.NombreEnc_Personal' a la lista de campos
$sql = "SELECT s.Monto, u.Nombre, u.Paterno, u.Materno, u.RFC, d.NombreDirector, d.NombreEnc_Personal
        FROM Solicitud_Ahorro s
        JOIN Usuarios u ON s.Id_Ahorrador = u.Id_Ahorrador
        JOIN DatosSistema d ON d.Id_Datos = 1 
        WHERE s.Id_SolicitudAhorro = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_solicitud]);
$data = $stmt->fetch();

if (!$data) die("No se encontraron datos de la solicitud.");

// Preparar variables
$director = mb_strtoupper($data['NombreDirector']);
$encargada_personal = $data['NombreEnc_Personal']; // Variable nueva
$nombre_ahorrador = mb_strtoupper($data['Nombre'] . " " . $data['Paterno'] . " " . $data['Materno']);
$monto_numerico = number_format($data['Monto'], 2);

// FECHA ACTUAL EN ESPAÑOL
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_texto = "Xalapa, Ver. a " . date('d') . " de " . $meses[date('n')-1] . " de " . date('Y');

// --- FUNCIÓN NÚMERO A LETRAS ---
function num2letras($num, $fem = false, $dec = true) { 
   $matuni = array("","uno","dos","tres","cuatro","cinco","seis","siete","ocho","nueve","diez","once","doce","trece","catorce","quince","dieciseis","diecisiete","dieciocho","diecinueve","veinte"); 
   $matdec = array("","diez","veinte","treinta","cuarenta","cincuenta","sesenta","setenta","ochenta","noventa"); 
   $matsub = array("","mill","millon","billon","trillon"); 
   $num = trim((string)@$num); 
   if ($num[0] == '-') { $neg = 'menos '; $num = substr($num, 1); }else $neg = ''; 
   while ($num[0] == '0') $num = substr($num, 1); 
   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
   $zeros = true; 
   $punt = false; 
   $ent = ''; 
   $fra = ''; 
   for ($c = 0; $c < strlen($num); $c++) { 
      $n = $num[$c]; 
      if (! (strpos(".,'''", $n) === false)) { 
         if ($punt) break; 
         else{ $punt = true; continue; } 
      }elseif (! (strpos('0123456789', $n) === false)) { 
         if ($punt) { if ($n != '0') $zeros = false; $fra .= $n; } 
         else $ent .= $n; 
      }else break; 
   } 
   $ent = '     ' . $ent; 
   if ($dec and $fra and ! $zeros) { 
      $fin = ' con ' . $fra . '/100'; 
   }else $fin = ' con 00/100'; 

   if (class_exists('NumberFormatter')) {
       $fmt = new NumberFormatter("es", NumberFormatter::SPELLOUT);
       return strtoupper($fmt->format($ent)) . " PESOS " . $fin . " M.N.";
   }
   return "CANTIDAD EN LETRA " . $fin . " M.N.";
}

// Generar letras
if (class_exists('NumberFormatter')) {
    $nf = new NumberFormatter("es", NumberFormatter::SPELLOUT);
    $monto_letras = mb_strtoupper($nf->format($data['Monto'])) . " PESOS 00/100 M.N.";
} else {
    $monto_letras = num2letras($data['Monto']);
}

// Helper conversión caracteres
function c($texto) { return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto); }

// --- PDF ---
class PDF extends FPDF {
    function Header() {
        if(file_exists('../img/NewLogo - 1.png')) $this->Image('../img/NewLogo - 1.png', 10, 5, 25);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 4, 'SETDITSX', 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 4, c('Sindicato de Empleados Trabajadores y Docentes del ITSX'), 0, 1, 'C');
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 4, 'FUSESTV', 0, 1, 'C');
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 4, c('Federación Unica de Sindicatos de Educación Superior Tecnológica de Veracruz'), 0, 1, 'C');
        $this->Ln(10);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);

// TÍTULO
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 6, c('SOLICITUD DE DESCUENTO'), 0, 1, 'R');
$pdf->Cell(0, 6, c('Caja de Ahorro 2025 - 2026'), 0, 1, 'R');
$pdf->Ln(10);

// DIRIGIDO A
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 5, c($director), 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 5, c('DIRECTOR GENERAL DEL'), 0, 1, 'L');
$pdf->Cell(0, 5, c('INSTITUTO TECNOLÓGICO SUPERIOR DE XALAPA'), 0, 1, 'L');
$pdf->Ln(5);

// FECHA
$pdf->Cell(0, 10, c($fecha_texto), 0, 1, 'R');
$pdf->Ln(5);

// CUERPO
$pdf->MultiCell(0, 7, c("Por medio de la presente le solicito que se realice la retención de descuento del importe neto de mi pago quincenal, con la finalidad de entregarlos a la Caja de Ahorro 2025-2026 del SETDITSX:"), 0, 'J');
$pdf->Ln(5);

// MONTO
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(40, 10, "$ " . $monto_numerico, 1, 0, 'C'); 
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(5);
$pdf->MultiCell(0, 10, c("( " . $monto_letras . " )"), 0, 'L');
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 10, c("pesos quincenales, con el concepto CAJA DE AHORRO SETDITSX"), 0, 1, 'L');
$pdf->Ln(5);

// TEXTO LEGAL
$pdf->MultiCell(0, 7, c("Acepto que el descuento sea efectivo inmediato a partir de la primera quincena de diciembre de 2025."), 0, 'J');
$pdf->Ln(2);
$pdf->MultiCell(0, 7, c("No omito mencionar que la presente solicitud se hace de manera voluntaria, sin error, dolo o mala fe y que el (ella) consiente lo que la presente contiene."), 0, 'J');
$pdf->Ln(25);

// FIRMA AHORRADOR
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 5, c('ATENTAMENTE'), 0, 1, 'C');
$pdf->Ln(20);
$pdf->Cell(0, 5, "___________________________________", 0, 1, 'C');
$pdf->Cell(0, 5, c($nombre_ahorrador), 0, 1, 'C'); 
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, c('Nombre y firma del ahorrador (a) de la caja del S.E.T.D.I.T.S.X.'), 0, 1, 'C');

// CCP (PIE DE PÁGINA)
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(0, 4, c('c.c.p. Subdirección Administrativa- Para su conocimiento.'), 0, 1, 'L');

// --- CAMBIO AQUÍ: Usamos la variable $encargada_personal ---
$texto_encargada = $encargada_personal . '. Encargada del Departamento de Personal del ITSX. Para su conocimiento.';
$pdf->Cell(0, 4, c($texto_encargada), 0, 1, 'L');

$pdf->Cell(0, 4, c('D5 Comité de Administración de la Caja de Ahorro SETDITSX.'), 0, 1, 'L');

// GUARDAR Y DESCARGAR
$nombre_pdf = 'Solicitud_' . $id_solicitud . '.pdf';
$ruta_guardado = '../uploads/solicitudes/' . $nombre_pdf;

// Verificar carpeta destino
if (!file_exists(dirname($ruta_guardado))) mkdir(dirname($ruta_guardado), 0777, true);

$pdf->Output('F', $ruta_guardado);

// Actualizar BD
$sqlUpdate = "UPDATE Solicitud_Ahorro SET ArchivoSolicitud = ? WHERE Id_SolicitudAhorro = ?";
$stmtUp = $pdo->prepare($sqlUpdate);
$stmtUp->execute([$nombre_pdf, $id_solicitud]);

ob_end_clean();
$pdf->Output('D', $nombre_pdf);
?>