<?php
$host = "localhost";     // o 127.0.0.1
$usuario = "root";       // usuario por defecto en XAMPP
$contrasena = "admin";   // cambia si usas otra contraseña
$base_datos = "semillero"; // <- cambia esto por el nombre real de tu base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Opcional: Establecer codificación a UTF-8
$conn->set_charset("utf8");

// Puedes imprimir esto para verificar
// echo "Conexión exitosa";
?>