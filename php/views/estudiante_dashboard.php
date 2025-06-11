<?php
session_start();
include("../conexion.php");

// Verificar que el usuario esté logueado y sea estudiante
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'estudiante') {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = "";
$tipo_mensaje = "";

// Mostrar mensajes de sesión
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'];
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}

// Obtener semilleros disponibles para postulación
$query_semilleros = "SELECT s.*, u.nombre as tutor_nombre 
                     FROM semilleros s 
                     LEFT JOIN usuarios u ON s.tutor_id = u.id 
                     WHERE s.estado = 'activo'
                     ORDER BY s.nombre";
$result_semilleros = mysqli_query($conn, $query_semilleros);

// Obtener postulaciones del estudiante
$query_postulaciones = "SELECT p.*, s.nombre as semillero_nombre, s.descripcion as semillero_descripcion,
                        u.nombre as tutor_nombre
                        FROM postulaciones p
                        JOIN semilleros s ON p.semillero_id = s.id
                        LEFT JOIN usuarios u ON s.tutor_id = u.id
                        WHERE p.usuario_id = $usuario_id
                        ORDER BY p.fecha_postulacion DESC";
$result_postulaciones = mysqli_query($conn, $query_postulaciones);

// Obtener actividades de los semilleros donde el estudiante fue aceptado
$query_actividades = "SELECT a.*, s.nombre as semillero_nombre, s.id as semillero_id,
                      u.nombre as tutor_nombre
                      FROM actividades a
                      JOIN semilleros s ON a.semillero_id = s.id
                      LEFT JOIN usuarios u ON s.tutor_id = u.id
                      JOIN postulaciones p ON p.semillero_id = s.id
                      WHERE p.usuario_id = $usuario_id AND p.estado = 'aceptada'
                      ORDER BY a.fecha_entrega ASC, a.fecha_creacion DESC";
$result_actividades = mysqli_query($conn, $query_actividades);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Estudiante - EAN</title>
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
        
        .actividad-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #005baa;
        }
        
        .actividad-header {
            display: flex;
            justify-content: between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .actividad-titulo {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            flex: 1;
            min-width: 200px;
        }
        
        .fecha-entrega {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            color: #666;
            white-space: nowrap;
        }
        
        .fecha-entrega.urgente {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .fecha-entrega.vencida {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .actividad-semillero {
            background: #e3f2fd;
            color: #1565c0;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .actividad-descripcion {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .actividad-entregable {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 3px solid #28a745;
        }
        
        .actividad-entregable h4 {
            margin: 0 0 10px 0;
            color: #28a745;
            font-size: 16px;
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
            
            .actividad-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .fecha-entrega {
                align-self: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <img src="../../icon/ean_logo.png" alt="EAN Logo" style="width: 60px; margin-bottom: 15px;">
        <h1>Panel del Estudiante</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
    </div>
    
    <div class="dashboard-nav">
        <div class="nav-container">
            <div class="nav-links">
                <a href="#" class="nav-link active" onclick="showSection('resumen')">Resumen</a>
                <a href="#" class="nav-link" onclick="showSection('actividades')">Mis Actividades</a>
                <a href="#" class="nav-link" onclick="showSection('semilleros')">Semilleros Disponibles</a>
                <a href="#" class="nav-link" onclick="showSection('postulaciones')">Mis Postulaciones</a>
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
                    <div class="stat-number"><?php echo mysqli_num_rows($result_semilleros); ?></div>
                    <div class="stat-label">Semilleros Disponibles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo mysqli_num_rows($result_postulaciones); ?></div>
                    <div class="stat-label">Mis Postulaciones</div>
                </div>
                <div class="stat-card">
                    <?php
                    mysqli_data_seek($result_postulaciones, 0);
                    $postulaciones_aceptadas = 0;
                    while ($p = mysqli_fetch_assoc($result_postulaciones)) {
                        if ($p['estado'] == 'aceptada') $postulaciones_aceptadas++;
                    }
                    mysqli_data_seek($result_postulaciones, 0);
                    ?>
                    <div class="stat-number"><?php echo $postulaciones_aceptadas; ?></div>
                    <div class="stat-label">Postulaciones Aceptadas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo mysqli_num_rows($result_actividades); ?></div>
                    <div class="stat-label">Actividades Pendientes</div>
                </div>
            </div>
            
            <div class="card">
                <h3>Acciones Rápidas</h3>
                <p>Desde aquí puedes:</p>
                <ul style="text-align: left; margin: 15px 0;">
                    <li>Ver y realizar las actividades de tus semilleros</li>
                    <li>Ver semilleros disponibles para postularte</li>
                    <li>Revisar el estado de tus postulaciones</li>
                </ul>
            </div>
        </div>
        
        <!-- Sección Mis Actividades -->
        <div id="actividades-section" class="section hidden">
            <h2 class="section-title">Mis Actividades</h2>
            
            <?php if (mysqli_num_rows($result_actividades) > 0): ?>
                <?php while ($actividad = mysqli_fetch_assoc($result_actividades)): ?>
                    <?php
                    $fecha_actual = date('Y-m-d');
                    $fecha_entrega = $actividad['fecha_entrega'];
                    $dias_restantes = 0;
                    $clase_fecha = '';
                    
                    if ($fecha_entrega) {
                        $fecha_entrega_obj = new DateTime($fecha_entrega);
                        $fecha_actual_obj = new DateTime($fecha_actual);
                        $dias_restantes = $fecha_actual_obj->diff($fecha_entrega_obj)->days;
                        $es_pasada = $fecha_actual_obj > $fecha_entrega_obj;
                        
                        if ($es_pasada) {
                            $clase_fecha = 'vencida';
                        } elseif ($dias_restantes <= 3) {
                            $clase_fecha = 'urgente';
                        }
                    }
                    ?>
                    
                    <div class="actividad-card">
                        <div class="actividad-semillero">
                            📚 <?php echo htmlspecialchars($actividad['semillero_nombre']); ?>
                        </div>
                        
                        <div class="actividad-header">
                            <div class="actividad-titulo">
                                <?php echo htmlspecialchars($actividad['titulo']); ?>
                            </div>
                            
                            <?php if ($actividad['fecha_entrega']): ?>
                                <div class="fecha-entrega <?php echo $clase_fecha; ?>">
                                    <?php if ($clase_fecha == 'vencida'): ?>
                                        ⚠️ Vencida: <?php echo date('d/m/Y', strtotime($actividad['fecha_entrega'])); ?>
                                    <?php elseif ($clase_fecha == 'urgente'): ?>
                                        🕒 Entrega: <?php echo date('d/m/Y', strtotime($actividad['fecha_entrega'])); ?>
                                        <br><small>(<?php echo $dias_restantes; ?> días restantes)</small>
                                    <?php else: ?>
                                        📅 Entrega: <?php echo date('d/m/Y', strtotime($actividad['fecha_entrega'])); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($actividad['descripcion']): ?>
                            <div class="actividad-descripcion">
                                <?php echo nl2br(htmlspecialchars($actividad['descripcion'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($actividad['entregable']): ?>
                            <div class="actividad-entregable">
                                <h4>📋 Entregable</h4>
                                <?php echo nl2br(htmlspecialchars($actividad['entregable'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; color: #666; font-size: 14px;">
                            <strong>Tutor:</strong> <?php echo htmlspecialchars($actividad['tutor_nombre']); ?> |
                            <strong>Creada:</strong> <?php echo date('d/m/Y', strtotime($actividad['fecha_creacion'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No tienes actividades pendientes</h3>
                    <p>No hay actividades asignadas en tus semilleros aceptados.</p>
                    <?php
                    mysqli_data_seek($result_postulaciones, 0);
                    $tiene_aceptadas = false;
                    while ($p = mysqli_fetch_assoc($result_postulaciones)) {
                        if ($p['estado'] == 'aceptada') {
                            $tiene_aceptadas = true;
                            break;
                        }
                    }
                    ?>
                    <?php if (!$tiene_aceptadas): ?>
                        <p>Primero debes ser aceptado en algún semillero para ver las actividades.</p>
                        <button onclick="showSection('semilleros')" class="btn-cta btn-primary" style="margin-top: 15px;">
                            Ver Semilleros Disponibles
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sección Semilleros Disponibles -->
        <div id="semilleros-section" class="section hidden">
            <h2 class="section-title">Semilleros Disponibles</h2>
            
            <?php if (mysqli_num_rows($result_semilleros) > 0): ?>
                <?php while ($semillero = mysqli_fetch_assoc($result_semilleros)): ?>
                    <?php
                    // Verificar si ya se postuló a este semillero
                    mysqli_data_seek($result_postulaciones, 0);
                    $ya_postulado = false;
                    while ($p = mysqli_fetch_assoc($result_postulaciones)) {
                        if ($p['semillero_id'] == $semillero['id']) {
                            $ya_postulado = true;
                            break;
                        }
                    }
                    mysqli_data_seek($result_postulaciones, 0);
                    ?>
                    
                    <div class="card">
                        <div class="card-title"><?php echo htmlspecialchars($semillero['nombre']); ?></div>
                        
                        <?php if ($semillero['tutor_nombre']): ?>
                            <div class="card-info">
                                <strong>Tutor:</strong> <?php echo htmlspecialchars($semillero['tutor_nombre']); ?>
                            </div>
                        <?php endif; ?>
                        
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
                        
                        <div style="margin-top: 15px;">
                            <?php if ($ya_postulado): ?>
                                <span style="color: #28a745; font-weight: bold;">✓ Ya te has postulado a este semillero</span>
                            <?php else: ?>
                                <form action="../postular_semillero.php" method="post" style="display: inline;">
                                    <input type="hidden" name="semillero_id" value="<?php echo $semillero['id']; ?>">
                                    <button type="submit" class="btn-small btn-success">Postularme</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No hay semilleros disponibles</h3>
                    <p>En este momento no hay semilleros abiertos para postulaciones.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sección Mis Postulaciones -->
        <div id="postulaciones-section" class="section hidden">
            <h2 class="section-title">Mis Postulaciones</h2>
            
            <?php if (mysqli_num_rows($result_postulaciones) > 0): ?>
                <?php while ($postulacion = mysqli_fetch_assoc($result_postulaciones)): ?>
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                            <div class="card-title"><?php echo htmlspecialchars($postulacion['semillero_nombre']); ?></div>
                            <div class="estado-badge estado-<?php echo $postulacion['estado']; ?>">
                                <?php echo ucfirst($postulacion['estado']); ?>
                            </div>
                        </div>
                        
                        <?php if ($postulacion['tutor_nombre']): ?>
                            <div class="card-info">
                                <strong>Tutor:</strong> <?php echo htmlspecialchars($postulacion['tutor_nombre']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($postulacion['semillero_descripcion']): ?>
                            <div class="card-info">
                                <strong>Descripción:</strong><br>
                                <?php echo nl2br(htmlspecialchars($postulacion['semillero_descripcion'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-info">
                            <strong>Fecha de postulación:</strong> 
                            <?php echo date('d/m/Y H:i', strtotime($postulacion['fecha_postulacion'])); ?>
                        </div>
                        
                        <?php if ($postulacion['estado'] == 'aceptada'): ?>
                            <div style="margin-top: 15px; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; color: #155724;">
                                <strong>¡Felicitaciones!</strong> Tu postulación ha sido aceptada. 
                                El tutor se pondrá en contacto contigo pronto.
                                <br><small>Revisa la sección "Mis Actividades" para ver las tareas asignadas.</small>
                            </div>
                        <?php elseif ($postulacion['estado'] == 'rechazada'): ?>
                            <div style="margin-top: 15px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 6px; color: #721c24;">
                                Tu postulación no fue aceptada en esta ocasión. 
                                Te animamos a postularte a otros semilleros.
                            </div>
                        <?php else: ?>
                            <div style="margin-top: 15px; padding: 10px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; color: #856404;">
                                Tu postulación está siendo revisada por el tutor.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No tienes postulaciones</h3>
                    <p>Aún no te has postulado a ningún semillero.</p>
                    <button onclick="showSection('semilleros')" class="btn-cta btn-primary" style="margin-top: 15px;">
                        Ver Semilleros Disponibles
                    </button>
                </div>
            <?php endif; ?>
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