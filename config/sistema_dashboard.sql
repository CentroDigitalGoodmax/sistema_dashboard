-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 22-09-2026 a las 03:34:16
-- Versión del servidor: 8.0.45
-- Versión de PHP: 8.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_dashboard`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_galeria`
--

CREATE TABLE `categoria_galeria` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `icono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-image',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#3B82F6',
  `orden` int DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria_galeria`
--

INSERT INTO `categoria_galeria` (`id`, `nombre`, `slug`, `descripcion`, `icono`, `color`, `orden`, `activo`, `created_at`) VALUES
(13, 'Dr. Ivan Orduz', 'dr-ivan-orduz', 'Acompañamos al Dr. Iván Ordúz durante todo su proceso de inscripción y postulación a los concursos de residencias médicas 2026 en Argentina. Nuestro equipo lo orientó en cada etapa, desde la revisión de requisitos y la preparación de la documentación hasta la gestión de las inscripciones en las diferentes convocatorias.', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-19 22:29:36'),
(21, 'Dr. Andres Gonzales', 'dr-andres-gonzales', 'Acompañamos al Dr. Andrés González durante todo su proceso de inscripción y postulación a los concursos de residencia médica en Argentina', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-20 06:36:15'),
(22, 'Dra. Yessica Guardo', 'dra-yessica-guardo', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-20 06:39:12'),
(23, 'Dr. Dany Ramírez', 'dr-dany-ramrez', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-20 06:42:56'),
(24, 'Dr. Amaury Álvarez', 'dr-amaury-lvarez', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-20 06:45:47'),
(29, 'Dra. Sara Meza', 'dra-sara-meza', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-23 02:29:05'),
(30, 'Dra. Natalia Peralta', 'dra-natalia-peralta', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-23 02:32:00'),
(31, 'Dres. Jesus Navarro y Ana olivero', 'dres-jesus-navarro-y-ana-olivero', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-23 02:34:13'),
(33, 'Dr. Brayan Higuera', 'dr-brayan-higuera', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-23 02:55:08'),
(39, 'Dra. Ana Oliveros', 'dra-ana-oliveros', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-23 04:13:47'),
(40, 'Dra. Carolina Vivero', 'dra-carolina-vivero', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-23 04:15:02'),
(41, 'Dr. Wilson Martinez', 'dr-wilson-martinez', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-23 04:16:15'),
(43, 'Dr. Alejandro Sarrazola', 'dr-alejandro-sarrazola', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-23 04:19:34'),
(44, 'Dr. Orlando Lubo', 'dr-orlando-lubo', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-06-23 04:20:21'),
(45, 'Argen Medical', 'argen-medical', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-08-02 16:35:14'),
(47, 'Dr. José Escalante', 'dr-jos-escalante', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-08-06 06:30:49'),
(48, 'Dras. Angie y Laura', 'dras-angie-y-laura', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-08-06 06:33:13'),
(49, 'Caso de Éxito Migratorio', 'caso-de-xito-migratorio', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-08-06 06:37:41'),
(50, 'Dr. José Capella', 'dr-jos-capella', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-08-06 06:39:31'),
(51, 'Dra. Emilia Arregoces', 'dra-emilia-arregoces', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-08-06 07:00:33'),
(52, 'Dra. Ximena Valbuena', 'dra-ximena-valbuena', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-08-06 07:02:33'),
(53, 'Dres. Javier y Estefanía', 'dres-javier-y-estefana', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-08-06 07:04:34'),
(54, 'Dra. Wendy Pedrozo', 'dra-wendy-pedrozo', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-08-06 07:06:46'),
(57, 'Dra. Paula Cantillo', 'dra-paula-cantillo', '', 'fas fa-folder', '#6366f1', 0, 1, '2026-08-06 07:11:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_podcast`
--

CREATE TABLE `categoria_podcast` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#FF0000',
  `orden` int DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria_podcast`
--

INSERT INTO `categoria_podcast` (`id`, `nombre`, `slug`, `descripcion`, `color`, `orden`, `activo`, `created_at`) VALUES
(12, 'Residencias y Hospitales', 'residencias-y-hospitales', 'Información exclusiva sobre hospitales y residencias médicas para ayudarte a elegir el mejor lugar para tu formación especializada', '#6366f1', 0, 1, '2026-06-19 00:05:29'),
(13, 'Ruta Médica Internacional', 'ruta-mdica-internacional', 'Un espacio dedicado a orientar a médicos y profesionales de la salud en cada etapa de su proyecto internacional: migración, reconocimiento de títulos, matrículas profesionales, residencias y ejercicio laboral', '#6366f1', 0, 1, '2026-06-19 00:05:54'),
(14, 'Historias que Inspiran', 'historias-que-inspiran', 'Historias reales de médicos que transformaron su futuro profesional a través de la migración, el reconocimiento de títulos y el acceso a residencias médicas en el exterior', '#6366f1', 0, 1, '2026-06-19 00:11:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria`
--

CREATE TABLE `galeria` (
  `id` int NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `imagen_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagen_thumbnail` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_id` int DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `visitas` int DEFAULT '0',
  `likes` int DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `destacado` tinyint(1) DEFAULT '0',
  `fecha_publicacion` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `galeria`
--

INSERT INTO `galeria` (`id`, `titulo`, `descripcion`, `imagen_url`, `imagen_thumbnail`, `categoria_id`, `usuario_id`, `visitas`, `likes`, `activo`, `destacado`, `fecha_publicacion`, `created_at`, `updated_at`) VALUES
(20, 'Dr. Ivan Orduz - Inscripción Concursos de Residencia', 'Acompañamos al Dr. Iván Ordúz durante todo su proceso de inscripción y postulación a los concursos de residencias médicas 2026 en Argentina. Nuestro equipo lo orientó en cada etapa, desde la revisión de requisitos y la preparación de la documentación hasta la gestión de las inscripciones en las diferentes convocatorias.', '/assets/galeria/img_6a3536f5d929d.JPG', NULL, 13, NULL, 0, 0, 1, 0, '2026-06-19', '2026-06-19 12:32:53', '2026-06-19 12:32:53'),
(24, 'Dr. Andres Gonzales - Inscripción Concursos de Residencia', 'Acompañamos al Dr. Andrés González durante todo su proceso de inscripción y postulación a los concursos de residencias médicas en Argentina', '/assets/galeria/img_6a35a8b8b728d.jpeg', NULL, 21, NULL, 0, 0, 1, 0, '2026-06-19', '2026-06-19 20:38:16', '2026-06-19 20:38:16'),
(25, 'Dra. Yessica aguardo - Inscripción Concursos de Residencia', 'Acompañamos a la Dra. Yessica Guardo durante todo su proceso de inscripción y postulación a los concursos de residencia en Argentinas', '/assets/galeria/img_6a35a95a54076.jpeg', NULL, 22, NULL, 0, 0, 1, 0, '2026-06-19', '2026-06-19 20:40:58', '2026-06-19 20:40:58'),
(26, 'Dr. Dany Ramírez - Convalidación de Titulo', 'Acompañamos al Dr. Dany Ramírez en su trámite Migratorio y reconocimiento de título en Argentina para aplicar a una especialidad médica.', '/assets/galeria/img_6a35aa1c173a5.jpeg', NULL, 23, NULL, 0, 0, 1, 0, '2026-06-19', '2026-06-19 20:44:12', '2026-06-19 20:44:12'),
(28, 'Dr. Amaury Alvarez - Convalidación de Títutlo', 'Acompañamos al Dr. Amaury en todo su proceso migratorio y de reconocimiento de titulo en Argentina', '/assets/galeria/img_6a36cf2902617.jpg', NULL, 24, NULL, 0, 0, 1, 0, '2026-06-20', '2026-06-20 17:34:33', '2026-06-20 17:34:33'),
(32, 'Dra. Sara Meza - Caso Exitoso', 'Acompañamos a la Dra. Sara Meza en su proceso para Especializarse en Argentina', '/assets/galeria/img_6a39630b36dcb.jpg', NULL, 29, NULL, 0, 0, 1, 0, '2026-06-22', '2026-06-22 16:30:03', '2026-06-22 16:30:03'),
(34, 'Dra. Natalia Peralta - Caso de Exito', 'Acompañamos a la Dra. Natalia Peralta en su proceso para especializarse en Argentina.', '/assets/galeria/img_6a3963b9dfb91.jpg', NULL, 30, NULL, 0, 0, 1, 0, '2026-06-22', '2026-06-22 16:32:57', '2026-06-22 16:32:57'),
(35, 'Argen Medical', 'Acompañamos a los Dres. Jesus y Ana en su proceso para especializarse en Argentina', '/assets/galeria/img_6a39644589ef3.jpg', NULL, 31, NULL, 0, 0, 1, 0, '2026-06-22', '2026-06-22 16:35:17', '2026-06-22 16:35:17'),
(36, 'Dr. Brayan Higuera', 'Acompañamos al Dr. Brayan Higuera en su proceso para especializarse en Argentina', '/assets/galeria/img_6a396a9a5c508.jpeg', NULL, 33, NULL, 0, 0, 1, 0, '2026-06-22', '2026-06-22 17:02:18', '2026-06-22 17:02:18'),
(41, 'Dra. Ana Oliveros - Caso Exitoso', 'Acompañamos a la Dra. Ana Oliveros en su proceso para especializarse en Argentina', '/assets/galeria/img_6a397b94b7f99.jpeg', NULL, 39, NULL, 0, 0, 1, 0, '2026-06-22', '2026-06-22 18:14:44', '2026-06-22 18:14:44'),
(42, 'Dra. Carolina Vivero - Caso Exitoso', 'Acompañamos a la Dra. Carolina Vivero en su proceso para especializarse en Argentina', '/assets/galeria/img_6a397be431298.jpeg', NULL, 40, NULL, 0, 0, 1, 0, '2026-06-22', '2026-06-22 18:16:04', '2026-06-22 18:16:04'),
(43, 'Dr. Wilson Martinez - Caso Exitoso', 'Acompañamos al Dr. Wilson Martínez en su proceso para especializarse en Argentina', '/assets/galeria/img_6a397c1a0da05.jpeg', NULL, 41, NULL, 0, 0, 1, 0, '2026-06-22', '2026-06-22 18:16:58', '2026-06-22 18:16:58'),
(44, 'Dr. Alejandro Sarrazola - Caso Exitoso', 'Acompañamos al Dr. Alejandro Sarrazola en su proceso para especializarse en Argentina', '/assets/galeria/img_6a397cda7c45e.jpeg', NULL, 43, NULL, 0, 0, 1, 0, '2026-06-22', '2026-06-22 18:20:10', '2026-06-22 18:20:10'),
(45, 'Dr. Orlando Lubo - Caso Exitoso', 'Acompañamos al Dr. Orlando Lubo en su proceso para especializarse en Argentina', '/assets/galeria/img_6a397d0a13e92.jpeg', NULL, 44, NULL, 0, 0, 1, 0, '2026-06-22', '2026-06-22 18:20:58', '2026-06-22 18:20:58'),
(49, 'Dr. Jose Escalante', 'Acompañamos al Dr. José Escalante en su proceso para especializarse en Argentina', '/assets/galeria/img_6a739db3407af.jpeg', NULL, 47, NULL, 0, 0, 1, 0, '2026-08-05', '2026-08-05 20:31:47', '2026-08-05 20:31:47'),
(50, 'Dras. Angie y Laura - Caso de Exito', 'Acompañamos a las Dras. Angie y Laura en su proceso Migratorio en Argentina', '/assets/galeria/img_6a739e47589fb.jpeg', NULL, 48, NULL, 0, 0, 1, 0, '2026-08-05', '2026-08-05 20:34:15', '2026-08-05 20:34:15'),
(51, 'Caso de Éxito Migratorio', 'Acompañamos a la Dra. en su proceso Migratorio para Argentina', '/assets/galeria/img_6a739f5279da0.jpeg', NULL, 49, NULL, 0, 0, 1, 0, '2026-08-05', '2026-08-05 20:38:42', '2026-08-05 20:38:42'),
(52, 'Dr. José Capella - Caso Exitoso', 'Acompañamos al Dr. José Capella en su proceso migratorio para Argentina', '/assets/galeria/img_6a739fcbd39d7.jpeg', NULL, 50, NULL, 0, 0, 1, 0, '2026-08-05', '2026-08-05 20:40:43', '2026-08-05 20:40:43'),
(53, 'Dra. Emilia Arregoces - Caso Exitoso', 'Acompañamos a la Dra. Emilia Arregocés en su proceso para especializarse en Argentina', '/assets/galeria/img_6a73a4bc23064.jpeg', NULL, 51, NULL, 0, 0, 1, 0, '2026-08-05', '2026-08-05 21:01:48', '2026-08-05 21:01:48'),
(54, 'Dra. Ximena Valbuena', 'Acompañamos a la Dra. Ximena Valbuena en su proceso para especializarse en Argentina', '/assets/galeria/img_6a73a52a411c7.jpeg', NULL, 52, NULL, 0, 0, 1, 0, '2026-08-05', '2026-08-05 21:03:38', '2026-08-05 21:03:38'),
(55, 'Dres. Javier y Estefanía - Caso de Éxito', 'Acompañamos a los Dres. Javier y Estefanía en su proceso para especializarse en Argentina', '/assets/galeria/img_6a73a5b1474b7.jpeg', NULL, 53, NULL, 0, 0, 1, 0, '2026-08-05', '2026-08-05 21:05:53', '2026-08-05 21:05:53'),
(56, 'Dra. Wendy Pedrozo - Caso de Exito', 'Acompañamos a la Dra. Wendy Pedrozo en su proceso para especializarse en Argentina', '/assets/galeria/img_6a73a634dcb85.jpeg', NULL, 54, NULL, 0, 0, 1, 0, '2026-08-05', '2026-08-05 21:08:04', '2026-08-05 21:08:04'),
(59, 'Dra. Paula Cantillo - Caso de Exito', 'Acompañamos a la Dra. Paula Cantillo en su proceso para especializarse en Argentina', '/assets/galeria/img_6a73a7636dd06.jpeg', NULL, 57, NULL, 0, 0, 1, 0, '2026-08-05', '2026-08-05 21:13:07', '2026-08-05 21:13:07'),
(60, 'Dra. Yessica Guardo - Caso de Exito', 'Acompañamos a la Dra. Yessica Guardo en su proceso para especializarse en Argentina', '/assets/galeria/img_6a73a7b4db99d.jpeg', NULL, 22, NULL, 0, 0, 1, 0, '2026-08-05', '2026-08-05 21:14:28', '2026-08-05 21:14:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instagram`
--

CREATE TABLE `instagram` (
  `id` int NOT NULL,
  `reel` text COLLATE utf8mb4_general_ci NOT NULL,
  `titulo` text COLLATE utf8mb4_general_ci NOT NULL,
  `estado` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `instagram`
--

INSERT INTO `instagram` (`id`, `reel`, `titulo`, `estado`, `created_at`, `updated_at`) VALUES
(2, 'DL5lJOluNAI', 'Testimonio de Carolina Viver', 1, '2026-08-02 05:07:52', '2026-08-04 19:30:30'),
(3, 'DJ16QZSOien', 'Experiencia de homologación', 1, '2026-08-02 05:08:57', '2026-08-02 05:08:57'),
(4, 'DGEEs3hJ_9L', 'Mi vida en Argentina', 1, '2026-08-02 05:09:24', '2026-08-02 05:09:24'),
(5, 'DHrPNOuOGrr', 'Consejos para médicos', 1, '2026-08-02 05:17:21', '2026-08-02 05:17:21'),
(6, 'C940pgxOMec', 'Proceso migratorio exitoso', 1, '2026-08-02 05:17:33', '2026-08-02 05:17:33'),
(7, 'DOgfaQVDW7z', 'Especialización médica', 1, '2026-08-02 05:17:45', '2026-08-02 05:17:45'),
(8, 'DRAzHz-kXjP', 'Testimonio de éxito', 1, '2026-08-02 05:17:58', '2026-08-02 05:17:58'),
(9, 'DQ6p97QjdeB', 'Experiencia Argén Medical', 1, '2026-08-02 05:18:13', '2026-08-02 05:18:13'),
(10, 'DdJrz4mia-H', 'Dra. Adriana Rodriguez', 1, '2026-09-12 05:27:30', '2026-09-12 05:27:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo360`
--

CREATE TABLE `metodo360` (
  `id` int NOT NULL,
  `reel` text COLLATE utf8mb4_general_ci NOT NULL,
  `titulo` text COLLATE utf8mb4_general_ci NOT NULL,
  `estado` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodo360`
--

INSERT INTO `metodo360` (`id`, `reel`, `titulo`, `estado`, `created_at`, `updated_at`) VALUES
(5, 'DHrPNOuOGrr', 'Consejos para médicos', 1, '2026-08-02 05:17:21', '2026-08-04 19:38:10'),
(6, 'C940pgxOMec', 'Proceso migratorio exitoso', 1, '2026-08-02 05:17:33', '2026-08-02 05:17:33'),
(7, 'DOgfaQVDW7z', 'Especialización médica', 1, '2026-08-02 05:17:45', '2026-08-02 05:17:45'),
(8, 'DRAzHz-kXjP', 'Testimonio de éxito', 1, '2026-08-02 05:17:58', '2026-08-02 05:17:58'),
(9, 'DQ6p97QjdeB', 'Experiencia Argén Medical', 1, '2026-08-02 05:18:13', '2026-08-02 05:18:13'),
(10, 'Dad45ahDeA5', 'Testimonio Dr. Dany Ramirez', 1, '2026-08-05 16:22:55', '2026-08-05 16:22:55'),
(11, 'DdJrz4mia-H', 'Dra. Adriana Rodriguez', 1, '2026-09-12 05:26:52', '2026-09-12 05:26:52'),
(12, 'DY8SIcUHBAg', 'Dra. Yessica Guardo', 1, '2026-09-12 05:29:43', '2026-09-12 05:29:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `podcast`
--

CREATE TABLE `podcast` (
  `id` int NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `youtube_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `youtube_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_id` int DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `thumbnail_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitas` int DEFAULT '0',
  `likes` int DEFAULT '0',
  `duracion` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `destacado` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `podcast`
--

INSERT INTO `podcast` (`id`, `titulo`, `descripcion`, `youtube_link`, `youtube_id`, `categoria_id`, `usuario_id`, `thumbnail_url`, `visitas`, `likes`, `duracion`, `fecha_publicacion`, `activo`, `destacado`, `created_at`, `updated_at`) VALUES
(12, 'Residencia de Pediatria en la Clinica del Niño', 'Conoce todo sobre la Residencia de Pediatría en la Clínica del Niño. En este episodio exploramos las vacantes disponibles, el proceso de ingreso, las características del programa de formación y las diferentes rotaciones que realizan los residentes durante su entrenamiento. Además, conocerás de primera mano cómo es la experiencia académica y asistencial en una de las instituciones de referencia para la formación de especialistas en pediatría en Argentina', 'https://www.youtube.com/watch?v=N_aMLn_z3wo&t=29s', 'N_aMLn_z3wo', 12, 1, 'https://img.youtube.com/vi/N_aMLn_z3wo/maxresdefault.jpg', 0, 0, '4:53', '2026-06-18', 1, 1, '2026-06-18 14:13:51', '2026-06-18 14:13:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resoluciones`
--

CREATE TABLE `resoluciones` (
  `id` int NOT NULL,
  `persona` text COLLATE utf8mb4_general_ci NOT NULL,
  `img` text COLLATE utf8mb4_general_ci NOT NULL,
  `estado` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resoluciones`
--

INSERT INTO `resoluciones` (`id`, `persona`, `img`, `estado`, `created_at`, `updated_at`) VALUES
(4, 'Bernardo Campo', '/assets/resoluciones/res_6a71e6ebcae6c.jpeg', 1, '2026-08-04 08:19:39', '2026-08-04 08:19:39'),
(5, 'Dr. Carlos Mejia', '/assets/resoluciones/res_6aa52a21f3d62.png', 1, '2026-09-12 05:32:01', '2026-09-12 05:32:01'),
(6, 'Dr. Julian Pereañez', '/assets/resoluciones/res_6aa52a3db8dcb.png', 1, '2026-09-12 05:32:29', '2026-09-12 05:32:29'),
(7, 'Dr. Carlos Barrera', '/assets/resoluciones/res_6aa52a4fbeb8c.png', 1, '2026-09-12 05:32:47', '2026-09-12 05:32:47'),
(8, 'Dr. Juan Pablo Rodriguez', '/assets/resoluciones/res_6aa52a64c9a21.png', 1, '2026-09-12 05:33:08', '2026-09-12 05:33:08'),
(9, 'Dr. Ismael Malaver', '/assets/resoluciones/res_6aa5a0067f422.png', 1, '2026-09-12 13:55:02', '2026-09-12 13:55:02'),
(10, 'Dra. Wendy Pedrozo', '/assets/resoluciones/res_6aa5a02676bd0.png', 1, '2026-09-12 13:55:34', '2026-09-12 13:55:34'),
(11, 'Dr. Fabian Mora', '/assets/resoluciones/res_6aa5a04091a11.png', 1, '2026-09-12 13:56:00', '2026-09-12 13:56:00'),
(12, 'Dr. Lennin Pineda', '/assets/resoluciones/res_6aa5a0658751e.png', 1, '2026-09-12 13:56:37', '2026-09-12 13:56:37'),
(13, 'Dr. Ronald Santiago', '/assets/resoluciones/res_6aa5a085d010f.png', 1, '2026-09-12 13:57:09', '2026-09-12 13:57:09'),
(14, 'Dr. Andres Avendaño', '/assets/resoluciones/res_6aa5a09f91901.png', 1, '2026-09-12 13:57:35', '2026-09-12 13:57:35'),
(15, 'Dra. Daniela Hernandez', '/assets/resoluciones/res_6aa5a0c121b10.png', 1, '2026-09-12 13:58:09', '2026-09-12 13:58:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `ultimo_acceso` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `avatar`, `activo`, `ultimo_acceso`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@admin.com', 'SGZMbDVIWWdEWExsSWxYZ0thSWFBQT09::gv2-C_167c_x2vwGM4dGkA', '/assets/avatares/avatar_6ab184dea5e25.jpg', 1, '2026-09-21 14:19:56', '2026-06-10 18:39:47', '2026-09-21 19:27:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visados`
--

CREATE TABLE `visados` (
  `id` int NOT NULL,
  `persona` text COLLATE utf8mb4_general_ci NOT NULL,
  `img` text COLLATE utf8mb4_general_ci NOT NULL,
  `estado` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `visados`
--

INSERT INTO `visados` (`id`, `persona`, `img`, `estado`, `created_at`, `updated_at`) VALUES
(4, 'Dr. Cristhian Vargas', '/assets/visados/res_6aa5eac5d9d4c.jpeg', 1, '2026-08-05 00:24:32', '2026-09-12 19:13:57'),
(9, 'Dra. Andrea Beltran', '/assets/visados/res_6aa527771fe6b.jpg', 1, '2026-09-12 05:20:39', '2026-09-12 05:20:39'),
(10, 'Dra. Adriana Rodriguez', '/assets/visados/res_6aa5281601d68.png', 1, '2026-09-12 05:23:18', '2026-09-12 05:23:18'),
(11, 'Dra. Karol Rodriguez', '/assets/visados/res_6aa5356f7e93c.jpeg', 1, '2026-09-12 06:20:15', '2026-09-12 06:20:15'),
(12, 'Dra. Luz Vergara', '/assets/visados/res_6aa53650befc4.jpeg', 1, '2026-09-12 06:24:00', '2026-09-12 06:24:00'),
(13, 'Dr. Ricardo Tete', '/assets/visados/res_6aa53695a9768.jpeg', 1, '2026-09-12 06:25:09', '2026-09-12 06:25:09');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria_galeria`
--
ALTER TABLE `categoria_galeria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indices de la tabla `categoria_podcast`
--
ALTER TABLE `categoria_podcast`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indices de la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_visitas` (`visitas`),
  ADD KEY `idx_destacado` (`destacado`),
  ADD KEY `idx_galeria_visitas` (`visitas`),
  ADD KEY `idx_galeria_fecha` (`created_at`);

--
-- Indices de la tabla `instagram`
--
ALTER TABLE `instagram`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `metodo360`
--
ALTER TABLE `metodo360`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `podcast`
--
ALTER TABLE `podcast`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_visitas` (`visitas`),
  ADD KEY `idx_destacado` (`destacado`),
  ADD KEY `idx_postca_visitas` (`visitas`),
  ADD KEY `idx_postca_fecha` (`created_at`);

--
-- Indices de la tabla `resoluciones`
--
ALTER TABLE `resoluciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indices de la tabla `visados`
--
ALTER TABLE `visados`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria_galeria`
--
ALTER TABLE `categoria_galeria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `categoria_podcast`
--
ALTER TABLE `categoria_podcast`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `galeria`
--
ALTER TABLE `galeria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `instagram`
--
ALTER TABLE `instagram`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `metodo360`
--
ALTER TABLE `metodo360`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `podcast`
--
ALTER TABLE `podcast`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `resoluciones`
--
ALTER TABLE `resoluciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `visados`
--
ALTER TABLE `visados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD CONSTRAINT `galeria_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoria_galeria` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `galeria_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
