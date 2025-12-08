<?php
// Usuario/procesar_ahorro.php
session_start();
require '../includes/conexion.php';

// Habilitar reporte de errores para ver qué falla
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id_usuario = $_SESSION['id_usuario'];
    $sueldo = $_POST['sueldo'];
    $monto = $_POST['monto'];

    // 1. Validar 30%
    if ($monto > ($sueldo * 0.30)) {
        die("<script>alert('Error: El monto supera el 30% permitido.'); window.history.back();</script>");
    }

    // 2. Lógica del Archivo (Nómina)
    $nombre_archivo_nomina = "nomina_" . $id_usuario . "_" . time() . ".pdf";
    
    // RUTA ABSOLUTA (Más segura para evitar errores de ../)
    // __DIR__ es la carpeta actual (Usuario), subimos un nivel (dirname) y entramos a uploads
    $ruta_base = dirname(__DIR__) . "/uploads/nominas/";
    $ruta_destino = $ruta_base . $nombre_archivo_nomina;

    // Verificar si existe la carpeta, si no, intentar crearla
    if (!file_exists($ruta_base)) {
        if (!mkdir($ruta_base, 0777, true)) {
            die("Error Crítico: No se pudo crear la carpeta 'uploads/nominas'. Créala manualmente.");
        }
    }

    // 3. Subir el archivo
    if (isset($_FILES['archivo_nomina']) && $_FILES['archivo_nomina']['error'] === UPLOAD_ERR_OK) {
        
        if (move_uploaded_file($_FILES['archivo_nomina']['tmp_name'], $ruta_destino)) {
            
            // 4. Guardar en BD
            try {
                // Usamos placeholder temporal para el archivo de solicitud
                $sql = "INSERT INTO Solicitud_Ahorro (Fecha, Monto, Nomina, ArchivoNomina, ArchivoSolicitud, Id_Ahorrador, Id_Estado) 
                        VALUES (NOW(), ?, ?, ?, 'GENERANDO...', ?, 1)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$monto, $sueldo, $nombre_archivo_nomina, $id_usuario]);

                // ID para el generador
                $id_solicitud = $pdo->lastInsertId();
                
                // ¡ÉXITO! Vamos al generador
                header("Location: generar_formato.php?id=" . $id_solicitud);
                exit();

            } catch (PDOException $e) {
                die("Error BD: " . $e->getMessage());
            }

        } else {
            die("Error: Falló move_uploaded_file. Verifica permisos de la carpeta uploads.");
        }
    } else {
        die("Error: No se recibió ningún archivo o hubo un error en la subida (Código: " . $_FILES['archivo_nomina']['error'] . ")");
    }
}
?>