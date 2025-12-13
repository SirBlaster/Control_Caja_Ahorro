<?php
// includes/Usuario/procesar_ahorro.php
session_start();
// 1. Ruta Conexión: Estamos en includes/Usuario, salimos una y ahí está conexion.php
require '../conexion.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id_usuario = $_SESSION['id_usuario'];



    // 2. RECEPCIÓN DE DATOS
    $sueldo = $_POST['sueldo'];
    $monto = $_POST['monto'];

    if ($monto > ($sueldo * 0.30)) {
        die("<script>alert('Error: El monto supera el 30% permitido.'); window.history.back();</script>");
    }

    // 3. SUBIDA DE ARCHIVO (Ruta Corregida)
    $nombre_archivo_nomina = "nomina_" . $id_usuario . "_" . time() . ".pdf";
    
    // USAMOS dirname(dirname(__DIR__)) para ir a la Raíz del proyecto de forma segura
    // __DIR__ = includes/Usuario
    // dirname(__DIR__) = includes
    // dirname(dirname(__DIR__)) = Raíz del proyecto
    $ruta_raiz = dirname(dirname(__DIR__)); 
    $ruta_destino = $ruta_raiz . "/uploads/nominas/" . $nombre_archivo_nomina;
    $carpeta_nominas = $ruta_raiz . "/uploads/nominas/";

    // Verificar carpeta
    if (!file_exists($carpeta_nominas)) {
        if (!mkdir($carpeta_nominas, 0777, true)) {
            die("Error Crítico: No se pudo crear la carpeta uploads/nominas.");
        }
    }

    if (isset($_FILES['archivo_nomina']) && $_FILES['archivo_nomina']['error'] === UPLOAD_ERR_OK) {
        
        if (move_uploaded_file($_FILES['archivo_nomina']['tmp_name'], $ruta_destino)) {
            
            try {
                $sql = "INSERT INTO Solicitud_Ahorro (Fecha, Monto, Nomina, ArchivoNomina, ArchivoSolicitud, Id_Ahorrador, Id_Estado) 
                        VALUES (NOW(), ?, ?, ?, 'GENERANDO...', ?, 1)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$monto, $sueldo, $nombre_archivo_nomina, $id_usuario]);

                $id_solicitud = $pdo->lastInsertId();
                
                // 4. Redirección al generador (Están en la misma carpeta, así que es directo)
                header("Location: generar_formato.php?id=" . $id_solicitud);
                exit();

            } catch (PDOException $e) {
                die("Error BD: " . $e->getMessage());
            }

        } else {
            die("Error: Falló move_uploaded_file.");
        }
    } else {
        die("Error: Archivo no válido.");
    }
}
?>