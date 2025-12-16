<?php
// C:\laragon\www\ControlCajadeAhorro\includes\conexion.php

$host = '127.0.0.1'; // IP de Docker
$port = '3306';
$db   = 'sistema_caja';
$user = 'root';
$pass = 'password123'; // La contraseña de tu docker-compose.yml
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<h1>Error de Conexión</h1><p>No se pudo conectar a la base de datos en Docker.</p>" . $e->getMessage());
}
?>
