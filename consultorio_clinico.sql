-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-08-2025 a las 16:10:30
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `consultorio_clinico`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `antecedente`
--

CREATE TABLE `antecedente` (
  `antecedente_id` int(11) NOT NULL,
  `consulta_id` int(11) NOT NULL,
  `tipo` enum('personal','quirurgico','familiar','alergia','otro') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `creado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cita`
--

CREATE TABLE `cita` (
  `cita_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `estado` enum('confirmada','cancelada','pendiente') NOT NULL DEFAULT 'pendiente',
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `creado_por` int(11) DEFAULT NULL,
  `modificado_en` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consulta`
--

CREATE TABLE `consulta` (
  `consulta_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `fecha_consulta` datetime NOT NULL,
  `motivo` text DEFAULT NULL,
  `historia_actual` text DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `creado_por` int(11) DEFAULT NULL,
  `modificado_en` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `consulta`
--

INSERT INTO `consulta` (`consulta_id`, `paciente_id`, `fecha_consulta`, `motivo`, `historia_actual`, `creado_en`, `creado_por`, `modificado_en`, `modificado_por`) VALUES
(1, 1, '2025-05-15 10:12:44', 'Dolor de cabeza', 'Paciente refiere cefalea desde hace 3 días', '2025-05-15 10:12:44', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consulta_archivo`
--

CREATE TABLE `consulta_archivo` (
  `archivo_id` int(11) NOT NULL,
  `consulta_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `fecha_consulta` datetime NOT NULL,
  `motivo` text DEFAULT NULL,
  `historia_actual` text DEFAULT NULL,
  `creado_en` datetime NOT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `modificado_en` datetime DEFAULT NULL,
  `modificado_por` int(11) DEFAULT NULL,
  `fecha_archivado` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diagnostico`
--

CREATE TABLE `diagnostico` (
  `diagnostico_id` int(11) NOT NULL,
  `consulta_id` int(11) NOT NULL,
  `codigo_icd10` varchar(10) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `creado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `diagnostico`
--

INSERT INTO `diagnostico` (`diagnostico_id`, `consulta_id`, `codigo_icd10`, `descripcion`, `creado_en`, `creado_por`) VALUES
(1, 1, 'G43.9', 'Migraña, no especificada', '2025-05-15 10:12:44', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `examenfisico`
--

CREATE TABLE `examenfisico` (
  `examen_id` int(11) NOT NULL,
  `consulta_id` int(11) NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `signos_vitales` text DEFAULT NULL,
  `hallazgos` text DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `creado_por` int(11) DEFAULT NULL,
  `imc` decimal(5,2) GENERATED ALWAYS AS (`peso` / (`talla` * `talla`)) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `examenfisico`
--

INSERT INTO `examenfisico` (`examen_id`, `consulta_id`, `peso`, `talla`, `signos_vitales`, `hallazgos`, `creado_en`, `creado_por`) VALUES
(1, 1, 70.50, 1.75, '{\"temperatura\": 36.5, \"presion_arterial\": \"120/80\", \"frecuencia_cardiaca\": 72}', 'Sin hallazgos relevantes', '2025-05-15 10:12:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamento`
--

CREATE TABLE `medicamento` (
  `medicamento_id` int(11) NOT NULL,
  `diagnostico_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `dosis` varchar(50) DEFAULT NULL,
  `frecuencia` varchar(50) DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `creado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicamento`
--

INSERT INTO `medicamento` (`medicamento_id`, `diagnostico_id`, `nombre`, `dosis`, `frecuencia`, `creado_en`, `creado_por`) VALUES
(1, 1, 'Paracetamol', '500mg', 'Cada 8 horas por 3 días', '2025-05-15 10:12:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paciente`
--

CREATE TABLE `paciente` (
  `paciente_id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `domicilio` text DEFAULT NULL,
  `ocupacion` varchar(100) DEFAULT NULL,
  `escolaridad` varchar(100) DEFAULT NULL,
  `nombre_responsable` varchar(100) DEFAULT NULL,
  `telefono_responsable` varchar(20) DEFAULT NULL,
  `anonim` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `creado_por` int(11) DEFAULT NULL,
  `modificado_en` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL,
  `identificacion` varchar(30) DEFAULT NULL,
  `edad` int(11) GENERATED ALWAYS AS (timestampdiff(YEAR,`fecha_nacimiento`,curdate())) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paciente`
--

INSERT INTO `paciente` (`paciente_id`, `nombre`, `fecha_nacimiento`, `telefono`, `domicilio`, `ocupacion`, `escolaridad`, `nombre_responsable`, `telefono_responsable`, `anonim`, `creado_en`, `creado_por`, `modificado_en`, `modificado_por`, `identificacion`) VALUES
(1, 'Paciente Prueba Actualizado', '1980-01-15', '555-123-4567', 'Av. Principal #123', NULL, NULL, NULL, NULL, 0, '2025-05-15 10:12:44', NULL, '2025-05-15 10:12:45', NULL, 'ID12345'),
(2, 'Paciente Referencia', '1990-05-20', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-05-15 10:12:45', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `usuario_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` char(60) NOT NULL,
  `rol` enum('admin','medico','enfermero') NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `creado_por` int(11) DEFAULT NULL,
  `modificado_en` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `username`, `password_hash`, `rol`, `creado_en`, `creado_por`, `modificado_en`, `modificado_por`) VALUES
(3, 'admin', 'admin123', 'admin', '2025-05-21 16:32:53', NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `antecedente`
--
ALTER TABLE `antecedente`
  ADD PRIMARY KEY (`antecedente_id`),
  ADD KEY `fk_antecedente_consulta` (`consulta_id`),
  ADD KEY `fk_antecedente_creado_por` (`creado_por`);

--
-- Indices de la tabla `cita`
--
ALTER TABLE `cita`
  ADD PRIMARY KEY (`cita_id`),
  ADD KEY `fk_cita_paciente` (`paciente_id`),
  ADD KEY `fk_cita_usuario` (`usuario_id`),
  ADD KEY `fk_cita_creado_por` (`creado_por`),
  ADD KEY `fk_cita_modificado_por` (`modificado_por`);

--
-- Indices de la tabla `consulta`
--
ALTER TABLE `consulta`
  ADD PRIMARY KEY (`consulta_id`),
  ADD KEY `fk_consulta_paciente` (`paciente_id`),
  ADD KEY `fk_consulta_creado_por` (`creado_por`),
  ADD KEY `fk_consulta_modificado_por` (`modificado_por`);

--
-- Indices de la tabla `consulta_archivo`
--
ALTER TABLE `consulta_archivo`
  ADD PRIMARY KEY (`archivo_id`),
  ADD KEY `fk_archivo_consulta` (`consulta_id`),
  ADD KEY `fk_archivo_paciente` (`paciente_id`),
  ADD KEY `fk_archivo_creado_por` (`creado_por`),
  ADD KEY `fk_archivo_modificado_por` (`modificado_por`);

--
-- Indices de la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD PRIMARY KEY (`diagnostico_id`),
  ADD KEY `fk_diagnostico_consulta` (`consulta_id`),
  ADD KEY `fk_diagnostico_creado_por` (`creado_por`),
  ADD KEY `idx_codigo_icd10` (`codigo_icd10`);

--
-- Indices de la tabla `examenfisico`
--
ALTER TABLE `examenfisico`
  ADD PRIMARY KEY (`examen_id`),
  ADD KEY `fk_examen_consulta` (`consulta_id`),
  ADD KEY `fk_examen_creado_por` (`creado_por`);

--
-- Indices de la tabla `medicamento`
--
ALTER TABLE `medicamento`
  ADD PRIMARY KEY (`medicamento_id`),
  ADD KEY `fk_medicamento_diagnostico` (`diagnostico_id`),
  ADD KEY `fk_medicamento_creado_por` (`creado_por`);

--
-- Indices de la tabla `paciente`
--
ALTER TABLE `paciente`
  ADD PRIMARY KEY (`paciente_id`),
  ADD KEY `fk_paciente_creado_por` (`creado_por`),
  ADD KEY `fk_paciente_modificado_por` (`modificado_por`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_identificacion` (`identificacion`),
  ADD KEY `idx_fecha_nacimiento` (`fecha_nacimiento`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_usuario_creado_por` (`creado_por`),
  ADD KEY `fk_usuario_modificado_por` (`modificado_por`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `antecedente`
--
ALTER TABLE `antecedente`
  MODIFY `antecedente_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cita`
--
ALTER TABLE `cita`
  MODIFY `cita_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `consulta`
--
ALTER TABLE `consulta`
  MODIFY `consulta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `consulta_archivo`
--
ALTER TABLE `consulta_archivo`
  MODIFY `archivo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  MODIFY `diagnostico_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `examenfisico`
--
ALTER TABLE `examenfisico`
  MODIFY `examen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `medicamento`
--
ALTER TABLE `medicamento`
  MODIFY `medicamento_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `paciente`
--
ALTER TABLE `paciente`
  MODIFY `paciente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `antecedente`
--
ALTER TABLE `antecedente`
  ADD CONSTRAINT `fk_antecedente_consulta` FOREIGN KEY (`consulta_id`) REFERENCES `consulta` (`consulta_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_antecedente_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `cita`
--
ALTER TABLE `cita`
  ADD CONSTRAINT `fk_cita_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cita_modificado_por` FOREIGN KEY (`modificado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cita_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`paciente_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cita_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `consulta`
--
ALTER TABLE `consulta`
  ADD CONSTRAINT `fk_consulta_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consulta_modificado_por` FOREIGN KEY (`modificado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consulta_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`paciente_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `consulta_archivo`
--
ALTER TABLE `consulta_archivo`
  ADD CONSTRAINT `fk_archivo_consulta` FOREIGN KEY (`consulta_id`) REFERENCES `consulta` (`consulta_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_archivo_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_archivo_modificado_por` FOREIGN KEY (`modificado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_archivo_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`paciente_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD CONSTRAINT `fk_diagnostico_consulta` FOREIGN KEY (`consulta_id`) REFERENCES `consulta` (`consulta_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_diagnostico_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `examenfisico`
--
ALTER TABLE `examenfisico`
  ADD CONSTRAINT `fk_examen_consulta` FOREIGN KEY (`consulta_id`) REFERENCES `consulta` (`consulta_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_examen_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `medicamento`
--
ALTER TABLE `medicamento`
  ADD CONSTRAINT `fk_medicamento_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_medicamento_diagnostico` FOREIGN KEY (`diagnostico_id`) REFERENCES `diagnostico` (`diagnostico_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `paciente`
--
ALTER TABLE `paciente`
  ADD CONSTRAINT `fk_paciente_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_paciente_modificado_por` FOREIGN KEY (`modificado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario_modificado_por` FOREIGN KEY (`modificado_por`) REFERENCES `usuario` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
