CREATE DATABASE IF NOT EXISTS sistema_caja
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_caja;

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';
START TRANSACTION;

-- =========================
-- TABLA: ROL
-- =========================
CREATE TABLE IF NOT EXISTS rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    rol VARCHAR(15) NOT NULL
) ENGINE=InnoDB;

INSERT INTO rol (rol) VALUES
('Ahorrador'),
('Administrador'),
('Super Usuario');

-- =========================
-- TABLA: USUARIO
-- =========================
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50) NOT NULL,
    correo_institucional VARCHAR(100) UNIQUE,
    correo_personal VARCHAR(100) UNIQUE,
    rfc VARCHAR(13) UNIQUE,
    curp VARCHAR(18) UNIQUE,
    telefono VARCHAR(15) NOT NULL,
    contrasena VARCHAR(255),
    tarjeta VARCHAR(20),
    habilitado TINYINT DEFAULT 1,
    id_rol INT NOT NULL,
    INDEX idx_usuario_rol (id_rol),
    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO usuario
(nombre, apellido_paterno, apellido_materno, correo_institucional, telefono, contrasena, id_rol)
VALUES
('Administrador', 'General', 'Sistema', 'admin@itsx.edu.mx', '0000000000', '12345', 2),
('Super', 'Usuario', 'Administrador', 'superadmin@itsx.edu.mx', '0000000000', 'super123', 3);

-- =========================
-- TABLA: AHORRO
-- =========================
CREATE TABLE IF NOT EXISTS ahorro (
    id_ahorro INT AUTO_INCREMENT PRIMARY KEY,
    monto_ahorrado DECIMAL(10,2) DEFAULT 0.00,
    fecha_ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NOT NULL,
    UNIQUE KEY uk_ahorro_usuario (id_usuario),
    CONSTRAINT fk_ahorro_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =========================
-- TABLA: TIPO MOVIMIENTO
-- =========================
CREATE TABLE IF NOT EXISTS tipo_movimiento (
    id_tipo_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    tipo_movimiento VARCHAR(50)
) ENGINE=InnoDB;

INSERT INTO tipo_movimiento (tipo_movimiento) VALUES
('Depósito'),
('Retiro'),
('Pago Préstamo');

-- =========================
-- TABLA: MOVIMIENTO
-- =========================
CREATE TABLE IF NOT EXISTS movimiento (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    concepto VARCHAR(255),
    monto DECIMAL(10,2),
    id_usuario INT NOT NULL,
    id_tipo_movimiento INT NOT NULL,
    INDEX idx_mov_usuario (id_usuario),
    INDEX idx_mov_tipo (id_tipo_movimiento),
    CONSTRAINT fk_mov_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    CONSTRAINT fk_mov_tipo
        FOREIGN KEY (id_tipo_movimiento) REFERENCES tipo_movimiento(id_tipo_movimiento)
) ENGINE=InnoDB;

-- =========================
-- TABLA: ESTADO
-- =========================
CREATE TABLE IF NOT EXISTS estado (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    estado VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

INSERT INTO estado (estado) VALUES
('Pendiente'), ('Aprobado'), ('Rechazado'), ('Pagado'), ('Cancelado');

-- =========================
-- TABLA: SOLICITUD PRESTAMO
-- =========================
CREATE TABLE IF NOT EXISTS solicitud_prestamo (
    id_solicitud_prestamo INT AUTO_INCREMENT PRIMARY KEY,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    monto_solicitado DECIMAL(10,2) NOT NULL,
    plazo_quincenas INT NOT NULL,
    monto_pago_quincenal DECIMAL(10,2),
    total_a_pagar DECIMAL(10,2) NOT NULL,
    saldo_pendiente DECIMAL(10,2) NOT NULL,
    archivo_pagare VARCHAR(255) NOT NULL,
    id_usuario INT NOT NULL,
    id_estado INT DEFAULT 1,
    INDEX idx_prestamo_usuario (id_usuario),
    INDEX idx_prestamo_estado (id_estado),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_estado) REFERENCES estado(id_estado)
) ENGINE=InnoDB;

-- =========================
-- TABLA: SOLICITUD AHORRO
-- =========================
CREATE TABLE IF NOT EXISTS solicitud_ahorro (
    id_solicitud_ahorro INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    monto_solicitado DECIMAL(10,2),
    nomina DECIMAL(10,2) NOT NULL,
    archivo_nomina VARCHAR(255) NOT NULL,
    archivo_solicitud VARCHAR(255),
    id_usuario INT NOT NULL,
    id_estado INT DEFAULT 1,
    INDEX idx_ahorro_usuario (id_usuario),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_estado) REFERENCES estado(id_estado)
) ENGINE=InnoDB;

-- =========================
-- TABLA: DATOS SISTEMA
-- =========================
CREATE TABLE IF NOT EXISTS datos_sistema (
    id_datos INT AUTO_INCREMENT PRIMARY KEY,
    nombre_director VARCHAR(200),
    periodo VARCHAR(50),
    nombre_enc_personal VARCHAR(200),
    tasa_interes_general DECIMAL(5,2) DEFAULT 30.00,
    rendimiento_anual_ahorros DECIMAL(5,2) DEFAULT 5.00,
    correo_soporte VARCHAR(100) DEFAULT 'soporte@itsx.com',
    usuario_actualizacion VARCHAR(100),
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO datos_sistema
(nombre_director, periodo, nombre_enc_personal)
VALUES
('Sidney René Toledo Martínez', '2025-2026', 'Teresa de Jesús Hernández Reyes');

-- =========================
-- AUDITORÍAS
-- =========================
CREATE TABLE IF NOT EXISTS auditoria_usuario (
    id_auditoria INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    accion VARCHAR(20) NOT NULL,
    campo_modificado VARCHAR(100),
    valor_anterior VARCHAR(255),
    valor_nuevo VARCHAR(255),
    usuario_responsable VARCHAR(100),
    fecha_cambio DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS auditoria_solicitudes (
    id_auditoria INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT,
    tipo_solicitud VARCHAR(20),
    campo_modificado VARCHAR(50),
    valor_anterior VARCHAR(255),
    valor_nuevo VARCHAR(255),
    usuario_responsable VARCHAR(100),
    fecha_cambio DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================
-- TRIGGERS
-- =========================
DELIMITER $$

CREATE TRIGGER crear_cuenta_ahorro
AFTER INSERT ON usuario
FOR EACH ROW
BEGIN
    INSERT INTO ahorro (id_usuario) VALUES (NEW.id_usuario);
END $$

CREATE TRIGGER actualizar_saldo_ahorro
AFTER INSERT ON movimiento
FOR EACH ROW
BEGIN
    IF NEW.id_tipo_movimiento = 1 THEN
        UPDATE ahorro SET monto_ahorrado = monto_ahorrado + NEW.monto WHERE id_usuario = NEW.id_usuario;
    ELSEIF NEW.id_tipo_movimiento = 2 THEN
        UPDATE ahorro SET monto_ahorrado = monto_ahorrado - NEW.monto WHERE id_usuario = NEW.id_usuario;
    END IF;
END $$

CREATE TRIGGER actualizar_deuda_prestamo
AFTER INSERT ON movimiento
FOR EACH ROW
BEGIN
    IF NEW.id_tipo_movimiento = 3 THEN
        UPDATE solicitud_prestamo
        SET saldo_pendiente = saldo_pendiente - NEW.monto
        WHERE id_usuario = NEW.id_usuario AND id_estado = 2;
    END IF;
END $$

DELIMITER ;

COMMIT;
