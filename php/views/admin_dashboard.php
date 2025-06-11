<?php
session_start();
include("../conexion.php");

// Verificar que el usuario esté logueado y sea tutor o admin
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] != 'tutor' && $_SESSION['rol'] != 'admin')) {
    header("Location: ../login.php");
    exit();
}

$tutor_id = $_SESSION['usuario_id'];
$mensaje = "";
$tipo_mensaje = "";

// Mostrar mensajes de sesión
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'];
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}

// Mostrar mensajes por parámetro GET
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'semillero_creado':
            $mensaje = "Semillero creado exitosamente";
            $tipo_mensaje = "exito";
            break;
        case 'actividad_creada':
            $mensaje = "Actividad creada exitosamente";
            $tipo_mensaje = "exito";
            break;
        case 'postulacion_actualizada':
            $mensaje = "Estado de postulación actualizado";
            $tipo_mensaje = "exito";
            break;
    }
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear_semillero':
                $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
                $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);
                $objetivo_principal = mysqli_real_escape_string($conn, $_POST['objetivo_principal']);
                $objetivos_especificos = mysqli_real_escape_string($conn, $_POST['objetivos_especificos']);
                
                $query = "INSERT INTO semilleros (nombre, descripcion, objetivo_principal, objetivos_especificos, tutor_id) 
                         VALUES ('$nombre', '$descripcion', '$objetivo_principal', '$objetivos_especificos', $tutor_id)";
                
                if (mysqli_query($conn, $query)) {
                    $_SESSION['mensaje'] = "Semillero creado exitosamente";
                    $_SESSION['tipo_mensaje'] = "exito";
                } else {
                    $_SESSION['mensaje'] = "Error al crear el semillero";
                    $_SESSION['tipo_mensaje'] = "error";
                }
                header("Location: admin_dashboard.php");
                exit();
                break;
                
            case 'crear_actividad':
                $semillero_id = intval($_POST['semillero_id']);
                $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
                $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);
                $fecha_entrega = mysqli_real_escape_string($conn, $_POST['fecha_entrega']);
                $entregable = mysqli_real_escape_string($conn, $_POST['entregable']);
                
                $query = "INSERT INTO actividades (semillero_id, titulo, descripcion, fecha_entrega, entregable) 
                         VALUES ($semillero_id, '$titulo', '$descripcion', '$fecha_entrega', '$entregable')";
                
                if (mysqli_query($conn, $query)) {
                    $_SESSION['mensaje'] = "Actividad creada exitosamente";
                    $_SESSION['tipo_mensaje'] = "exito";
                } else {
                    $_SESSION['mensaje'] = "Error al crear la actividad";
                    $_SESSION['tipo_mensaje'] = "error";
                }
                header("Location: admin_dashboard.php");
                exit();
                break;
                
            case 'actualizar_postulacion':
                $postulacion_id = intval($_POST['postulacion_id']);
                $nuevo_estado = mysqli_real_escape_string($conn, $_POST['nuevo_estado']);
                
                $query = "UPDATE postulaciones SET estado = '$nuevo_estado' WHERE id = $postulacion_id";
                
                if (mysqli_query($conn, $query)) {
                    $_SESSION['mensaje'] = "Estado de postulación actualizado";
                    $_SESSION['tipo_mensaje'] = "exito";
                } else {
                    $_SESSION['mensaje'] = "Error al actualizar la postulación";
                    $_SESSION['tipo_mensaje'] = "error";
                }
                header("Location: admin_dashboard.php");
                exit();
                break;
        }
    }
}

// Obtener semilleros del tutor
$query_semilleros = "SELECT * FROM semilleros WHERE tutor_id = $tutor_id ORDER BY nombre";
$result_semilleros = mysqli_query($conn, $query_semilleros);

// Obtener postulaciones de los semilleros del tutor
$query_postulaciones = "SELECT p.*, u.nombre as estudiante_nombre, u.correo as estudiante_correo, 
                        u.telefono as estudiante_telefono, s.nombre as semillero_nombre
                        FROM postulaciones p
                        JOIN usuarios u ON p.usuario_id = u.id
                        JOIN semilleros s ON p.semillero_id = s.id
                        WHERE s.tutor_id = $tutor_id
                        ORDER BY p.fecha_postulacion DESC";
$result_postulaciones = mysqli_query($conn, $query_postulaciones);

// Obtener actividades de los semilleros del tutor
$query_actividades = "SELECT a.*, s.nombre as semillero_nombre
                      FROM actividades a
                      JOIN semilleros s ON a.semillero_id = s.id
                      WHERE s.tutor_id = $tutor_id
                      ORDER BY a.fecha_creacion DESC";
$result_actividades = mysqli_query($conn, $query_actividades);

// Contar estadísticas
$total_semilleros = mysqli_num_rows($result_semilleros);
$total_postulaciones = mysqli_num_rows($result_postulaciones);
$total_actividades = mysqli_num_rows($result_actividades);

mysqli_data_seek($result_postulaciones, 0);
$postulaciones_pendientes = 0;
$postulaciones_aceptadas = 0;
while ($p = mysqli_fetch_assoc($result_postulaciones)) {
    if ($p['estado'] == 'pendiente') $postulaciones_pendientes++;
    if ($p['estado'] == 'aceptada') $postulaciones_aceptadas++;
}
mysqli_data_seek($result_postulaciones, 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel <?php echo ucfirst($_SESSION['rol']); ?> - EAN</title>
    <link rel="icon" href="../../icon/ean_logo.png" type="image/png">
    <link rel="stylesheet" href="../../css/style2.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #89E0B7 0%, #3BAC53 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .dashboard-nav {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
        }
        
        .nav-link {
            color: #005baa;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }
        
        .nav-link:hover {
            background-color: #f8f9fa;
        }
        
        .nav-link.active {
            background-color: #005baa;
            color: white;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #005baa;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #005baa;
        }
        
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
        
        .empty-state {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .hidden {
            display: none;
        }
        
        .form-grid {
            display: grid;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .btn-form {
            background-color: #005baa;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }
        
        .btn-form:hover {
            background-color: #003d73;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            margin: 2px;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .estado-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
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
        
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <img src="../../icon/ean_logo.png" alt="EAN Logo" style="width: 60px; margin-bottom: 15px;">
        <h1>Panel del <?php echo ucfirst($_SESSION['rol']); ?></h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
    </div>
    
    <div class="dashboard-nav">
        <div class="nav-container">
            <div class="nav-links">
                <a href="#" class="nav-link active" onclick="showSection('resumen')">Resumen</a>
                <a href="#" class="nav-link" onclick="showSection('semilleros')">Mis Semilleros</a>
                <a href="#" class="nav-link" onclick="showSection('crear-semillero')">Crear Semillero</a>
                <a href="#" class="nav-link" onclick="showSection('postulaciones')">Postulaciones</a>
                <a href="#" class="nav-link" onclick="showSection('actividades')">Actividades</a>
                <a href="#" class="nav-link" onclick="showSection('crear-actividad')">Crear Actividad</a>
            </div>
            <div>
                <a href="../index.php" class="nav-link">Inicio</a>
                <a href="../logout.php" class="nav-link" style="color: #dc3545;">Cerrar Sesión</a>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <!-- Sección Resumen -->
        <div id="resumen-section" class="section">
            <h2 class="section-title">Resumen de Actividad</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_semilleros; ?></div>
                    <div class="stat-label">Mis Semilleros</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_postulaciones; ?></div>
                    <div class="stat-label">Total Postulaciones</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $postulaciones_pendientes; ?></div>
                    <div class="stat-label">Postulaciones Pendientes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_actividades; ?></div>
                    <div class="stat-label">Actividades Creadas</div>
                </div>
            </div>
            
            <div class="card">
                <h3>Acciones Rápidas</h3>
                <p>Desde aquí puedes:</p>
                <ul style="text-align: left; margin: 15px 0;">
                    <li>Crear nuevos semilleros de investigación</li>
                    <li>Gestionar postulaciones de estudiantes</li>
                    <li>Crear actividades para tus semilleros</li>
                    <li>Monitorear el progreso de tus estudiantes</li>
                </ul>
            </div>
        </div>
        
        <!-- Sección Mis Semilleros -->
        <div id="semilleros-section" class="section hidden">
            <h2 class="section-title">Mis Semilleros</h2>
            
            <?php if (mysqli_num_rows($result_semilleros) > 0): ?>
                <?php while ($semillero = mysqli_fetch_assoc($result_semilleros)): ?>
                    <div class="card">
                        <div class="card-title"><?php echo htmlspecialchars($semillero['nombre']); ?></div>
                        
                        <?php if ($semillero['descripcion']): ?>
                            <div class="card-info">
                                <strong>Descripción:</strong><br>
                                <?php echo nl2br(htmlspecialchars($semillero['descripcion'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($semillero['objetivo_principal']): ?>
                            <div class="card-info">
                                <strong>Objetivo Principal:</strong><br>
                                <?php echo nl2br(htmlspecialchars($semillero['objetivo_principal'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-info">
                            <strong>Estado:</strong> 
                            <span class="estado-badge estado-<?php echo $semillero['estado']; ?>">
                                <?php echo ucfirst($semillero['estado']); ?>
                            </span>
                        </div>
                        
                        <div class="card-info">
                            <strong>Fecha de creación:</strong> 
                            <?php echo date('d/m/Y', strtotime($semillero['fecha_creacion'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No has creado semilleros</h3>
                    <p>Aún no tienes semilleros creados.</p>
                    <button onclick="showSection('crear-semillero')" class="btn-form" style="margin-top: 15px;">
                        Crear Mi Primer Semillero
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sección Crear Semillero -->
        <div id="crear-semillero-section" class="section hidden">
            <h2 class="section-title">Crear Nuevo Semillero</h2>
            
            <div class="card">
                <form method="post">
                    <input type="hidden" name="action" value="crear_semillero">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre del Semillero *</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" placeholder="Describe brevemente el semillero"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="objetivo_principal">Objetivo Principal</label>
                            <textarea id="objetivo_principal" name="objetivo_principal" placeholder="¿Cuál es el objetivo principal de este semillero?"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="objetivos_especificos">Objetivos Específicos</label>
                            <textarea id="objetivos_especificos" name="objetivos_especificos" placeholder="Lista los objetivos específicos del semillero"></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-form">Crear Semillero</button>
                </form>
            </div>
        </div>
        
        <!-- Sección Postulaciones -->
        <div id="postulaciones-section" class="section hidden">
            <h2 class="section-title">Postulaciones Recibidas</h2>
            
            <?php if (mysqli_num_rows($result_postulaciones) > 0): ?>
                <?php while ($postulacion = mysqli_fetch_assoc($result_postulaciones)): ?>
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                            <div class="card-title"><?php echo htmlspecialchars($postulacion['estudiante_nombre']); ?></div>
                            <div class="estado-badge estado-<?php echo $postulacion['estado']; ?>">
                                <?php echo ucfirst($postulacion['estado']); ?>
                            </div>
                        </div>
                        
                        <div class="card-info">
                            <strong>Semillero:</strong> <?php echo htmlspecialchars($postulacion['semillero_nombre']); ?>
                        </div>
                        
                        <div class="card-info">
                            <strong>Correo:</strong> <?php echo htmlspecialchars($postulacion['estudiante_correo']); ?>
                        </div>
                        
                        <?php if ($postulacion['estudiante_telefono']): ?>
                            <div class="card-info">
                                <strong>Teléfono:</strong> <?php echo htmlspecialchars($postulacion['estudiante_telefono']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-info">
                            <strong>Fecha de postulación:</strong> 
                            <?php echo date('d/m/Y H:i', strtotime($postulacion['fecha_postulacion'])); ?>
                        </div>
                        
                        <?php if ($postulacion['estado'] == 'pendiente'): ?>
                            <div style="margin-top: 15px;">
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="actualizar_postulacion">
                                    <input type="hidden" name="postulacion_id" value="<?php echo $postulacion['id']; ?>">
                                    <input type="hidden" name="nuevo_estado" value="aceptada">
                                    <button type="submit" class="btn-small btn-success">Aceptar</button>
                                </form>
                                
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="actualizar_postulacion">
                                    <input type="hidden" name="postulacion_id" value="<?php echo $postulacion['id']; ?>">
                                    <input type="hidden" name="nuevo_estado" value="rechazada">
                                    <button type="submit" class="btn-small btn-danger">Rechazar</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No hay postulaciones</h3>
                    <p>Aún no has recibido postulaciones para tus semilleros.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sección Actividades -->
        <div id="actividades-section" class="section hidden">
            <h2 class="section-title">Actividades Creadas</h2>
            
            <?php if (mysqli_num_rows($result_actividades) > 0): ?>
                <?php while ($actividad = mysqli_fetch_assoc($result_actividades)): ?>
                    <div class="card">
                        <div class="card-title"><?php echo htmlspecialchars($actividad['titulo']); ?></div>
                        
                        <div class="card-info">
                            <strong>Semillero:</strong> <?php echo htmlspecialchars($actividad['semillero_nombre']); ?>
                        </div>
                        
                        <?php if ($actividad['descripcion']): ?>
                            <div class="card-info">
                                <strong>Descripción:</strong><br>
                                <?php echo nl2br(htmlspecialchars($actividad['descripcion'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($actividad['fecha_entrega']): ?>
                            <div class="card-info">
                                <strong>Fecha de entrega:</strong> 
                                <?php echo date('d/m/Y', strtotime($actividad['fecha_entrega'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($actividad['entregable']): ?>
                            <div class="card-info">
                                <strong>Entregable:</strong><br>
                                <?php echo nl2br(htmlspecialchars($actividad['entregable'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-info">
                            <strong>Fecha de creación:</strong> 
                            <?php echo date('d/m/Y H:i', strtotime($actividad['fecha_creacion'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No has creado actividades</h3>
                    <p>Aún no tienes actividades creadas para tus semilleros.</p>
                    <button onclick="showSection('crear-actividad')" class="btn-form" style="margin-top: 15px;">
                        Crear Mi Primera Actividad
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sección Crear Actividad -->
        <div id="crear-actividad-section" class="section hidden">
            <h2 class="section-title">Crear Nueva Actividad</h2>
            
            <div class="card">
                <form method="post">
                    <input type="hidden" name="action" value="crear_actividad">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="semillero_id">Semillero *</label>
                            <select id="semillero_id" name="semillero_id" required>
                                <option value="">Selecciona un semillero</option>
                                <?php 
                                mysqli_data_seek($result_semilleros, 0);
                                while ($semillero = mysqli_fetch_assoc($result_semilleros)): 
                                ?>
                                    <option value="<?php echo $semillero['id']; ?>">
                                        <?php echo htmlspecialchars($semillero['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="titulo">Título de la Actividad *</label>
                            <input type="text" id="titulo" name="titulo" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion_actividad">Descripción</label>
                            <textarea id="descripcion_actividad" name="descripcion" placeholder="Describe la actividad que deben realizar los estudiantes"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_entrega">Fecha de Entrega</label>
                            <input type="date" id="fecha_entrega" name="fecha_entrega">
                        </div>
                        
                        <div class="form-group">
                            <label for="entregable">Entregable</label>
                            <textarea id="entregable" name="entregable" placeholder="Describe qué deben entregar los estudiantes"></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-form">Crear Actividad</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function showSection(sectionName) {
            // Ocultar todas las secciones
            document.querySelectorAll('.section').forEach(section => {
                section.classList.add('hidden');
            });
            
            // Remover clase active de todos los links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Mostrar la sección seleccionada
            document.getElementById(sectionName + '-section').classList.remove('hidden');
            
            // Agregar clase active al link correspondiente
            event.target.classList.add('active');
        }
    </script>
</body>
</html>