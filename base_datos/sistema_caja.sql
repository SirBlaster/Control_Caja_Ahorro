-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 08-12-2025 a las 02:28:21
-- Versión del servidor: 8.0.44
-- Versión de PHP: 8.3.26
CREATE DATABASE IF NOT EXISTS `sistema_caja`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `sistema_caja`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_caja`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ahorro`
--

CREATE TABLE `Ahorro` (
  `Id_Ahorro` int NOT NULL,
  `MontoAhorrado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Id_Ahorrador` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `Ahorro`
--

INSERT INTO `Ahorro` (`Id_Ahorro`, `MontoAhorrado`, `Id_Ahorrador`) VALUES
(1, 0.00, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AuditodiaSolicitudes`
--

CREATE TABLE `AuditodiaSolicitudes` (
  `Id_Auditoria` int NOT NULL,
  `Id_Solicitud` int DEFAULT NULL,
  `Tipo_Solicitud` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CampoModificado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ValorAnterior` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ValorNuevo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `UsuarioResponsable` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FechaCambio` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AuditoriaUsuarios`
--

CREATE TABLE `AuditoriaUsuarios` (
  `Id_Auditoria` int NOT NULL,
  `Id_Ahorrador` int NOT NULL,
  `Accion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CampoModificado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ValorAnterior` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ValorNuevo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `UsuarioResponsable` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FechaCambio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IpAddress` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `UserAgent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `DatosSistema`
--

CREATE TABLE `DatosSistema` (
  `Id_Datos` int NOT NULL,
  `NombreDirector` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Periodo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NombreEnc_Personal` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `DatosSistema`
--

INSERT INTO `DatosSistema` (`Id_Datos`, `NombreDirector`, `Periodo`, `NombreEnc_Personal`) VALUES
(1, 'Sidney René Toledo Martínez', '2025-2026', 'Teresa de Jesús Hernández Reyes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Estado`
--

CREATE TABLE `Estado` (
  `Id_Estado` int NOT NULL,
  `Estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `Estado`
--

INSERT INTO `Estado` (`Id_Estado`, `Estado`) VALUES
(1, 'Pendiente'),
(2, 'Aprobado'),
(3, 'Rechazado'),
(4, 'Pagado'),
(5, 'Cancelado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Movimientos`
--

CREATE TABLE `Movimientos` (
  `Id_Movimiento` int NOT NULL,
  `Fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `Concepto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Monto` decimal(10,2) DEFAULT NULL,
  `Id_Ahorrador` int NOT NULL,
  `Id_TipoMovimiento` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `Movimientos`
--
DELIMITER $$
CREATE TRIGGER `actualizar_deuda_prestamo` AFTER INSERT ON `Movimientos` FOR EACH ROW BEGIN
    IF NEW.Id_TipoMovimiento = 3 THEN
        UPDATE Solicitud_Prestamo
        SET SaldoPendiente = SaldoPendiente - NEW.Monto
        WHERE Id_Ahorrador = NEW.Id_Ahorrador AND Id_Estado = 2;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `actualizar_saldo_ahorro` AFTER INSERT ON `Movimientos` FOR EACH ROW BEGIN
    IF NEW.Id_TipoMovimiento = 1 THEN
        UPDATE Ahorro SET MontoAhorrado = MontoAhorrado + NEW.Monto
        WHERE Id_Ahorrador = NEW.Id_Ahorrador;
    END IF;

    IF NEW.Id_TipoMovimiento = 2 THEN
        UPDATE Ahorro SET MontoAhorrado = MontoAhorrado - NEW.Monto
        WHERE Id_Ahorrador = NEW.Id_Ahorrador;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Rol`
--

CREATE TABLE `Rol` (
  `Id_Rol` int NOT NULL,
  `Rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `Rol`
--

INSERT INTO `Rol` (`Id_Rol`, `Rol`) VALUES
(1, 'Administrador'),
(2, 'Ahorrador'),
(3, 'SuperUsuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Solicitud_Ahorro`
--

CREATE TABLE `Solicitud_Ahorro` (
  `Id_SolicitudAhorro` int NOT NULL,
  `Fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `Monto` decimal(10,2) DEFAULT NULL,
  `Nomina` decimal(10,2) NOT NULL,
  `ArchivoNomina` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ArchivoSolicitud` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Id_Ahorrador` int NOT NULL,
  `Id_Estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `Solicitud_Ahorro`
--
DELIMITER $$
CREATE TRIGGER `auditar_solicitud_ahorro` AFTER UPDATE ON `Solicitud_Ahorro` FOR EACH ROW BEGIN
    IF OLD.Id_Estado <> NEW.Id_Estado THEN
        INSERT INTO AuditodiaSolicitudes (Id_Solicitud, Tipo_Solicitud, CampoModificado, ValorAnterior, ValorNuevo, UsuarioResponsable)
        VALUES (NEW.Id_SolicitudAhorro, 'Ahorro', 'Estado', OLD.Id_Estado, NEW.Id_Estado, CURRENT_USER());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Solicitud_Prestamo`
--

CREATE TABLE `Solicitud_Prestamo` (
  `Id_SolicitudPrestamo` int NOT NULL,
  `FechaSolicitud` datetime DEFAULT CURRENT_TIMESTAMP,
  `MontoSolicitado` decimal(10,2) NOT NULL,
  `Plazo_Quincenas` int NOT NULL,
  `Monto_Pago_Quincenal` decimal(10,2) DEFAULT NULL,
  `Total_A_Pagar` decimal(10,2) NOT NULL,
  `SaldoPendiente` decimal(10,2) NOT NULL,
  `ArchivoPagare` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Id_Ahorrador` int NOT NULL,
  `Id_Estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `Solicitud_Prestamo`
--
DELIMITER $$
CREATE TRIGGER `auditar_solicitud_prestamo` AFTER UPDATE ON `Solicitud_Prestamo` FOR EACH ROW BEGIN
    IF OLD.Id_Estado <> NEW.Id_Estado THEN
        INSERT INTO AuditodiaSolicitudes (Id_Solicitud, Tipo_Solicitud, CampoModificado, ValorAnterior, ValorNuevo, UsuarioResponsable)
        VALUES (NEW.Id_SolicitudPrestamo, 'Prestamo', 'Estado', OLD.Id_Estado, NEW.Id_Estado, CURRENT_USER());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TipoMovimiento`
--

CREATE TABLE `TipoMovimiento` (
  `Id_TipoMovimiento` int NOT NULL,
  `Movimiento` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `TipoMovimiento`
--

INSERT INTO `TipoMovimiento` (`Id_TipoMovimiento`, `Movimiento`) VALUES
(1, 'Depósito'),
(2, 'Retiro'),
(3, 'Pago Préstamo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuarios`
--

CREATE TABLE `Usuarios` (
  `Id_Ahorrador` int NOT NULL,
  `Nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Paterno` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Materno` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Institucional` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Personal` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RFC` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CURP` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Telefono` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Contrasena` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tarjeta` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Id_Rol` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `Usuarios`
--

INSERT INTO `Usuarios` (`Id_Ahorrador`, `Nombre`, `Paterno`, `Materno`, `Institucional`, `Personal`, `RFC`, `CURP`, `Telefono`, `Contrasena`, `Tarjeta`, `Id_Rol`) VALUES
(1, 'Administrador', 'General', 'Sistema', 'admin@itsx.edu.mx', NULL, NULL, NULL, '0000000000', '12345', NULL, 1),
(2, 'Juan', 'Bello', 'Zuñiga', '227O02930@itsx.edu.mx', 'bellozun12@gmail.com', 'BEZJ040831B99', 'BEZJ040831HVZLXNA8', '7841310586', 'contraseña1234', '4217470088983305', 2);

--
-- Disparadores `Usuarios`
--
DELIMITER $$
CREATE TRIGGER `auditar_cambio_rol` AFTER UPDATE ON `Usuarios` FOR EACH ROW BEGIN

  IF OLD.Id_Rol IS NULL AND NEW.Id_Rol IS NOT NULL OR OLD.Id_Rol <> NEW.Id_Rol THEN
    INSERT INTO `AuditoriaUsuarios`
      (`Id_Ahorrador`, `Accion`, `CampoModificado`, `ValorAnterior`, `ValorNuevo`, `UsuarioResponsable`)
    VALUES
      (NEW.Id_Ahorrador,
       'UPDATE',
       'Id_Rol',
       CAST(OLD.Id_Rol AS CHAR),
       CAST(NEW.Id_Rol AS CHAR),
       CURRENT_USER());
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_eliminacion_usuario` BEFORE DELETE ON `Usuarios` FOR EACH ROW BEGIN
  INSERT INTO `AuditoriaUsuarios`
    (`Id_Ahorrador`, `Accion`, `CampoModificado`, `ValorAnterior`, `ValorNuevo`, `UsuarioResponsable`)
  VALUES
    (
      OLD.Id_Ahorrador,
      'DELETE',
      'Usuario',
      CONCAT('Nombre=', IFNULL(OLD.Nombre,''), '; Paterno=', IFNULL(OLD.Paterno,''), '; Materno=', IFNULL(OLD.Materno,''), '; Institucional=', IFNULL(OLD.Institucional,'')),
      NULL,
      CURRENT_USER()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `crear_cuenta_ahorro` AFTER INSERT ON `Usuarios` FOR EACH ROW BEGIN
    INSERT INTO Ahorro (MontoAhorrado, Id_Ahorrador) VALUES (0.00, NEW.Id_Ahorrador);
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Ahorro`
--
ALTER TABLE `Ahorro`
  ADD PRIMARY KEY (`Id_Ahorro`),
  ADD KEY `fk_ahorro_usuario` (`Id_Ahorrador`);

--
-- Indices de la tabla `AuditodiaSolicitudes`
--
ALTER TABLE `AuditodiaSolicitudes`
  ADD PRIMARY KEY (`Id_Auditoria`);

--
-- Indices de la tabla `AuditoriaUsuarios`
--
ALTER TABLE `AuditoriaUsuarios`
  ADD PRIMARY KEY (`Id_Auditoria`),
  ADD KEY `idx_audit_user` (`Id_Ahorrador`),
  ADD KEY `idx_fecha_audit` (`FechaCambio`);

--
-- Indices de la tabla `DatosSistema`
--
ALTER TABLE `DatosSistema`
  ADD PRIMARY KEY (`Id_Datos`);

--
-- Indices de la tabla `Estado`
--
ALTER TABLE `Estado`
  ADD PRIMARY KEY (`Id_Estado`);

--
-- Indices de la tabla `Movimientos`
--
ALTER TABLE `Movimientos`
  ADD PRIMARY KEY (`Id_Movimiento`),
  ADD KEY `fk_movimiento_tipo` (`Id_TipoMovimiento`),
  ADD KEY `idx_movimientos_ahorrador` (`Id_Ahorrador`);

--
-- Indices de la tabla `Rol`
--
ALTER TABLE `Rol`
  ADD PRIMARY KEY (`Id_Rol`);

--
-- Indices de la tabla `Solicitud_Ahorro`
--
ALTER TABLE `Solicitud_Ahorro`
  ADD PRIMARY KEY (`Id_SolicitudAhorro`),
  ADD KEY `fk_sol_ahorro_estado` (`Id_Estado`),
  ADD KEY `idx_solicitud_ahorrador` (`Id_Ahorrador`);

--
-- Indices de la tabla `Solicitud_Prestamo`
--
ALTER TABLE `Solicitud_Prestamo`
  ADD PRIMARY KEY (`Id_SolicitudPrestamo`),
  ADD KEY `fk_prestamo_estado` (`Id_Estado`),
  ADD KEY `idx_solicitudp_ahorrador` (`Id_Ahorrador`);

--
-- Indices de la tabla `TipoMovimiento`
--
ALTER TABLE `TipoMovimiento`
  ADD PRIMARY KEY (`Id_TipoMovimiento`);

--
-- Indices de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  ADD PRIMARY KEY (`Id_Ahorrador`),
  ADD KEY `fk_usuario_rol` (`Id_Rol`),
  ADD KEY `idx_usuarios_institucional` (`Institucional`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Ahorro`
--
ALTER TABLE `Ahorro`
  MODIFY `Id_Ahorro` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `AuditodiaSolicitudes`
--
ALTER TABLE `AuditodiaSolicitudes`
  MODIFY `Id_Auditoria` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `AuditoriaUsuarios`
--
ALTER TABLE `AuditoriaUsuarios`
  MODIFY `Id_Auditoria` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `DatosSistema`
--
ALTER TABLE `DatosSistema`
  MODIFY `Id_Datos` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `Estado`
--
ALTER TABLE `Estado`
  MODIFY `Id_Estado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `Movimientos`
--
ALTER TABLE `Movimientos`
  MODIFY `Id_Movimiento` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Rol`
--
ALTER TABLE `Rol`
  MODIFY `Id_Rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `Solicitud_Ahorro`
--
ALTER TABLE `Solicitud_Ahorro`
  MODIFY `Id_SolicitudAhorro` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Solicitud_Prestamo`
--
ALTER TABLE `Solicitud_Prestamo`
  MODIFY `Id_SolicitudPrestamo` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `TipoMovimiento`
--
ALTER TABLE `TipoMovimiento`
  MODIFY `Id_TipoMovimiento` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  MODIFY `Id_Ahorrador` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Ahorro`
--
ALTER TABLE `Ahorro`
  ADD CONSTRAINT `fk_ahorro_usuario` FOREIGN KEY (`Id_Ahorrador`) REFERENCES `Usuarios` (`Id_Ahorrador`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Movimientos`
--
ALTER TABLE `Movimientos`
  ADD CONSTRAINT `fk_movimiento_tipo` FOREIGN KEY (`Id_TipoMovimiento`) REFERENCES `TipoMovimiento` (`Id_TipoMovimiento`),
  ADD CONSTRAINT `fk_movimiento_usuario` FOREIGN KEY (`Id_Ahorrador`) REFERENCES `Usuarios` (`Id_Ahorrador`);

--
-- Filtros para la tabla `Solicitud_Ahorro`
--
ALTER TABLE `Solicitud_Ahorro`
  ADD CONSTRAINT `fk_sol_ahorro_estado` FOREIGN KEY (`Id_Estado`) REFERENCES `Estado` (`Id_Estado`),
  ADD CONSTRAINT `fk_sol_ahorro_usuario` FOREIGN KEY (`Id_Ahorrador`) REFERENCES `Usuarios` (`Id_Ahorrador`);

--
-- Filtros para la tabla `Solicitud_Prestamo`
--
ALTER TABLE `Solicitud_Prestamo`
  ADD CONSTRAINT `fk_prestamo_estado` FOREIGN KEY (`Id_Estado`) REFERENCES `Estado` (`Id_Estado`),
  ADD CONSTRAINT `fk_prestamo_usuario` FOREIGN KEY (`Id_Ahorrador`) REFERENCES `Usuarios` (`Id_Ahorrador`);

--
-- Filtros para la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`Id_Rol`) REFERENCES `Rol` (`Id_Rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
