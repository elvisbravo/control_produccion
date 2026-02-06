-- ============================================
-- Base de Datos: Sistema de Control de Producción
-- Empresa de Servicios Académicos
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS control_produccion CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE control_produccion;

-- ============================================
-- TABLAS DE AUTENTICACIÓN Y USUARIOS
-- ============================================

-- Tabla de perfiles/roles
CREATE TABLE IF NOT EXISTS perfiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_perfil VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    fecha_nacimiento DATE,
    perfil_id INT NOT NULL,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (perfil_id) REFERENCES perfiles(id) ON DELETE RESTRICT,
    INDEX idx_correo (correo),
    INDEX idx_perfil (perfil_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de módulos del sistema
CREATE TABLE IF NOT EXISTS modulos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_modulo VARCHAR(100) NOT NULL,
    icono VARCHAR(50),
    url VARCHAR(150),
    orden INT DEFAULT 0,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de permisos
CREATE TABLE IF NOT EXISTS permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perfil_id INT NOT NULL,
    modulo_id INT NOT NULL,
    puede_ver TINYINT(1) DEFAULT 0,
    puede_crear TINYINT(1) DEFAULT 0,
    puede_editar TINYINT(1) DEFAULT 0,
    puede_eliminar TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (perfil_id) REFERENCES perfiles(id) ON DELETE CASCADE,
    FOREIGN KEY (modulo_id) REFERENCES modulos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_perfil_modulo (perfil_id, modulo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLAS DE CLIENTES Y SERVICIOS
-- ============================================

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    telefono VARCHAR(20),
    empresa VARCHAR(150),
    direccion VARCHAR(255),
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombres (nombres, apellidos),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de tipos de servicio
CREATE TABLE IF NOT EXISTS tipos_servicio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    horas_estimadas_base DECIMAL(6,2) DEFAULT 0,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de servicios/trabajos
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    cliente_id INT NOT NULL,
    captador_id INT NOT NULL COMMENT 'Usuario que captó el cliente',
    tipo_servicio_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    horas_estimadas DECIMAL(6,2) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_inicio DATE,
    fecha_limite DATE COMMENT 'Fecha límite establecida por el cliente',
    fecha_entrega_calculada DATETIME COMMENT 'Fecha calculada automáticamente',
    fecha_entrega_real DATETIME,
    jefe_produccion_id INT COMMENT 'Jefe asignado para revisar',
    auxiliar_produccion_id INT COMMENT 'Auxiliar asignado para ejecutar',
    estado ENUM('Pendiente', 'En Proceso', 'En Revisión', 'Completado', 'Entregado') DEFAULT 'Pendiente',
    prioridad ENUM('Baja', 'Media', 'Alta', 'Urgente') DEFAULT 'Media',
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (captador_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (tipo_servicio_id) REFERENCES tipos_servicio(id) ON DELETE RESTRICT,
    FOREIGN KEY (jefe_produccion_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (auxiliar_produccion_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_codigo (codigo),
    INDEX idx_estado (estado),
    INDEX idx_auxiliar (auxiliar_produccion_id),
    INDEX idx_jefe (jefe_produccion_id),
    INDEX idx_fecha_limite (fecha_limite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de historial de cambios de servicios
CREATE TABLE IF NOT EXISTS historial_servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    usuario_id INT NOT NULL,
    estado_anterior VARCHAR(50),
    estado_nuevo VARCHAR(50),
    comentario TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_servicio (servicio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLAS DE CALENDARIO LABORAL
-- ============================================

-- Tabla de feriados
CREATE TABLE IF NOT EXISTS feriados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    fecha DATE NOT NULL,
    tipo ENUM('Nacional', 'Regional', 'Local') DEFAULT 'Nacional',
    es_laborable TINYINT(1) DEFAULT 0 COMMENT '1 si se trabaja ese día',
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de cumpleaños de auxiliares (para excluir del calendario)
CREATE TABLE IF NOT EXISTS cumpleanos_auxiliares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha_cumpleanos DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_fecha (fecha_cumpleanos),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de asignaciones de auxiliar (para tracking)
CREATE TABLE IF NOT EXISTS asignaciones_auxiliar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    auxiliar_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_desasignacion TIMESTAMP NULL,
    asignado_por INT NOT NULL,
    motivo_cambio TEXT,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE,
    FOREIGN KEY (auxiliar_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (asignado_por) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_servicio (servicio_id),
    INDEX idx_auxiliar (auxiliar_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Insertar perfiles
INSERT INTO perfiles (id, nombre_perfil, descripcion, estado) VALUES
(1, 'Administrador', 'Acceso completo al sistema', 1),
(2, 'Captador', 'Capta clientes y registra servicios', 1),
(3, 'Jefe de Producción', 'Revisa y supervisa trabajos', 1),
(4, 'Auxiliar de Producción', 'Ejecuta los trabajos asignados', 1);

-- Insertar usuario administrador (password: admin123)
INSERT INTO usuarios (nombres, apellidos, correo, password, perfil_id, estado) VALUES
('Administrador', 'Sistema', 'admin@controlproduccion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- Insertar módulos del sistema
INSERT INTO modulos (nombre_modulo, icono, url, orden, estado) VALUES
('Dashboard', 'fas fa-tachometer-alt', '/dashboard', 1, 1),
('Servicios', 'fas fa-briefcase', '/servicios', 2, 1),
('Clientes', 'fas fa-users', '/clientes', 3, 1),
('Calendario', 'fas fa-calendar-alt', '/calendario', 4, 1),
('Feriados', 'fas fa-calendar-times', '/feriados', 5, 1),
('Usuarios', 'fas fa-user-cog', '/usuarios', 6, 1),
('Permisos', 'fas fa-lock', '/permisos', 7, 1);

-- Insertar permisos para Administrador (acceso completo)
INSERT INTO permisos (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar) VALUES
(1, 1, 1, 1, 1, 1), -- Dashboard
(1, 2, 1, 1, 1, 1), -- Servicios
(1, 3, 1, 1, 1, 1), -- Clientes
(1, 4, 1, 1, 1, 1), -- Calendario
(1, 5, 1, 1, 1, 1), -- Feriados
(1, 6, 1, 1, 1, 1), -- Usuarios
(1, 7, 1, 1, 1, 1); -- Permisos

-- Insertar permisos para Captador
INSERT INTO permisos (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar) VALUES
(2, 1, 1, 0, 0, 0), -- Dashboard (solo ver)
(2, 2, 1, 1, 1, 0), -- Servicios (crear y editar)
(2, 3, 1, 1, 1, 0), -- Clientes (crear y editar)
(2, 4, 1, 0, 0, 0); -- Calendario (solo ver)

-- Insertar permisos para Jefe de Producción
INSERT INTO permisos (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar) VALUES
(3, 1, 1, 0, 0, 0), -- Dashboard
(3, 2, 1, 0, 1, 0), -- Servicios (ver y editar)
(3, 4, 1, 0, 0, 0); -- Calendario

-- Insertar permisos para Auxiliar de Producción
INSERT INTO permisos (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar) VALUES
(4, 1, 1, 0, 0, 0), -- Dashboard
(4, 2, 1, 0, 0, 0), -- Servicios (solo ver)
(4, 4, 1, 0, 0, 0); -- Calendario

-- Insertar tipos de servicio
INSERT INTO tipos_servicio (nombre, descripcion, horas_estimadas_base, estado) VALUES
('Tesis de Pregrado', 'Elaboración de tesis para pregrado', 80.00, 1),
('Tesis de Maestría', 'Elaboración de tesis para maestría', 120.00, 1),
('Tesis de Doctorado', 'Elaboración de tesis para doctorado', 200.00, 1),
('Artículo Científico', 'Redacción de artículo científico', 40.00, 1),
('Informe de Investigación', 'Elaboración de informe de investigación', 30.00, 1),
('Monografía', 'Elaboración de monografía', 25.00, 1),
('Proyecto de Investigación', 'Diseño de proyecto de investigación', 35.00, 1),
('Revisión y Corrección', 'Revisión y corrección de trabajos', 15.00, 1);

-- Insertar feriados de Perú 2025
INSERT INTO feriados (nombre, fecha, tipo, es_laborable, estado) VALUES
('Año Nuevo', '2025-01-01', 'Nacional', 0, 1),
('Jueves Santo', '2025-04-17', 'Nacional', 0, 1),
('Viernes Santo', '2025-04-18', 'Nacional', 0, 1),
('Día del Trabajo', '2025-05-01', 'Nacional', 0, 1),
('San Pedro y San Pablo', '2025-06-29', 'Nacional', 0, 1),
('Fiestas Patrias', '2025-07-28', 'Nacional', 0, 1),
('Fiestas Patrias', '2025-07-29', 'Nacional', 0, 1),
('Santa Rosa de Lima', '2025-08-30', 'Nacional', 0, 1),
('Combate de Angamos', '2025-10-08', 'Nacional', 0, 1),
('Todos los Santos', '2025-11-01', 'Nacional', 0, 1),
('Inmaculada Concepción', '2025-12-08', 'Nacional', 0, 1),
('Navidad', '2025-12-25', 'Nacional', 0, 1);

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger para actualizar cumpleaños cuando se actualiza fecha_nacimiento
DELIMITER $$

CREATE TRIGGER after_usuario_insert
AFTER INSERT ON usuarios
FOR EACH ROW
BEGIN
    IF NEW.fecha_nacimiento IS NOT NULL AND NEW.perfil_id = 4 THEN
        INSERT INTO cumpleanos_auxiliares (usuario_id, fecha_cumpleanos)
        VALUES (NEW.id, NEW.fecha_nacimiento);
    END IF;
END$$

CREATE TRIGGER after_usuario_update
AFTER UPDATE ON usuarios
FOR EACH ROW
BEGIN
    IF NEW.perfil_id = 4 AND NEW.fecha_nacimiento IS NOT NULL THEN
        -- Eliminar registro anterior si existe
        DELETE FROM cumpleanos_auxiliares WHERE usuario_id = NEW.id;
        -- Insertar nuevo registro
        INSERT INTO cumpleanos_auxiliares (usuario_id, fecha_cumpleanos)
        VALUES (NEW.id, NEW.fecha_nacimiento);
    END IF;
END$$

-- Trigger para registrar cambios de estado en historial
CREATE TRIGGER after_servicio_update
AFTER UPDATE ON servicios
FOR EACH ROW
BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO historial_servicios (servicio_id, usuario_id, estado_anterior, estado_nuevo, comentario)
        VALUES (NEW.id, 1, OLD.estado, NEW.estado, CONCAT('Estado cambiado de ', OLD.estado, ' a ', NEW.estado));
    END IF;
END$$

DELIMITER ;

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista de servicios con información completa
CREATE OR REPLACE VIEW vista_servicios_completa AS
SELECT 
    s.id,
    s.codigo,
    s.titulo,
    s.descripcion,
    CONCAT(c.nombres, ' ', c.apellidos) AS cliente,
    c.email AS cliente_email,
    c.telefono AS cliente_telefono,
    CONCAT(cap.nombres, ' ', cap.apellidos) AS captador,
    ts.nombre AS tipo_servicio,
    s.horas_estimadas,
    s.fecha_registro,
    s.fecha_inicio,
    s.fecha_limite,
    s.fecha_entrega_calculada,
    s.fecha_entrega_real,
    CONCAT(jefe.nombres, ' ', jefe.apellidos) AS jefe_produccion,
    CONCAT(aux.nombres, ' ', aux.apellidos) AS auxiliar_produccion,
    aux.id AS auxiliar_id,
    s.estado,
    s.prioridad,
    s.observaciones,
    CASE 
        WHEN s.fecha_limite < CURDATE() AND s.estado NOT IN ('Completado', 'Entregado') THEN 'Atrasado'
        WHEN DATEDIFF(s.fecha_limite, CURDATE()) <= 3 AND s.estado NOT IN ('Completado', 'Entregado') THEN 'Próximo a vencer'
        ELSE 'Normal'
    END AS alerta
FROM servicios s
INNER JOIN clientes c ON s.cliente_id = c.id
INNER JOIN usuarios cap ON s.captador_id = cap.id
INNER JOIN tipos_servicio ts ON s.tipo_servicio_id = ts.id
LEFT JOIN usuarios jefe ON s.jefe_produccion_id = jefe.id
LEFT JOIN usuarios aux ON s.auxiliar_produccion_id = aux.id;

-- Vista de carga de trabajo por auxiliar
CREATE OR REPLACE VIEW vista_carga_auxiliares AS
SELECT 
    u.id AS auxiliar_id,
    CONCAT(u.nombres, ' ', u.apellidos) AS auxiliar,
    COUNT(s.id) AS total_trabajos,
    SUM(CASE WHEN s.estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
    SUM(CASE WHEN s.estado = 'En Proceso' THEN 1 ELSE 0 END) AS en_proceso,
    SUM(CASE WHEN s.estado = 'En Revisión' THEN 1 ELSE 0 END) AS en_revision,
    SUM(s.horas_estimadas) AS total_horas_asignadas,
    SUM(CASE WHEN s.estado NOT IN ('Completado', 'Entregado') THEN s.horas_estimadas ELSE 0 END) AS horas_pendientes
FROM usuarios u
LEFT JOIN servicios s ON u.id = s.auxiliar_produccion_id
WHERE u.perfil_id = 4 AND u.estado = 1
GROUP BY u.id, u.nombres, u.apellidos;

-- ============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================

CREATE INDEX idx_servicios_fechas ON servicios(fecha_inicio, fecha_limite, estado);
CREATE INDEX idx_historial_fecha ON historial_servicios(created_at);

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
