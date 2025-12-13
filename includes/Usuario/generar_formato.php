<?php
// includes/Usuario/generar_formato.php
ob_start();
require('../fpdf/fpdf.php');
require('../conexion.php');
session_start();

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
       return strtoupper($fmt->format($num));
    }
    return "CANTIDAD EN LETRAS";
}
$monto_letras = num2letras($data['Monto']);
function c($t) { return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $t); }

// --- PDF ---
class PDF extends FPDF {
    function Header() {
        if(file_exists('../../img/NewLogo - 1.png')) {
            $this->Image('../../img/NewLogo - 1.png', 10, 5, 25);
        }
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
$pdf->MultiCell(0, 10, c("( " . $monto_letras . " PESOS 00/100 M.N. )"), 0, 'L');
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 10, c("pesos quincenales, con el concepto CAJA DE AHORRO SETDITSX"), 0, 1, 'L');
$pdf->Ln(5);
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
// 2. GUARDADO FÍSICO
// ==========================================
$nombre_pdf = 'Solicitud_' . $id_solicitud . '_' . $data['RFC'] . '.pdf';

// Ruta Disco Duro (Para PHP)
$ruta_raiz = dirname(dirname(__DIR__)); 
$ruta_fisica = $ruta_raiz . '/uploads/solicitudes/' . $nombre_pdf;
$carpeta_solicitudes = $ruta_raiz . '/uploads/solicitudes/';

if (!file_exists($carpeta_solicitudes)) mkdir($carpeta_solicitudes, 0777, true);

$pdf->Output('F', $ruta_fisica); // Guardar en disco

// Actualizar BD
$sqlUpdate = "UPDATE Solicitud_Ahorro SET ArchivoSolicitud = ? WHERE Id_SolicitudAhorro = ?";
$stmtUp = $pdo->prepare($sqlUpdate);
$stmtUp->execute([$nombre_pdf, $id_solicitud]);


// ==========================================
// 3. RUTA WEB PARA DESCARGA (CORREGIDA)
// ==========================================
// En lugar de ../../ usamos la ruta absoluta del proyecto.
// Esto asume que tu carpeta en Laragon se llama "ControlCajadeAhorro"
$ruta_web = '/ControlCajadeAhorro/uploads/solicitudes/' . $nombre_pdf;

ob_end_clean(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesando Solicitud</title>
    <link rel="stylesheet" href="../../css/bootstrap/bootstrap.min.css">
    <style>
        body { background: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; }
        .card-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; max-width: 500px; width: 100%; }
        .spinner { width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #d18819; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <div class="card-box">
        <div class="spinner"></div>
        <h3 class="text-success mb-3">¡Solicitud Exitosa!</h3>
        <p class="text-muted mb-4">El archivo se ha generado en el servidor.</p>
        
        <a id="btnDescarga" href="<?php echo $ruta_web; ?>" download="<?php echo $nombre_pdf; ?>" target="_blank" class="btn btn-primary w-100 mb-3">
            <i class="bi bi-download"></i> DESCARGAR AHORA
        </a>
        
        <small class="text-muted d-block mt-3">Redirigiendo al menú en <span id="segundos">5</span> segundos...</small>
    </div>

    <script>
        // 1. INTENTO DE CLIC AUTOMÁTICO
        setTimeout(() => {
            const btn = document.getElementById('btnDescarga');
            btn.click();
        }, 1000);

        // 2. REDIRECCIÓN AL MENÚ
        let count = 5;
        const display = document.getElementById('segundos');
        const timer = setInterval(() => {
            count--;
            display.textContent = count;
            if (count <= 0) {
                clearInterval(timer);
                window.location.href = '../../Usuario/panelAhorrador.php'; 
            }
        }, 1000);
    </script>
</body>
</html>