<?php
session_start();
include("../php/db.php");
if ($_SESSION['rol'] != 'admin') {
    header("Location: ../php/logout.php");
}
$tutores = mysqli_query($conn, "SELECT id, nombre FROM usuarios WHERE rol='tutor'");
?>
<html>
<body>
<h2>Administrador</h2>
<form method="post" action="../php/crear_semillero.php">
    Nombre: <input type="text" name="nombre"><br>
    Descripción: <textarea name="descripcion"></textarea><br>
    Tutor: <select name="tutor_id">
        <?php while($t = mysqli_fetch_assoc($tutores)) { ?>
        <option value="<?php echo $t['id']; ?>"><?php echo $t['nombre']; ?></option>
        <?php } ?>
    </select>
    <button type="submit">Crear</button>
</form>
</body>
</html>