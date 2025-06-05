
<?php
session_start();
$user = $_SESSION['user_id'] = 'PRUEBA';
$role = $_SESSION['role'] = 'tutor';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tutor') {
    header("Location: login.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $actividad = $_POST['actividad'];
    file_put_contents("actividades.txt", $actividad . PHP_EOL, FILE_APPEND);
    echo "<p>Actividad creada correctamente.</p>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Actividad</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Crear Nueva Actividad</h2>
        <form method="post">
            <textarea name="actividad" required></textarea>
            <button type="submit">Crear</button>
        </form>
    </div>
</body>
</html>
