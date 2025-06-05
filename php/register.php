<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrate</title>
    <link rel="icon" href="../icon/ean_logo.png" type="image/png">
    <link rel="stylesheet" href="../css/styles.css">
    
    <style>
    .seleccionador {
      width: 400px;
      height: 35px;
      border-radius: 10px;
      border: 1px solid #ccc;
      padding: 5px 10px;
      font-size: 16px;
      background-color: white;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: url("data:image/svg+xml;utf8,<svg fill='%23666' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 20px;
      padding-right: 35px;
    }
    </style>
    

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
          <select class = "seleccionador" name="role" id="role" required>
            <option value="estudiante">Estudiante</option>
            <option value="tutor">Tutor</option>
        </select>
        <button type="submit">Crear Usuario</button>
        <a class="crear-cuenta"  href="../php/login.php" style="text-align: center;">Ya tengo una cuenta</a>
    </div>
    </form>
    
</body>
</html>