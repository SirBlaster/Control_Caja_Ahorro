<?php
// procesar_registro.php
require 'includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recibir datos
    $nombre = $_POST['nombre'];
    $paterno = $_POST['paterno'];
    $materno = $_POST['materno'];
    $curp = $_POST['curp'];
    $rfc = $_POST['rfc'];
    
    // ¡AQUÍ ESTÁ EL TELÉFONO DE REGRESO!
    $telefono = $_POST['telefono']; 
    
    $correo_personal = $_POST['correo_personal'];
    $institucional = $_POST['correo_institucional'];
    
    // Tarjeta (Opcional, puede venir vacía si no seleccionaron nada, validar en JS)
    $tarjeta = isset($_POST['dato_bancario']) ? $_POST['dato_bancario'] : null;
    
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // 2. Validaciones
    if ($password !== $confirm) {
        die("<script>alert('Las contraseñas no coinciden'); window.history.back();</script>");
    }

    if ($tarjeta && strlen($tarjeta) != 16) {
        die("<script>alert('La tarjeta debe tener 16 dígitos'); window.history.back();</script>");
    }

    // 3. Verificar duplicados
    $checkSql = "SELECT Id_Ahorrador FROM Usuarios WHERE Institucional = ?";
    $stmtCheck = $pdo->prepare($checkSql);
    $stmtCheck->execute([$institucional]);

    if ($stmtCheck->rowCount() > 0) {
        die("<script>alert('El correo institucional ya está registrado.'); window.history.back();</script>");
    }

    // 4. INSERTAR (Incluyendo Telefono)
    // Orden: Nombre, Paterno, Materno, Institucional, Personal, RFC, CURP, Telefono, Contrasena, Tarjeta, Id_Rol
    $sql = "INSERT INTO Usuarios (Nombre, Paterno, Materno, Institucional, Personal, RFC, CURP, Telefono, Contrasena, Tarjeta, Id_Rol) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 2)";
    
    $stmt = $pdo->prepare($sql);
    
    $resultado = $stmt->execute([
        $nombre, 
        $paterno, 
        $materno, 
        $institucional, 
        $correo_personal, 
        $rfc, 
        $curp, 
        $telefono, // <--- Aquí se inserta
        $password, 
        $tarjeta
    ]);

    if ($resultado) {
        echo "<script>alert('¡Registro Exitoso! Inicia sesión.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Error al guardar en la base de datos.'); window.history.back();</script>";
    }
}
?>