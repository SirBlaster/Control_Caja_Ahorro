<?php
// includes/Usuario/generar_formato.php
ob_start();
require('../fpdf/fpdf.php');
require('../conexion.php');
session_start();

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

// Verificar sesión y permisos
if (!isset($_SESSION['id_usuario'])) {
    die("Acceso no autorizado. Inicie sesión.");
}

if (!isset($_GET['id'])) {
    die("Falta el ID de la solicitud.");
}

$id_solicitud = $_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

// 1. OBTENER DATOS - CORREGIDO SEGÚN TU BD
$sql = "SELECT 
            sa.monto_solicitado,
            sa.nomina,
            sa.fecha,
            sa.archivo_nomina,
            u.nombre,
            u.apellido_paterno,
            u.apellido_materno,
            u.rfc,
            ds.nombre_director,
            ds.nombre_enc_personal
        FROM solicitud_ahorro sa
        JOIN usuario u ON sa.id_usuario = u.id_usuario
        JOIN datos_sistema ds ON ds.id_datos = 1 
        WHERE sa.id_solicitud_ahorro = ?
        AND sa.id_usuario = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_solicitud, $id_usuario]);
$data = $stmt->fetch();

if (!$data) {
    die("No se encontraron datos para esta solicitud.");
}

// ==========================================
// FUNCIONES PARA CALCULAR PERIODO Y QUINCENA
// ==========================================

/**
 * Determina la próxima quincena basada en la fecha actual
 * Retorna: "primera quincena de [mes] de [año]" o "segunda quincena de [mes] de [año]"
 */
function obtenerProximaQuincena($fecha_solicitud) {
    $fecha = new DateTime($fecha_solicitud);
    $dia = (int)$fecha->format('j'); // Día del mes (1-31)
    $mes = (int)$fecha->format('n'); // Mes (1-12)
    $anio = (int)$fecha->format('Y'); // Año
    
    $meses = array(
        1 => "enero", 2 => "febrero", 3 => "marzo", 4 => "abril",
        5 => "mayo", 6 => "junio", 7 => "julio", 8 => "agosto",
        9 => "septiembre", 10 => "octubre", 11 => "noviembre", 12 => "diciembre"
    );
    
    // Determinar en qué quincena estamos y cuál es la próxima
    if ($dia <= 15) {
        // Estamos en primera quincena, próxima es segunda quincena del mismo mes
        return "segunda quincena de " . $meses[$mes] . " de " . $anio;
    } else {
        // Estamos en segunda quincena, próxima es primera quincena del próximo mes
        if ($mes == 12) {
            // Diciembre → Enero del siguiente año
            return "primera quincena de enero de " . ($anio + 1);
        } else {
            // Otro mes → siguiente mes mismo año
            return "primera quincena de " . $meses[$mes + 1] . " de " . $anio;
        }
    }
}

/**
 * Determina el período de caja de ahorro basado en el mes de la solicitud
 * Reglas: Si es noviembre (mes 11), es para el siguiente período
 */
function obtenerPeriodo($fecha_solicitud) {
    $fecha = new DateTime($fecha_solicitud);
    $mes = (int)$fecha->format('n');
    $anio = (int)$fecha->format('Y');
    
    // Si la solicitud es en noviembre (mes 11), es para el siguiente año
    if ($mes == 11) {
        $anio_inicio = $anio + 1;
        $anio_fin = $anio_inicio + 1;
    } else {
        // Para otros meses, es para el año actual/siguiente
        $anio_inicio = $anio;
        $anio_fin = $anio + 1;
    }
    
    return $anio_inicio . " - " . $anio_fin;
}

/**
 * Determina el texto de la fecha de inicio del descuento
 * Nueva lógica: Noviembre → Diciembre siguiente año, otros meses → próxima quincena
 */
function obtenerFechaInicioDescuento($fecha_solicitud) {
    $fecha = new DateTime($fecha_solicitud);
    $mes = (int)$fecha->format('n');
    
    if ($mes == 11) {
        // Noviembre: empieza en diciembre del siguiente año
        $anio = (int)$fecha->format('Y');
        return "diciembre de " . ($anio + 1);
    } else {
        // Otros meses: empieza en la próxima quincena
        return obtenerProximaQuincena($fecha_solicitud);
    }
}

/**
 * Determina si la solicitud es para periodo próximo o actual
 */
function esSolicitudProximoPeriodo($fecha_solicitud) {
    $fecha = new DateTime($fecha_solicitud);
    $mes = (int)$fecha->format('n');
    return ($mes == 11); // Noviembre = próximo período
}

/**
 * Obtiene información detallada para debug
 */
function obtenerInfoQuincena($fecha_solicitud) {
    $fecha = new DateTime($fecha_solicitud);
    $dia = (int)$fecha->format('j');
    $mes = (int)$fecha->format('n');
    $anio = (int)$fecha->format('Y');
    
    $info = "Fecha solicitud: " . $fecha->format('Y-m-d') . "\n";
    $info .= "Día del mes: " . $dia . "\n";
    $info .= "Mes: " . $mes . "\n";
    
    if ($dia <= 15) {
        $info .= "Situación: En primera quincena\n";
        $info .= "Próxima quincena: Segunda quincena del mismo mes\n";
    } else {
        $info .= "Situación: En segunda quincena\n";
        $info .= "Próxima quincena: Primera quincena del próximo mes\n";
    }
    
    return $info;
}

// Variables - CORREGIDAS
$director = mb_strtoupper($data['nombre_director']);
$encargada = $data['nombre_enc_personal'];
$nombre_ahorrador = mb_strtoupper($data['nombre'] . " " . $data['apellido_paterno'] . " " . $data['apellido_materno']);
$monto_numerico = number_format($data['monto_solicitado'], 2);
$rfc = $data['rfc'];

// Obtener período dinámico basado en fecha de solicitud
$fecha_solicitud = $data['fecha'];
$periodo = obtenerPeriodo($fecha_solicitud);
$fecha_inicio_descuento = obtenerFechaInicioDescuento($fecha_solicitud);
$es_proximo_periodo = esSolicitudProximoPeriodo($fecha_solicitud);

// Información adicional para debug
$info_quincena = obtenerInfoQuincena($fecha_solicitud);

// Formato de fecha para el encabezado (fecha actual de generación)
$meses = array(
    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
    "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
);
$fecha_actual = new DateTime();
$fecha_texto = "Xalapa, Ver. a " . $fecha_actual->format('d') . " de " . $meses[$fecha_actual->format('n')-1] . " de " . $fecha_actual->format('Y');

// Función número a letras - MEJORADA
function num2letras($num) {
    $num = floatval($num);
    
    if (class_exists('NumberFormatter')) {
        $fmt = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $letras = $fmt->format($num);
        $letras = str_replace('coma', 'pesos', $letras);
        $letras .= ' 00/100 M.N.';
        return mb_strtoupper($letras);
    }
    
    $partes = explode('.', number_format($num, 2, '.', ''));
    $entero = intval($partes[0]);
    $decimal = isset($partes[1]) ? intval($partes[1]) : 0;
    
    // Sistema básico de conversión
    $unidades = array("", "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE");
    $decenas = array("", "DIEZ", "VEINTE", "TREINTA", "CUARENTA", "CINCUENTA", "SESENTA", "SETENTA", "OCHENTA", "NOVENTA");
    $centenas = array("", "CIENTO", "DOSCIENTOS", "TRESCIENTOS", "CUATROCIENTOS", "QUINIENTOS", "SEISCIENTOS", "SETECIENTOS", "OCHOCIENTOS", "NOVECIENTOS");
    
    $especiales = array(
        11 => "ONCE", 12 => "DOCE", 13 => "TRECE", 14 => "CATORCE", 15 => "QUINCE",
        16 => "DIECISEIS", 17 => "DIECISIETE", 18 => "DIECIOCHO", 19 => "DIECINUEVE"
    );
    
    // Conversión básica para montos comunes
    if ($entero == 0) {
        $texto = "CERO";
    } elseif ($entero <= 9) {
        $texto = $unidades[$entero];
    } elseif ($entero <= 19) {
        $texto = isset($especiales[$entero]) ? $especiales[$entero] : $decenas[floor($entero/10)] . " Y " . $unidades[$entero%10];
    } elseif ($entero <= 99) {
        $texto = $decenas[floor($entero/10)];
        if ($entero%10 > 0) {
            $texto .= " Y " . $unidades[$entero%10];
        }
    } elseif ($entero == 100) {
        $texto = "CIEN";
    } elseif ($entero <= 199) {
        $texto = "CIENTO " . num2letras($entero - 100);
    } elseif ($entero <= 999) {
        $texto = $centenas[floor($entero/100)];
        if ($entero%100 > 0) {
            $texto .= " " . num2letras($entero%100);
        }
    } else {
        $texto = "CIEN";
    }
    
    $texto .= " PESOS " . sprintf("%02d", $decimal) . "/100 M.N.";
    return $texto;
}

$monto_letras = num2letras($data['monto_solicitado']);

// Función para convertir caracteres especiales
function convertirTexto($texto) {
    $reemplazos = array(
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'ñ' => 'n', 'Ñ' => 'N', '°' => 'o', '´' => '', '`' => '',
        'ü' => 'u', 'Ü' => 'U'
    );
    return strtr($texto, $reemplazos);
}

// --- CLASE PDF PERSONALIZADA ---
class PDF extends FPDF {
    function Header() {
        $logo_path = '../../img/NewLogo - 1.png';
        if (file_exists($logo_path)) {
            $this->Image($logo_path, 20, 8, 25);
        }
        
        $this->SetFont('Arial', 'B', 10);
        $this->SetY(10);
        $this->Cell(0, 4, 'SETDITSX', 0, 1, 'C');
        
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 4, convertirTexto('Sindicato de Empleados Trabajadores y Docentes del ITSX'), 0, 1, 'C');
        
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 4, 'FUSESTV', 0, 1, 'C');
        
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 4, convertirTexto('Federacion Unica de Sindicatos de Educacion Superior Tecnologica de Veracruz'), 0, 1, 'C');
        
        $this->Ln(10);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Crear PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);

// Título principal
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetX(-80);
$pdf->Cell(70, 6, convertirTexto('SOLICITUD DE DESCUENTO'), 0, 1, 'R');
$pdf->SetX(-80);
$pdf->Cell(70, 6, convertirTexto('Caja de Ahorro ' . $periodo), 0, 1, 'R');

$pdf->Ln(10);

// Director
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 5, convertirTexto($director), 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 5, convertirTexto('DIRECTOR GENERAL DEL'), 0, 1, 'L');
$pdf->Cell(0, 5, convertirTexto('INSTITUTO TECNOLOGICO SUPERIOR DE XALAPA'), 0, 1, 'L');

$pdf->Ln(5);

// Fecha
$pdf->Cell(0, 10, convertirTexto($fecha_texto), 0, 1, 'R');
$pdf->Ln(5);

// Primer párrafo
$texto1 = convertirTexto("Por medio de la presente le solicito que se realice la retención de descuento del importe neto de mi pago quincenal, con la finalidad de entregarlos a la Caja de Ahorro " . $periodo . " del SETDITSX:");
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 7, $texto1, 0, 'J');

$pdf->Ln(5);

// Monto
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(40, 10, "$ " . $monto_numerico, 1, 0, 'C'); 

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(5);
$pdf->MultiCell(0, 10, convertirTexto("pesos quincenales, con el concepto CAJA DE AHORRO SETDITSX"), 0, 'L');

$pdf->Ln(2);

// Monto en letras
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(40, 8, "", 0, 0, 'C');
$pdf->Cell(5);
$pdf->MultiCell(0, 8, convertirTexto("(" . $monto_letras . ")"), 0, 'L');

$pdf->Ln(5);

// Segundo párrafo - CON FECHA DINÁMICA DE QUINCENA
$texto2 = convertirTexto("Acepto que el descuento sea efectivo inmediato a partir de la " . $fecha_inicio_descuento . ".");
$pdf->MultiCell(0, 7, $texto2, 0, 'J');

$pdf->Ln(2);

// Tercer párrafo
$texto3 = convertirTexto("No omito mencionar que la presente solicitud se hace de manera voluntaria, sin error, dolo o mala fe y que el (ella) consiente lo que la presente contiene.");
$pdf->MultiCell(0, 7, $texto3, 0, 'J');

$pdf->Ln(25);

// ATENTAMENTE
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 5, convertirTexto('ATENTAMENTE'), 0, 1, 'C');

$pdf->Ln(20);

// Firma y nombre
$pdf->Cell(0, 5, "___________________________________", 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 5, convertirTexto($nombre_ahorrador), 0, 1, 'C'); 
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, convertirTexto('Nombre y firma del ahorrador (a) de la caja del S.E.T.D.I.T.S.X.'), 0, 1, 'C');

$pdf->Ln(15);

// Copias
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(0, 4, convertirTexto('c.c.p. Subdireccion Administrativa- Para su conocimiento.'), 0, 1, 'L');
$texto_encargada = convertirTexto($encargada . '. Encargada del Departamento de Personal del ITSX. Para su conocimiento.');
$pdf->Cell(0, 4, $texto_encargada, 0, 1, 'L');
$pdf->Cell(0, 4, convertirTexto('D5 Comite de Administracion de la Caja de Ahorro SETDITSX.'), 0, 1, 'L');

// ==========================================
// 2. GUARDADO FÍSICO
// ==========================================
$nombre_pdf = 'Solicitud_' . $id_solicitud . '_' . $rfc . '.pdf';

// Ruta para guardar
$ruta_raiz = dirname(dirname(__DIR__)); 
$carpeta_solicitudes = $ruta_raiz . '/uploads/solicitudes/';
$ruta_fisica = $carpeta_solicitudes . $nombre_pdf;

if (!file_exists($carpeta_solicitudes)) {
    mkdir($carpeta_solicitudes, 0777, true);
}

$pdf->Output('F', $ruta_fisica);

// Actualizar BD
$sqlUpdate = "UPDATE solicitud_ahorro SET archivo_solicitud = ? WHERE id_solicitud_ahorro = ?";
$stmtUp = $pdo->prepare($sqlUpdate);
$stmtUp->execute([$nombre_pdf, $id_solicitud]);

// ==========================================
// 3. RUTA WEB PARA DESCARGA
// ==========================================
$ruta_web = '../../uploads/solicitudes/' . $nombre_pdf;

// Mostrar información de depuración
if (isset($_GET['debug'])) {
    echo "<pre>";
    echo "=== INFORMACIÓN DE LA SOLICITUD ===\n";
    echo "Fecha solicitud: " . $fecha_solicitud . "\n";
    echo "Período: " . $periodo . "\n";
    echo "Fecha inicio descuento: " . $fecha_inicio_descuento . "\n";
    echo "Es próximo período: " . ($es_proximo_periodo ? "Sí" : "No") . "\n\n";
    echo "=== CÁLCULO DE QUINCENA ===\n";
    echo $info_quincena;
    echo "\nTexto en PDF: 'Acepto que el descuento sea efectivo inmediato a partir de la " . $fecha_inicio_descuento . "'";
    echo "</pre>";
    exit;
}

ob_end_clean(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato Generado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }
        .card-box { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            text-align: center; 
            max-width: 500px; 
            width: 100%;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .spinner { 
            width: 60px; 
            height: 60px; 
            border: 5px solid #e9ecef; 
            border-top: 5px solid #0d6efd; 
            border-radius: 50%; 
            animation: spin 1s linear infinite; 
            margin: 0 auto 25px auto; 
        }
        @keyframes spin { 
            0% { transform: rotate(0deg); } 
            100% { transform: rotate(360deg); } 
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .info-box {
            background: #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            text-align: left;
            font-size: 0.9rem;
        }
        .info-box h6 {
            color: #2a3472;
            font-weight: bold;
            border-bottom: 2px solid #2a3472;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="card-box">
        <div class="spinner"></div>
        <h3 class="text-success mb-3">¡Formato Generado Exitosamente!</h3>
        
        <!-- Información del período -->
        <div class="info-box">
            <h6>Detalles de la Solicitud:</h6>
            <p class="mb-1"><strong>Período de Caja:</strong> <?php echo $periodo; ?></p>
            <p class="mb-1"><strong>Inicio de descuento:</strong> <?php echo $fecha_inicio_descuento; ?></p>
            <p class="mb-1"><strong>Fecha de solicitud:</strong> <?php echo date('d/m/Y', strtotime($fecha_solicitud)); ?></p>
            <p class="mb-0"><strong>Tipo de solicitud:</strong> 
                <?php echo $es_proximo_periodo ? 'Para próximo período (Noviembre)' : 'Para período actual'; ?>
            </p>
        </div>
        
        <div class="d-grid gap-2">
            <a id="btnDescarga" href="<?php echo $ruta_web; ?>" download="<?php echo $nombre_pdf; ?>" target="_blank" class="btn btn-primary">
                <i class="bi bi-download"></i> DESCARGAR FORMATO PDF
            </a>
            
            <a href="../../Usuario/panelAhorrador.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> VOLVER AL PANEL
            </a>
        </div>
        
        <small class="text-muted d-block mt-4">
            La descarga comenzará automáticamente en <span id="segundos" class="fw-bold">10</span> segundos...
        </small>
    </div>

    <script>
        // Auto-descarga después de 1 segundo
        setTimeout(() => {
            const btn = document.getElementById('btnDescarga');
            if (btn) {
                btn.click();
            }
        }, 1000);

        // Contador para redirección
        let count = 10;
        const display = document.getElementById('segundos');
        const timer = setInterval(() => {
            count--;
            if (display) {
                display.textContent = count;
            }
            if (count <= 0) {
                clearInterval(timer);
                window.location.href = '../../Usuario/panelAhorrador.php';
            }
        }, 1000);
    </script>
</body>
</html>