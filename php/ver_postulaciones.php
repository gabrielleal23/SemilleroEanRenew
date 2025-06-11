<?php
session_start();
include("conexion.php");

// Verificar que el usuario esté logueado y sea tutor o admin
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] != 'tutor' && $_SESSION['rol'] != 'admin')) {
    header("Location: login.php");
    exit();
}

$mensaje = "";
$tipo_mensaje = "";

// Procesar cambio de estado de postulación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['postulacion_id']) && isset($_POST['nuevo_estado'])) {
    $postulacion_id = (int)$_POST['postulacion_id'];
    $nuevo_estado = limpiar_datos($_POST['nuevo_estado']);
    
    if (in_array($nuevo_estado, ['pendiente', 'aceptada', 'rechazada'])) {
        $query_update = "UPDATE postulaciones SET estado = '$nuevo_estado' WHERE id = $postulacion_id";
        
        if (mysqli_query($conn, $query_update)) {
            $mensaje = "Estado de postulación actualizado exitosamente";
            $tipo_mensaje = "exito";
        } else {
            $mensaje = "Error al actualizar el estado: " . mysqli_error($conn);
            $tipo_mensaje = "error";
        }
    }
}

// Obtener postulaciones
if ($_SESSION['rol'] == 'admin') {
    // Admin puede ver todas las postulaciones
    $query = "SELECT p.*, u.nombre as estudiante_nombre, u.correo as estudiante_correo, 
                     u.telefono as estudiante_telefono, s.nombre as semillero_nombre,
                     ut.nombre as tutor_nombre
              FROM postulaciones p
              JOIN usuarios u ON p.usuario_id = u.id
              JOIN semilleros s ON p.semillero_id = s.id
              LEFT JOIN usuarios ut ON s.tutor_id = ut.id
              ORDER BY p.fecha_postulacion DESC";
} else {
    // Tutor solo ve postulaciones de sus semilleros
    $tutor_id = $_SESSION['usuario_id'];
    $query = "SELECT p.*, u.nombre as estudiante_nombre, u.correo as estudiante_correo, 
                     u.telefono as estudiante_telefono, s.nombre as semillero_nombre
              FROM postulaciones p
              JOIN usuarios u ON p.usuario_id = u.id
              JOIN semilleros s ON p.semillero_id = s.id
              WHERE s.tutor_id = $tutor_id
              ORDER BY p.fecha_postulacion DESC";
}

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postulaciones - EAN</title>
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
        .postulacion-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .postulacion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .estudiante-nombre {
            font-size: 18px;
            font-weight: bold;
            color: #005baa;
        }
        .estado-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .estado-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }
        .estado-aceptada {
            background-color: #d4edda;
            color: #155724;
        }
        .estado-rechazada {
            background-color: #f8d7da;
            color: #721c24;
        }
        .postulacion-info {
            margin: 8px 0;
            color: #666;
        }
        .acciones {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-aceptar {
            background-color: #28a745;
            color: white;
        }
        .btn-rechazar {
            background-color: #dc3545;
            color: white;
        }
        .btn-pendiente {
            background-color: #ffc107;
            color: #212529;
        }
        .no-postulaciones {
            text-align: center;
            color: #666;
            font-style: italic;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="form-container" style="max-width: 800px;">
        <center>
            <img src="../icon/ean_logo.png" alt="ean" style="width: 50px;padding-bottom: 0px;">
            <h2 style="text-align: center;">Postulaciones de Estudiantes</h2>
        </center>
        
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($postulacion = mysqli_fetch_assoc($result)): ?>
                <div class="postulacion-card">
                    <div class="postulacion-header">
                        <div class="estudiante-nombre"><?php echo htmlspecialchars($postulacion['estudiante_nombre']); ?></div>
                        <div class="estado-badge estado-<?php echo $postulacion['estado']; ?>">
                            <?php echo ucfirst($postulacion['estado']); ?>
                        </div>
                    </div>
                    
                    <div class="postulacion-info">
                        <strong>Semillero:</strong> <?php echo htmlspecialchars($postulacion['semillero_nombre']); ?>
                    </div>
                    
                    <?php if ($_SESSION['rol'] == 'admin' && isset($postulacion['tutor_nombre'])): ?>
                        <div class="postulacion-info">
                            <strong>Tutor:</strong> <?php echo htmlspecialchars($postulacion['tutor_nombre']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="postulacion-info">
                        <strong>Correo:</strong> <?php echo htmlspecialchars($postulacion['estudiante_correo']); ?>
                    </div>
                    
                    <?php if ($postulacion['estudiante_telefono']): ?>
                        <div class="postulacion-info">
                            <strong>Teléfono:</strong> <?php echo htmlspecialchars($postulacion['estudiante_telefono']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="postulacion-info">
                        <strong>Fecha de postulación:</strong> <?php echo date('d/m/Y H:i', strtotime($postulacion['fecha_postulacion'])); ?>
                    </div>
                    
                    <div class="acciones">
                        <?php if ($postulacion['estado'] != 'aceptada'): ?>
                            <form action="" method="post" style="display: inline;">
                                <input type="hidden" name="postulacion_id" value="<?php echo $postulacion['id']; ?>">
                                <input type="hidden" name="nuevo_estado" value="aceptada">
                                <button type="submit" class="btn btn-aceptar">Aceptar</button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if ($postulacion['estado'] != 'rechazada'): ?>
                            <form action="" method="post" style="display: inline;">
                                <input type="hidden" name="postulacion_id" value="<?php echo $postulacion['id']; ?>">
                                <input type="hidden" name="nuevo_estado" value="rechazada">
                                <button type="submit" class="btn btn-rechazar">Rechazar</button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if ($postulacion['estado'] != 'pendiente'): ?>
                            <form action="" method="post" style="display: inline;">
                                <input type="hidden" name="postulacion_id" value="<?php echo $postulacion['id']; ?>">
                                <input type="hidden" name="nuevo_estado" value="pendiente">
                                <button type="submit" class="btn btn-pendiente">Marcar como Pendiente</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-postulaciones">
                No hay postulaciones registradas.
            </div>
        <?php endif; ?>
        
        <a class="crear-cuenta" href="<?php echo ($_SESSION['rol'] == 'admin') ? '../views/admin_dashboard.php' : '../views/tutor_dashboard.php'; ?>" style="text-align: center;">Volver al panel</a>
    </div>
</body>
</html>