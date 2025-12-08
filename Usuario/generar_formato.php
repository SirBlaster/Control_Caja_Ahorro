<?php
// Usuario/generar_formato.php
ob_start(); // Iniciar buffer para evitar errores
require('../includes/fpdf/fpdf.php');
require('../includes/conexion.php');
session_start();

// Ocultar errores para que no salgan en el PDF
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

if (!isset($_GET['id'])) die("Falta ID");
$id_solicitud = $_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

// 1. OBTENER DATOS
$sql = "SELECT s.Monto, u.Nombre, u.Paterno, u.Materno, u.RFC, d.NombreDirector, d.NombreEnc_Personal
        FROM Solicitud_Ahorro s
        JOIN Usuarios u ON s.Id_Ahorrador = u.Id_Ahorrador
        JOIN DatosSistema d ON d.Id_Datos = 1 
        WHERE s.Id_SolicitudAhorro = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_solicitud]);
$data = $stmt->fetch();

if (!$data) die("No se encontraron datos.");

// Variables
$director = mb_strtoupper($data['NombreDirector']);
$encargada = $data['NombreEnc_Personal'];
$nombre_ahorrador = mb_strtoupper($data['Nombre'] . " " . $data['Paterno'] . " " . $data['Materno']);
$monto_numerico = number_format($data['Monto'], 2);
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_texto = "Xalapa, Ver. a " . date('d') . " de " . $meses[date('n')-1] . " de " . date('Y');

// Función número a letras
function num2letras($num) {
    if (class_exists('NumberFormatter')) {
       $fmt = new NumberFormatter("es", NumberFormatter::SPELLOUT);
       return strtoupper($fmt->format($num)) . "";
    }
    return "CANTIDAD EN LETRAS M.N.";
}
$monto_letras = num2letras($data['Monto']);
function c($t) { return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $t); }

// --- GENERACIÓN DEL PDF ---
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
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 6, c('SOLICITUD DE DESCUENTO'), 0, 1, 'R');
$pdf->Cell(0, 6, c('Caja de Ahorro 2025 - 2026'), 0, 1, 'R');
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 5, c($director), 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 5, c('DIRECTOR GENERAL DEL'), 0, 1, 'L');
$pdf->Cell(0, 5, c('INSTITUTO TECNOLÓGICO SUPERIOR DE XALAPA'), 0, 1, 'L');
$pdf->Ln(5);
$pdf->Cell(0, 10, c($fecha_texto), 0, 1, 'R');
$pdf->Ln(5);
$pdf->MultiCell(0, 7, c("Por medio de la presente le solicito que se realice la retención de descuento del importe neto de mi pago quincenal, con la finalidad de entregarlos a la Caja de Ahorro 2025-2026 del SETDITSX:"), 0, 'J');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(40, 10, "$ " . $monto_numerico, 1, 0, 'C'); 
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(5);
$pdf->MultiCell(0, 10, c( $monto_letras . " pesos quincenales, con el concepto CAJA DE AHORRO SETDITSX"), 0, 'L');
$pdf->Ln(2);
$pdf->MultiCell(0, 7, c("Acepto que el descuento sea efectivo inmediato a partir de la primera quincena de diciembre de 2025."), 0, 'J');
$pdf->Ln(2);
$pdf->MultiCell(0, 7, c("No omito mencionar que la presente solicitud se hace de manera voluntaria, sin error, dolo o mala fe y que el (ella) consiente lo que la presente contiene."), 0, 'J');
$pdf->Ln(25);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 5, c('ATENTAMENTE'), 0, 1, 'C');
$pdf->Ln(20);
$pdf->Cell(0, 5, "___________________________________", 0, 1, 'C');
$pdf->Cell(0, 5, c($nombre_ahorrador), 0, 1, 'C'); 
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, c('Nombre y firma del ahorrador (a) de la caja del S.E.T.D.I.T.S.X.'), 0, 1, 'C');
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(0, 4, c('c.c.p. Subdirección Administrativa- Para su conocimiento.'), 0, 1, 'L');
$texto_encargada = $encargada . '. Encargada del Departamento de Personal del ITSX. Para su conocimiento.';
$pdf->Cell(0, 4, c($texto_encargada), 0, 1, 'L');
$pdf->Cell(0, 4, c('D5 Comité de Administración de la Caja de Ahorro SETDITSX.'), 0, 1, 'L');


// ==========================================
// AQUÍ ESTÁ LA LÓGICA DE GUARDADO Y REDIRECCIÓN
// ==========================================

// 1. Preparar ruta
$nombre_pdf = 'Solicitud_' . $id_solicitud . '_' . $data['RFC'] . '.pdf';
$ruta_guardado = '../uploads/solicitudes/' . $nombre_pdf;

if (!file_exists('../uploads/solicitudes/')) {
    mkdir('../uploads/solicitudes/', 0777, true);
}

// 2. Guardar el archivo FÍSICAMENTE en el servidor
$pdf->Output('F', $ruta_guardado);

// 3. ACTUALIZAR BASE DE DATOS (Quitamos el "GENERANDO...")
$sqlUpdate = "UPDATE Solicitud_Ahorro SET ArchivoSolicitud = ? WHERE Id_SolicitudAhorro = ?";
$stmtUp = $pdo->prepare($sqlUpdate);
$stmtUp->execute([$nombre_pdf, $id_solicitud]);

// 4. GENERAR PÁGINA HTML DE ÉXITO (Puente)
ob_end_clean(); // Limpiamos cualquier basura anterior
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud Exitosa</title>
    <link rel="stylesheet" href="../css/bootstrap/bootstrap.min.css">
    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-success { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }
        .icon-box { color: #28a745; font-size: 60px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="card-success">
        <div class="icon-box">
            <i class="bi bi-check-circle-fill"></i> &#10004; </div>
        <h2 class="mb-3">¡Solicitud Enviada!</h2>
        <p class="text-muted">Hemos guardado tu información y tu formato se está descargando automáticamente.</p>
        
        <div class="alert alert-info mt-4">
            Redirigiendo al menú principal en <span id="contador">3</span> segundos...
        </div>

        <a id="linkDescarga" href="<?php echo $ruta_guardado; ?>" download="<?php echo $nombre_pdf; ?>" style="display:none;"></a>
    </div>

    <script>
        // Paso A: Descargar el archivo inmediatamente
        document.getElementById('linkDescarga').click();

        // Paso B: Cuenta regresiva y redirección
        let segundos = 3;
        const spanContador = document.getElementById('contador');
        
        const intervalo = setInterval(() => {
            segundos--;
            spanContador.textContent = segundos;
            
            if (segundos <= 0) {
                clearInterval(intervalo);
                // Paso C: ¡AQUÍ TE REGRESA AL MENÚ!
                window.location.href = 'panelAhorrador.php'; 
            }
        }, 1000);
    </script>

</body>
</html>