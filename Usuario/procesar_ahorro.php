<?php
// Usuario/procesar_ahorro.php
session_start();
require '../includes/conexion.php';

// Habilitar reporte de errores para ver qué falla (útil en desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Obtenemos ID del usuario logueado
    $id_usuario = $_SESSION['id_usuario'];

    // ========================================================
    // 1. VALIDACIÓN DE SEGURIDAD (ANTI-DUPLICADOS)
    // ========================================================
    // Buscamos la última solicitud de este usuario
    $sqlCheck = "SELECT Id_Estado FROM Solicitud_Ahorro WHERE Id_Ahorrador = ? ORDER BY Id_SolicitudAhorro DESC LIMIT 1";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([$id_usuario]);
    $estado_actual = $stmtCheck->fetchColumn();

    // Si existe y es Pendiente (1), DETENER.
    if ($estado_actual == 1) {
        die("<script>alert('Error: Ya tienes una solicitud PENDIENTE en revisión. No puedes enviar otra hasta que te respondan.'); window.location.href='registrahorro.php';</script>");
    }
    // Si existe y es Aprobado (2), DETENER.
    if ($estado_actual == 2) {
        die("<script>alert('Error: Ya tienes una solicitud APROBADA y activa. No puedes enviar otra.'); window.location.href='registrahorro.php';</script>");
    }
    // Si es 3 (Rechazado) o no existe ninguna solicitud previa, dejamos continuar el código.


    // ========================================================
    // 2. RECEPCIÓN DE DATOS
    // ========================================================
    $sueldo = $_POST['sueldo'];
    $monto = $_POST['monto'];

    // Validar regla del 30%
    if ($monto > ($sueldo * 0.30)) {
        die("<script>alert('Error: El monto supera el 30% permitido.'); window.history.back();</script>");
    }


    // ========================================================
    // 3. SUBIDA DE ARCHIVO (NÓMINA)
    // ========================================================
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

    // Intentar subir el archivo
    if (isset($_FILES['archivo_nomina']) && $_FILES['archivo_nomina']['error'] === UPLOAD_ERR_OK) {
        
        if (move_uploaded_file($_FILES['archivo_nomina']['tmp_name'], $ruta_destino)) {
            
            // ========================================================
            // 4. GUARDAR EN BASE DE DATOS
            // ========================================================
            try {
                // Usamos placeholder temporal 'GENERANDO...' para el archivo de solicitud
                // esto cumple con el NOT NULL de la base de datos momentáneamente.
                // El archivo PDF real se generará y actualizará en el siguiente paso.
                $sql = "INSERT INTO Solicitud_Ahorro (Fecha, Monto, Nomina, ArchivoNomina, ArchivoSolicitud, Id_Ahorrador, Id_Estado) 
                        VALUES (NOW(), ?, ?, ?, 'GENERANDO...', ?, 1)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$monto, $sueldo, $nombre_archivo_nomina, $id_usuario]);

                // Obtenemos el ID de la solicitud recién creada
                $id_solicitud = $pdo->lastInsertId();
                
                // ¡ÉXITO! Redirigimos al generador de PDF
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