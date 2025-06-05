<?php
include("db.php");

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$tutor_id = $_POST['tutor_id'];

$query = "INSERT INTO semilleros (nombre, descripcion, tutor_id) VALUES ('$nombre', '$descripcion', $tutor_id)";
mysqli_query($conn, $query);

header("Location: ../views/admin_dashboard.php");
?>