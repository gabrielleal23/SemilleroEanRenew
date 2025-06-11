<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semilleros EAN - Universidad EAN</title>
    <link rel="icon" href="icon/ean_logo.png" type="image/png">
    <link rel="stylesheet" href="../css/style2.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #89E0B7 0%, #3BAC53 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            min-height: 50vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .hero-content h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-content p {
            font-size: 1.2em;
            margin-bottom: 30px;
            max-width: 600px;
            line-height: 1.6;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-cta {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            min-width: 150px;
        }
        
        .btn-primary {
            background-color: #005baa;
            color: white;
            border: 2px solid #005baa;
        }
        
        .btn-primary:hover {
            background-color: #004494;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            background-color: white;
            color: #005baa;
            transform: translateY(-2px);
        }
        
        .features-section {
            padding: 60px 20px;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background-color: #005baa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 24px;
        }
        
        .welcome-user {
            background-color: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .dashboard-link {
            background-color: #28a745;
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        
        .dashboard-link:hover {
            background-color: #218838;
        }
        
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2em;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-cta {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="hero-content">
            <img src="../icon/ean_logo.png" alt="EAN Logo" style="width: 80px; margin-bottom: 20px;">
            
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="welcome-user">
                    <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
                    <p>Rol: <?php echo ucfirst($_SESSION['rol']); ?></p>
                    
                    <?php
                    $dashboard_url = '';
                    switch ($_SESSION['rol']) {
                        case 'admin':
                            $dashboard_url = 'views/admin_dashboard.php';
                            break;
                        case 'tutor':
                            $dashboard_url = 'views/admin_dashboard.php';
                            break;
                        case 'estudiante':
                            $dashboard_url = 'views/estudiante_dashboard.php';
                            break;
                        default:
                            $dashboard_url = 'login.php';
                    }
                    ?>
                    
                    <a href="<?php echo $dashboard_url; ?>" class="dashboard-link">
                        Ir a mi Panel de Control
                    </a>
                    <br>
                    <a href="logout.php" style="color: white; text-decoration: underline; margin-top: 10px; display: inline-block;">
                        Cerrar Sesión
                    </a>
                </div>
            <?php else: ?>
                <h1>Semilleros de Investigación</h1>
                <p>
                    Únete a nuestra comunidad académica y participa en proyectos de investigación 
                    que fortalecerán tu formación profesional y contribuirán al desarrollo del conocimiento.
                </p>
                
                <div class="cta-buttons">
                    <a href="login.php" class="btn-cta btn-primary">Iniciar Sesión</a>
                    <a href="register.php" class="btn-cta btn-secondary">Registrarse</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="features-section">
        <div class="container">
            <h2 style="text-align: center; color: #333; margin-bottom: 20px;">
                ¿Qué son los Semilleros de Investigación?
            </h2>
            <p style="text-align: center; color: #666; max-width: 800px; margin: 0 auto;">
                Los semilleros son espacios académicos donde estudiantes y tutores desarrollan 
                proyectos de investigación colaborativos, fomentando el pensamiento crítico y la innovación.
            </p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎓</div>
                    <h3>Para Estudiantes</h3>
                    <p>Postúlate a semilleros de tu interés y desarrolla habilidades investigativas 
                    mientras contribuyes a proyectos significativos.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">👨‍🏫</div>
                    <h3>Para Tutores</h3>
                    <p>Crea y gestiona semilleros, guía a estudiantes talentosos y lidera 
                    proyectos de investigación innovadores.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⚙️</div>
                    <h3>Administración</h3>
                    <p>Herramientas completas para administrar semilleros, usuarios y 
                    supervisar el progreso de los proyectos.</p>
                </div>
            </div>
            
            <?php if (!isset($_SESSION['usuario_id'])): ?>
                <div style="text-align: center; margin-top: 40px;">
                    <h3 style="color: #333;">¿Listo para comenzar?</h3>
                    <p style="color: #666;">Únete a nuestra comunidad de investigadores</p>
                    <a href="register.php" class="btn-cta btn-primary" style="margin-right: 15px;">
                        Crear Cuenta
                    </a>
                    <a href="login.php" class="btn-cta btn-secondary" style="background-color: #6c757d; border-color: #6c757d; color: white;">
                        Ya tengo cuenta
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer style="background-color: #333; color: white; text-align: center; padding: 20px;">
        <p>&copy; 2025 Universidad EAN - Semilleros de Investigación</p>
    </footer>
</body>
</html>