<?php
session_start();
include("db.php");

$usuario_id = $_SESSION['usuario_id'];
$semillero_id = $_POST['semillero_id'];

$query = "INSERT INTO postulaciones (usuario_id, semillero_id, estado) VALUES ($usuario_id, $semillero_id, 'pendiente')";
mysqli_query($conn, $query);

header("Location: ../views/estudiante_dashboard.php");
?>