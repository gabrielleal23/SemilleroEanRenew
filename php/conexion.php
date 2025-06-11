<?php
$host = "localhost";
$usuario = "root";
$contrasena = "";  // En XAMPP por defecto es vacía
$base_datos = "semillero";

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer codificación a UTF-8
$conn->set_charset("utf8");

// Función para limpiar datos de entrada
function limpiar_datos($dato) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($dato));
}

// Función para verificar si un correo ya existe
function correo_existe($correo) {
    global $conn;
    $correo = limpiar_datos($correo);
    $query = "SELECT id FROM usuarios WHERE correo = '$correo'";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

// Función para verificar si una cédula ya existe
function cedula_existe($cedula) {
    global $conn;
    $cedula = limpiar_datos($cedula);
    $query = "SELECT id FROM usuarios WHERE cedula = '$cedula'";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}
?>