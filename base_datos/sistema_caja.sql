CREATE DATABASE IF NOT EXISTS sistema_caja;
USE sistema_caja;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS rol(
    id_rol INT NOT NULL AUTO_INCREMENT,
    rol VARCHAR(15) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY(id_rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO rol (rol) VALUES 
('Administrador'),
('Ahorrador'),
('Super Usuario');

CREATE TABLE IF NOT EXISTS usuario (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    apellido_paterno VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    apellido_materno VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    correo_institucional VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    correo_personal VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    rfc VARCHAR(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    curp VARCHAR(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    telefono VARCHAR(15) COLLATE utf8mb4_unicode_ci NOT NULL,
    contrasena VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    tarjeta VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    habilitado TINYINT NOT NULL DEFAULT 1,
    id_rol INT NOT NULL,
    UNIQUE (correo_institucional),
    UNIQUE (correo_personal),
    PRIMARY KEY (id_usuario),
    CONSTRAINT fk_usuario_rol FOREIGN KEY (id_rol)
        REFERENCES rol(id_rol)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO usuario (nombre, apellido_paterno, apellido_materno, correo_institucional, correo_personal, rfc, curp, telefono, contrasena, tarjeta, id_rol) VALUES
('Administrador', 'General', 'Sistema', 'admin@itsx.edu.mx', NULL, NULL, NULL, '0000000000', '12345', NULL, 1),
('Juan', 'Bello', 'Zuñiga', '227O02930@itsx.edu.mx', 'bellozun12@gmail.com', 'BEZJ040831B99', 'BEZJ040831HVZLXNA8', '7841310586', 'contraseña1234', '4217470088983305', 2);

CREATE TABLE IF NOT EXISTS ahorro (
    id_ahorro INT NOT NULL AUTO_INCREMENT,
    monto_ahorrado DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    id_usuario INT NOT NULL,
    PRIMARY KEY (id_ahorro),
    CONSTRAINT fk_usuario_ahorro FOREIGN KEY(id_usuario)
        REFERENCES usuario(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ahorro (monto_ahorrado, id_usuario) VALUES (0.00, 2);

CREATE TABLE IF NOT EXISTS tipo_movimiento(
    id_tipo_movimiento INT NOT NULL AUTO_INCREMENT,
    tipo_movimiento VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (id_tipo_movimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tipo_movimiento (tipo_movimiento) VALUES
('Depósito'),
('Retiro'),
('Pago Préstamo');

CREATE TABLE IF NOT EXISTS movimiento(
    id_movimiento INT NOT NULL AUTO_INCREMENT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    concepto VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    monto DECIMAL(10,2) DEFAULT NULL,
    id_usuario INT NOT NULL,
    id_tipo_movimiento INT NOT NULL,
    PRIMARY KEY (id_movimiento),
    CONSTRAINT fk_usuario_movimiento FOREIGN KEY(id_usuario)
        REFERENCES usuario(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_movimiento_tipo_movimiento FOREIGN KEY(id_tipo_movimiento)
        REFERENCES tipo_movimiento(id_tipo_movimiento)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS estado (
    id_estado INT NOT NULL AUTO_INCREMENT,
    estado VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (id_estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO estado (estado) VALUES
('Pendiente'),
('Aprobado'),
('Rechazado'),
('Pagado'),
('Cancelado');

CREATE TABLE IF NOT EXISTS solicitud_prestamo(
    id_solicitud_prestamo INT NOT NULL AUTO_INCREMENT,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    monto_solicitado DECIMAL(10,2) NOT NULL,
    plazo_quincenas INT NOT NULL,
    monto_pago_quincenal DECIMAL(10,2) DEFAULT NULL,
    total_a_pagar DECIMAL(10,2) NOT NULL,
    saldo_pendiente DECIMAL(10,2) NOT NULL,
    archivo_pagare VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    id_usuario INT NOT NULL,
    id_estado INT NOT NULL DEFAULT 1,
    PRIMARY KEY(id_solicitud_prestamo),
    CONSTRAINT fk_solicitud_prestamo_usuario FOREIGN KEY(id_usuario)
        REFERENCES usuario(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_solicitud_prestamo_estado FOREIGN KEY (id_estado)
        REFERENCES estado(id_estado)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS solicitud_ahorro(
    id_solicitud_ahorro INT NOT NULL AUTO_INCREMENT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    monto_solicitado DECIMAL(10,2) DEFAULT NULL,
    nomina DECIMAL(10,2) NOT NULL,
    archivo_nomina VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    archivo_solicitud VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    id_usuario INT NOT NULL,
    id_estado INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id_solicitud_ahorro),
    CONSTRAINT fk_solicitud_ahorro_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuario(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_solicitud_ahorro_estado FOREIGN KEY (id_estado)
        REFERENCES estado(id_estado)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS datos_sistema (
    id_datos INT NOT NULL AUTO_INCREMENT,
    nombre_director VARCHAR(200) DEFAULT NULL,
    periodo VARCHAR(50) DEFAULT NULL,
    nombre_enc_personal VARCHAR(200) DEFAULT NULL,
    correo_soporte COLLATE utf8mb4_unicode_ci DEFAULT UNIQUE,
    PRIMARY KEY (id_datos)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO datos_sistema (nombre_director, periodo, nombre_enc_personal) VALUES
('Sidney René Toledo Martínez', '2025-2026', 'Teresa de Jesús Hernández Reyes');

CREATE TABLE IF NOT EXISTS auditoria_usuario (
    id_auditoria INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    accion VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    campo_modificado VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    valor_anterior VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    valor_nuevo VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    usuario_responsable VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    fecha_cambio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    user_agent VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (id_auditoria),
    CONSTRAINT fk_auditoria_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuario(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS auditoria_solicitudes (
    id_auditoria INT NOT NULL AUTO_INCREMENT,
    id_solicitud INT DEFAULT NULL,
    tipo_solicitud VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    campo_modificado VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    valor_anterior VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    valor_nuevo VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    usuario_responsable VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    fecha_cambio DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_auditoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DELIMITER $$

CREATE TRIGGER auditar_cambio_rol 
AFTER UPDATE ON usuario 
FOR EACH ROW 
BEGIN
    IF (OLD.id_rol IS NULL AND NEW.id_rol IS NOT NULL) 
        OR (OLD.id_rol <> NEW.id_rol) THEN
            INSERT INTO auditoria_usuario
                (id_usuario, accion, campo_modificado, valor_anterior, valor_nuevo, usuario_responsable)
            VALUES
                (NEW.id_usuario,
                    'UPDATE',
                    'id_rol',
                    CAST(OLD.id_rol AS CHAR),
                    CAST(NEW.id_rol AS CHAR),
                    CURRENT_USER());
    END IF;
END $$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER auditar_eliminacion_usuario 
BEFORE DELETE ON usuario 
FOR EACH ROW 
BEGIN
    INSERT INTO auditoria_usuario
        (id_usuario, accion, campo_modificado, valor_anterior, valor_nuevo, usuario_responsable)
    VALUES
        (
            OLD.id_usuario,
            'DELETE',
            'usuario',
            CONCAT(
                'nombre=', IFNULL(OLD.nombre,''), 
                '; apellido_paterno=', IFNULL(OLD.apellido_paterno,''), 
                '; apellido_materno=', IFNULL(OLD.apellido_materno,''), 
                '; correo_institucional=', IFNULL(OLD.correo_institucional,'')
            ),
            NULL,
            CURRENT_USER()
        );
END $$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER crear_cuenta_ahorro 
AFTER INSERT ON usuario 
FOR EACH ROW 
BEGIN
    INSERT INTO ahorro (monto_ahorrado, id_usuario) 
    VALUES (0.00, NEW.id_usuario);
END $$

DELIMITER ;


DELIMITER $$

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


DELIMITER $$

CREATE TRIGGER actualizar_saldo_ahorro
AFTER INSERT ON movimiento
FOR EACH ROW
BEGIN
    IF NEW.id_tipo_movimiento = 1 THEN
        UPDATE ahorro 
        SET monto_ahorrado = monto_ahorrado + NEW.monto
        WHERE id_usuario = NEW.id_usuario;
    END IF;

    IF NEW.id_tipo_movimiento = 2 THEN
        UPDATE ahorro 
        SET monto_ahorrado = monto_ahorrado - NEW.monto
        WHERE id_usuario = NEW.id_usuario;
    END IF;
END $$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER auditar_solicitud_ahorro
AFTER UPDATE ON solicitud_ahorro
FOR EACH ROW
BEGIN
    IF OLD.id_estado <> NEW.id_estado THEN
        INSERT INTO auditoria_solicitudes
            (id_solicitud, tipo_solicitud, campo_modificado, valor_anterior, valor_nuevo, usuario_responsable)
        VALUES
            (NEW.id_solicitud_ahorro, 'ahorro', 'estado', 
             CAST(OLD.id_estado AS CHAR), CAST(NEW.id_estado AS CHAR), 
             CURRENT_USER());
    END IF;
END $$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER auditar_solicitud_prestamo
AFTER UPDATE ON solicitud_prestamo
FOR EACH ROW
BEGIN
    IF OLD.id_estado <> NEW.id_estado THEN
        INSERT INTO auditoria_solicitudes
            (id_solicitud, tipo_solicitud, campo_modificado, valor_anterior, valor_nuevo, usuario_responsable)
        VALUES
            (NEW.id_solicitud_prestamo, 'prestamo', 'estado', 
             CAST(OLD.id_estado AS CHAR), CAST(NEW.id_estado AS CHAR), 
             CURRENT_USER());
    END IF;
END $$

DELIMITER ;


-- Tabla para tasas de interes globales
CREATE TABLE IF NOT EXISTS tasas_interes (
    id_tasa INT NOT NULL AUTO_INCREMENT,
    tipo VARCHAR(20) NOT NULL, -- 'ahorro' o 'prestamo'
    tasa DECIMAL(5,2) NOT NULL, -- porcentaje
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE DEFAULT NULL, -- NULL = aún vigente
    PRIMARY KEY(id_tasa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para historial 
CREATE TABLE IF NOT EXISTS historial_tasas_usuario (
    id_historial INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    tasa DECIMAL(5,2) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_historial),
    CONSTRAINT fk_historial_usuario FOREIGN KEY(id_usuario)
        REFERENCES usuario(id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

--- Trigger historial de tasa de usuario

DELIMITER $$

CREATE TRIGGER auditar_cambio_tasa
AFTER UPDATE ON usuario
FOR EACH ROW
BEGIN
    IF OLD.tasa_interes <> NEW.tasa_interes THEN
        INSERT INTO historial_tasas_usuario(id_usuario, tasa)
        VALUES (NEW.id_usuario, NEW.tasa_interes);
    END IF;
END $$

DELIMITER ;


