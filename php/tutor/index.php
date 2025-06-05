
<?php
session_start();
$user = $_SESSION['user_id'] = 'PRUEBA';
$role = $_SESSION['role'] = 'tutor';
$nombre = $_SESSION['name'] = 'Tutor universidad ean';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tutor') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Tutor</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Bienvenido Tutor</h2>
        <p>Nombre: <?php echo $_SESSION['name']; ?></p>
        <ul>
            <li><a href="ver_postulaciones.php">Ver Postulaciones</a></li>
            <li><a href="crear_actividad.php">Crear Actividad</a></li>
            <li><a href="../php/logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>
</body>
</html>
