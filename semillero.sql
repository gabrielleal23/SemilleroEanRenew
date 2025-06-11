CREATE DATABASE IF NOT EXISTS semillero;
USE semillero;

-- Tabla de usuarios (pueden ser estudiantes, tutores o admin)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(15),
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('estudiante', 'tutor', 'admin') NOT NULL DEFAULT 'estudiante',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de semilleros
CREATE TABLE semilleros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    objetivo_principal TEXT,
    objetivos_especificos TEXT,
    tutor_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    FOREIGN KEY (tutor_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Tabla de actividades
CREATE TABLE actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semillero_id INT,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATE,
    entregable TEXT,
    FOREIGN KEY (semillero_id) REFERENCES semilleros(id) ON DELETE CASCADE
);

-- Tabla de postulaciones (usuarios se postulan a semilleros)
CREATE TABLE postulaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    semillero_id INT,
    fecha_postulacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'aceptada', 'rechazada') DEFAULT 'pendiente',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (semillero_id) REFERENCES semilleros(id) ON DELETE CASCADE,
    UNIQUE KEY unique_postulacion (usuario_id, semillero_id)
);

-- Insertar usuario admin por defecto
INSERT INTO usuarios (nombre, cedula, correo, telefono, contrasena, rol) 
VALUES ('Administrador', '12345678', 'admin@ean.edu.co', '3001234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertar algunos usuarios de ejemplo
INSERT INTO usuarios (nombre, cedula, correo, telefono, contrasena, rol) 
VALUES 
('Juan Pérez', '98765432', 'juan.perez@ean.edu.co', '3009876543', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
('María García', '11223344', 'maria.garcia@ean.edu.co', '3011223344', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante');