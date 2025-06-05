<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrate</title>
    <link rel="icon" href="../icon/ean_logo.png" type="image/png">
    <link rel="stylesheet" href="../css/styles.css">
    

</head>
<body>
    <form action="" method="post">
    <div class = "form-container">
        <h2 style="text-align: center;">Registro Para El Semillero</h2>
        <input type="text" placeholder="Nombre" required>
        <input type="text" placeholder="Cedula" required>
        <input type="text" placeholder="Correo" required>
        <input type="text" placeholder="Telefono" required>
        <input type="text" placeholder="Contraseña" required>
          <select class = 'seleccionador' name="role" id="role" required>
            <option value="estudiante">Estudiante</option>
            <option value="tutor">Tutor</option>
        </select>
        <button type="submit">Crear Usuario</button>
        <a class="crear-cuenta"  href="../php/login.php" style="text-align: center;">Ya tengo una cuenta</a>
    </div>
    </form>
    
</body>
</html>