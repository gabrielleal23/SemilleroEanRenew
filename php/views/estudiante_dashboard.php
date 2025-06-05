<?php
session_start();
include("../php/db.php");
if ($_SESSION['rol'] != 'estudiante') {
    header("Location: ../php/logout.php");
}
$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT * FROM semilleros";
$semilleros = mysqli_query($conn, $query);
?>
<html>
<body>
<h2>Bienvenido Estudiante</h2>
<ul>
<?php while($s = mysqli_fetch_assoc($semilleros)) { ?>
    <li>
        <?php echo $s['nombre']; ?>
        <form method="post" action="../php/postular_semillero.php">
            <input type="hidden" name="semillero_id" value="<?php echo $s['id']; ?>">
            <button type="submit">Postularme</button>
        </form>
    </li>
<?php } ?>
</ul>
<a href="../php/logout.php">Cerrar Sesión</a>
</body>
</html>