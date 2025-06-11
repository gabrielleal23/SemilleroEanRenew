<?php
session_start();
include("conexion.php");

// Verificar que el usuario esté logueado y sea tutor o admin
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] != 'tutor' && $_SESSION['rol'] != 'admin')) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = limpiar_datos($_POST['nombre']);
    $descripcion = limpiar_datos($_POST['descripcion']);
    $objetivo_principal = limpiar_datos($_POST['objetivo_principal']);
    $objetivos_especificos = limpiar_datos($_POST['objetivos_especificos']);
    
    // Si es admin, puede asignar cualquier tutor, si es tutor, se asigna a sí mismo
    if ($_SESSION['rol'] == 'admin' && isset($_POST['tutor_id'])) {
        $tutor_id = (int)$_POST['tutor_id'];
    } else {
        $tutor_id = $_SESSION['usuario_id'];
    }
    
    // Validaciones
    if (empty($nombre) || empty($descripcion)) {
        $mensaje = "El nombre y la descripción son obligatorios";
        $tipo_mensaje = "error";
    } else {
        // Verificar que el nombre del semillero no esté duplicado
        $query_check = "SELECT id FROM semilleros WHERE nombre = '$nombre'";
        $result_check = mysqli_query($conn, $query_check);
        
        if (mysqli_num_rows($result_check) > 0) {
            $mensaje = "Ya existe un semillero con ese nombre";
            $tipo_mensaje = "error";
        } else {
            // Insertar semillero
            $query = "INSERT INTO semilleros (nombre, descripcion, objetivo_principal, objetivos_especificos, tutor_id) 
                      VALUES ('$nombre', '$descripcion', '$objetivo_principal', '$objetivos_especificos', $tutor_id)";
            
            if (mysqli_query($conn, $query)) {
                $mensaje = "Semillero creado exitosamente";
                $tipo_mensaje = "exito";
                
                // Redirigir según el rol
                if ($_SESSION['rol'] == 'admin') {
                    header("Location: ../views/admin_dashboard.php?msg=semillero_creado");
                } else {
                    header("Location: ../views/tutor_dashboard.php?msg=semillero_creado");
                }
                exit();
            } else {
                $mensaje = "Error al crear semillero: " . mysqli_error($conn);
                $tipo_mensaje = "error";
            }
        }
    }
}

// Obtener lista de tutores (solo para admin)
$tutores = [];
if ($_SESSION['rol'] == 'admin') {
    $query_tutores = "SELECT id, nombre FROM usuarios WHERE rol = 'tutor' ORDER BY nombre";
    $result_tutores = mysqli_query($conn, $query_tutores);
    while ($tutor = mysqli_fetch_assoc($result_tutores)) {
        $tutores[] = $tutor;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Semillero - EAN</title>
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
        textarea {
            padding: 12px 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
        }
        textarea:focus {
            border-color: #005baa;
            box-shadow: 0 0 5px rgba(0, 91, 170, 0.3);
            outline: none;
        }
    </style>
</head>
<body>
    <form action="" method="post">
        <div class="form-container">
            <center>
                <img src="../icon/ean_logo.png" alt="ean" style="width: 50px;padding-bottom: 0px;">
                <h2 style="text-align: center;">Crear Nuevo Semillero</h2>
            </center>
            
            <?php if (isset($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <input type="text" name="nombre" placeholder="Nombre del semillero" value="<?php echo isset($nombre) ? $nombre : ''; ?>" required>
            
            <textarea name="descripcion" placeholder="Descripción del semillero" required><?php echo isset($descripcion) ? $descripcion : ''; ?></textarea>
            
            <textarea name="objetivo_principal" placeholder="Objetivo principal (opcional)"><?php echo isset($objetivo_principal) ? $objetivo_principal : ''; ?></textarea>
            
            <textarea name="objetivos_especificos" placeholder="Objetivos específicos (opcional)"><?php echo isset($objetivos_especificos) ? $objetivos_especificos : ''; ?></textarea>
            
            <?php if ($_SESSION['rol'] == 'admin' && !empty($tutores)): ?>
                <select class="seleccionador" name="tutor_id" required>
                    <option value="">Selecciona un tutor</option>
                    <?php foreach ($tutores as $tutor): ?>
                        <option value="<?php echo $tutor['id']; ?>"><?php echo $tutor['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            
            <button type="submit">Crear Semillero</button>
            
            <a class="crear-cuenta" href="<?php echo ($_SESSION['rol'] == 'admin') ? '../views/admin_dashboard.php' : '../views/admin_dashboard.php'; ?>" style="text-align: center;">Volver al panel</a>
        </div>
    </form>
</body>
</html>