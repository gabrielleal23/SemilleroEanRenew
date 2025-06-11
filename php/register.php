<?php
session_start();
include("conexion.php");

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = limpiar_datos($_POST['nombre']);
    $cedula = limpiar_datos($_POST['cedula']);
    $correo = limpiar_datos($_POST['correo']);
    $telefono = limpiar_datos($_POST['telefono']);
    $contrasena = $_POST['contrasena'];
    $rol = limpiar_datos($_POST['rol']);
    
    // Validaciones
    if (empty($nombre) || empty($cedula) || empty($correo) || empty($contrasena) || empty($rol)) {
        $mensaje = "Todos los campos son obligatorios";
        $tipo_mensaje = "error";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El correo electrónico no es válido";
        $tipo_mensaje = "error";
    } elseif (correo_existe($correo)) {
        $mensaje = "Este correo ya está registrado";
        $tipo_mensaje = "error";
    } elseif (cedula_existe($cedula)) {
        $mensaje = "Esta cédula ya está registrada";
        $tipo_mensaje = "error";
    } elseif (strlen($contrasena) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres";
        $tipo_mensaje = "error";
    } else {
        // Encriptar contraseña
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
        
        // Insertar usuario
        $query = "INSERT INTO usuarios (nombre, cedula, correo, telefono, contrasena, rol) 
                  VALUES ('$nombre', '$cedula', '$correo', '$telefono', '$contrasena_hash', '$rol')";
        
        if (mysqli_query($conn, $query)) {
            $mensaje = "Usuario registrado exitosamente";
            $tipo_mensaje = "exito";
            // Limpiar campos después del registro exitoso
            $nombre = $cedula = $correo = $telefono = "";
        } else {
            $mensaje = "Error al registrar usuario: " . mysqli_error($conn);
            $tipo_mensaje = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Semillero EAN</title>
    <link rel="icon" href="../icon/ean_logo.png" type="image/png">
    <link rel="stylesheet" href="../css/style2.css">
    <style>
        .mensaje {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
        .mensaje.exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <form action="" method="post">
        <div class="form-container">
            <center>
                <img src="../icon/ean_logo.png" alt="ean" style="width: 50px;padding-bottom: 0px;">
                <h2 style="text-align: center;">Registro Para El Semillero</h2>
            </center>
            
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <input type="text" name="nombre" placeholder="Nombre completo" value="<?php echo isset($nombre) ? $nombre : ''; ?>" required>
            <input type="text" name="cedula" placeholder="Cédula" value="<?php echo isset($cedula) ? $cedula : ''; ?>" required>
            <input type="email" name="correo" placeholder="Correo electrónico" value="<?php echo isset($correo) ? $correo : ''; ?>" required>
            <input type="text" name="telefono" placeholder="Teléfono" value="<?php echo isset($telefono) ? $telefono : ''; ?>">
            <input type="password" name="contrasena" placeholder="Contraseña (mín. 6 caracteres)" required>
            
            <select class="seleccionador" name="rol" required>
                <option value="">Selecciona tu rol</option>
                <option value="estudiante" <?php echo (isset($rol) && $rol == 'estudiante') ? 'selected' : ''; ?>>Estudiante</option>
                <option value="tutor" <?php echo (isset($rol) && $rol == 'tutor') ? 'selected' : ''; ?>>Tutor</option>
            </select>
            
            <button type="submit">Crear Usuario</button>
            <a class="crear-cuenta" href="login.php" style="text-align: center;">Ya tengo una cuenta</a>
        </div>
    </form>
</body>
</html>