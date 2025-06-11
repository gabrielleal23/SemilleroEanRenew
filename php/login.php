<?php
session_start();
include("conexion.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = limpiar_datos($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    
    if (empty($correo) || empty($contrasena)) {
        $mensaje = "Por favor ingresa correo y contraseña";
    } else {
        // Buscar usuario por correo
        $query = "SELECT id, nombre, correo, contrasena, rol FROM usuarios WHERE correo = '$correo'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $usuario = mysqli_fetch_assoc($result);
            
            // Verificar contraseña
            if (password_verify($contrasena, $usuario['contrasena'])) {
                // Iniciar sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['rol'] = $usuario['rol'];
                
                // Redirigir según el rol
                switch ($usuario['rol']) {
                    case 'admin':
                        header("Location: index.php");
                        break;
                    case 'tutor':
                        header("Location: index.php");
                        break;
                    case 'estudiante':
                        header("Location: index.php");
                        break;
                    default:
                        header("Location: index.php");
                }
                exit();
            } else {
                $mensaje = "Contraseña incorrecta";
            }
        } else {
            $mensaje = "Usuario no encontrado";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Semillero EAN</title>
    <link rel="icon" href="../icon/ean_logo.png" type="image/png">
    <link rel="stylesheet" href="../css/style2.css">
    <style>
        .mensaje {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
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
                <h2 style="text-align: center;">Iniciar Sesión</h2>
            </center>
            
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <input type="email" name="correo" placeholder="Correo electrónico" value="<?php echo isset($correo) ? $correo : ''; ?>" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
            <a class="crear-cuenta" href="register.php" style="text-align: center;">¿No tienes cuenta? Crear Cuenta</a>
        </div>
    </form>
</body>
</html>