<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'estudiante') {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['semillero_id'])) {
    $semillero_id = (int) $_POST['semillero_id'];

    // Validar que exista el semillero
    $query_valid = "SELECT nombre FROM semilleros WHERE id = $semillero_id AND estado = 'activo'";
    $result_valid = mysqli_query($conn, $query_valid);

    if (mysqli_num_rows($result_valid) === 0) {
        $_SESSION['mensaje'] = "El semillero no está disponible.";
        $_SESSION['tipo_mensaje'] = "error";
    } else {
        $nombre_semillero = mysqli_fetch_assoc($result_valid)['nombre'];

        // Verificar si ya está postulado
        $check = mysqli_query($conn, "SELECT id FROM postulaciones WHERE usuario_id = $usuario_id AND semillero_id = $semillero_id");
        if (mysqli_num_rows($check) > 0) {
            $_SESSION['mensaje'] = "Ya estás postulado a este semillero.";
            $_SESSION['tipo_mensaje'] = "error";
        } else {
            $insert = "INSERT INTO postulaciones (usuario_id, semillero_id, estado) VALUES ($usuario_id, $semillero_id, 'pendiente')";
            if (mysqli_query($conn, $insert)) {
                $_SESSION['mensaje'] = "Te postulaste exitosamente al semillero: $nombre_semillero";
                $_SESSION['tipo_mensaje'] = "exito";
            } else {
                $_SESSION['mensaje'] = "Error al postularte.";
                $_SESSION['tipo_mensaje'] = "error";
            }
        }
    }
}

header("Location: views/estudiante_dashboard.php");
exit();