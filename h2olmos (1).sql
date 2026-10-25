-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-07-2020 a las 22:24:55
-- Versión del servidor: 10.1.13-MariaDB
-- Versión de PHP: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `h2olmos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso`
--

CREATE TABLE `acceso` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipousuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `opcionmenu_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area`
--

CREATE TABLE `area` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `area`
--

INSERT INTO `area` (`id`, `descripcion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'GERENCIA', '2020-07-13 23:20:53', '2020-07-13 23:20:53', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conductor`
--

CREATE TABLE `conductor` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `licencia` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechavencimiento` date NOT NULL,
  `contratista_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratista`
--

CREATE TABLE `contratista` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ruc` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razonsocial` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

CREATE TABLE `equipo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(22) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `placa` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motor` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asientos` int(11) DEFAULT NULL,
  `marca_id` bigint(20) UNSIGNED NOT NULL,
  `contratista_id` bigint(20) UNSIGNED NOT NULL,
  `anio` year(4) NOT NULL,
  `ua_id` bigint(20) UNSIGNED DEFAULT NULL,
  `area_id` bigint(20) UNSIGNED DEFAULT NULL,
  `chasis` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carroceria` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechavencimientosoat` date DEFAULT NULL,
  `fechavencimientogps` date DEFAULT NULL,
  `fechavencimientortv` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grifo`
--

CREATE TABLE `grifo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupomenu`
--

CREATE TABLE `grupomenu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icono` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grupomenu`
--

INSERT INTO `grupomenu` (`id`, `descripcion`, `icono`, `orden`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SISTEMA', 'settings_power', 2, '2020-06-23 05:00:00', '2020-07-13 21:52:15', NULL),
(2, 'MANTENIMIENTOS', 'content_paste', 1, '2020-07-13 21:52:10', '2020-07-13 21:54:17', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `marca`
--

INSERT INTO `marca` (`id`, `descripcion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'MERCEDES BENZ', '2020-07-13 23:21:55', '2020-07-13 23:21:55', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2020_06_23_050528_create_grupomenu', 2),
(5, '2020_06_23_050619_create_opcionmenu_table', 3),
(6, '2020_06_23_050720_create_tipousuario_table', 3),
(7, '2020_06_23_050755_create_acceso_table', 3),
(8, '2020_06_23_093425_create_marca_table', 4),
(9, '2020_06_23_234823_create_propietarios_table', 4),
(10, '2020_06_24_004558_create_repuestos_table', 4),
(11, '2020_06_24_005941_create_ua_table', 4),
(12, '2020_06_24_011622_create_unidades_table', 4),
(13, '2020_06_24_052844_create_tipohora_table', 4),
(14, '2020_06_24_053409_create_grifo_table', 4),
(15, '2020_06_24_172958_update_addfk_ua_propietarios', 4),
(16, '2020_06_24_173728_update_addfk_unidad_ua', 4),
(17, '2020_06_24_201109_create_contratistas_table', 4),
(18, '2020_06_24_201230_create_conductors_table', 4),
(19, '2020_06_25_011805_create_equipo_table', 4),
(20, '2020_06_26_000010_create_area_table', 4),
(21, '2020_06_26_000112_create_trabajo_table', 4),
(22, '2020_06_27_002323_edit_equipo_foreing', 4),
(23, '2020_06_29_040921_addfk_repuestos_unidad', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opcionmenu`
--

CREATE TABLE `opcionmenu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icono` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL,
  `grupomenu_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `opcionmenu`
--

INSERT INTO `opcionmenu` (`id`, `descripcion`, `link`, `icono`, `orden`, `grupomenu_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'GRUPO MENU', 'grupomenu', 'vertical_split', 1, 1, '2020-06-23 05:00:00', '2020-06-23 13:15:17', NULL),
(2, 'OPCION MENU', 'opcionmenu', 'language', 2, 1, '2020-06-23 05:00:00', '2020-06-23 13:10:29', NULL),
(3, 'USUARIO', 'usuario', 'person', 3, 1, '2020-06-23 13:17:10', '2020-06-23 13:18:19', NULL),
(4, 'UA', 'ua', 'api', 1, 2, '2020-07-13 21:53:13', '2020-07-13 21:53:13', NULL),
(5, 'PROPIETARIOS', 'propietario', 'accessibility', 2, 2, '2020-07-13 22:02:37', '2020-07-13 22:02:37', NULL),
(6, 'UNIDAD', 'unidad', 'ac_unit', 3, 2, '2020-07-13 22:03:06', '2020-07-13 22:03:06', NULL),
(7, 'AREA', 'areas', 'info', 3, 2, '2020-07-13 22:16:28', '2020-07-13 22:17:52', NULL),
(8, 'CONDUCTORES', 'conductores', 'face', 4, 2, '2020-07-13 22:17:14', '2020-07-13 22:18:55', NULL),
(9, 'TIPO HORA', 'tipohora', 'hourglass_empty', 5, 2, '2020-07-13 22:20:59', '2020-07-13 22:20:59', NULL),
(10, 'GRIFO', 'grifo', 'toc', 6, 2, '2020-07-13 22:21:58', '2020-07-13 22:21:58', NULL),
(11, 'EQUIPO', 'equipo', 'directions_car', 7, 2, '2020-07-13 22:23:10', '2020-07-13 22:23:10', NULL),
(12, 'MARCAS', 'marcas', 'model_training', 8, 2, '2020-07-13 22:23:42', '2020-07-13 22:23:42', NULL),
(13, 'REPUESTOS', 'repuestos', 'settings', 8, 2, '2020-07-13 22:24:51', '2020-07-13 22:24:51', NULL),
(14, 'CONTRATISTAS', 'contratistas', 'perm_identity', 9, 2, '2020-07-13 22:25:44', '2020-07-13 22:25:44', NULL),
(15, 'TRABAJOS', 'trabajos', 'play_for_work', 10, 2, '2020-07-13 22:26:14', '2020-07-13 22:26:14', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios`
--

CREATE TABLE `propietarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_llegada` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `fecha_contrato` date NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hra` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hrb` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hrc` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `km` int(11) NOT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ubicacion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ua_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repuesto`
--

CREATE TABLE `repuesto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidad_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipohora`
--

CREATE TABLE `tipohora` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipousuario`
--

CREATE TABLE `tipousuario` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajo`
--

CREATE TABLE `trabajo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ua`
--

CREATE TABLE `ua` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` bigint(20) NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fondos` tinyint(1) NOT NULL,
  `responsable` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_costo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidad_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad`
--

CREATE TABLE `unidad` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Admin', 'admin', '2020-06-23 09:51:40', '$2y$10$rf/fT3jlhk3yS1z5VlAdTOgdxOums/i7.PVjBYechFQWI5dYNE9Yi', NULL, '2020-06-23 09:51:40', '2020-06-23 09:51:40');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acceso`
--
ALTER TABLE `acceso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acceso_tipousuario_id_foreign` (`tipousuario_id`),
  ADD KEY `acceso_opcionmenu_id_foreign` (`opcionmenu_id`);

--
-- Indices de la tabla `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `conductor`
--
ALTER TABLE `conductor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `conductor_dni_unique` (`dni`),
  ADD UNIQUE KEY `conductor_licencia_unique` (`licencia`),
  ADD KEY `conductor_contratista_id_foreign` (`contratista_id`);

--
-- Indices de la tabla `contratista`
--
ALTER TABLE `contratista`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contratista_ruc_unique` (`ruc`);

--
-- Indices de la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipo_marca_id_foreign` (`marca_id`),
  ADD KEY `equipo_contratista_id_foreign` (`contratista_id`),
  ADD KEY `equipo_area_id_foreign` (`area_id`),
  ADD KEY `equipo_ua_id_foreign` (`ua_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grifo`
--
ALTER TABLE `grifo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grupomenu`
--
ALTER TABLE `grupomenu`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `marca_descripcion_unique` (`descripcion`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opcionmenu`
--
ALTER TABLE `opcionmenu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opcionmenu_grupomenu_id_foreign` (`grupomenu_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propietarios_ua_id_foreign` (`ua_id`);

--
-- Indices de la tabla `repuesto`
--
ALTER TABLE `repuesto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `repuesto_codigo_unique` (`codigo`),
  ADD UNIQUE KEY `repuesto_descripcion_unique` (`descripcion`),
  ADD KEY `repuesto_unidad_id_foreign` (`unidad_id`);

--
-- Indices de la tabla `tipohora`
--
ALTER TABLE `tipohora`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `trabajo`
--
ALTER TABLE `trabajo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ua`
--
ALTER TABLE `ua`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ua_codigo_unique` (`codigo`),
  ADD KEY `ua_unidad_id_foreign` (`unidad_id`);

--
-- Indices de la tabla `unidad`
--
ALTER TABLE `unidad`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acceso`
--
ALTER TABLE `acceso`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `area`
--
ALTER TABLE `area`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `conductor`
--
ALTER TABLE `conductor`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contratista`
--
ALTER TABLE `contratista`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `equipo`
--
ALTER TABLE `equipo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grifo`
--
ALTER TABLE `grifo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupomenu`
--
ALTER TABLE `grupomenu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `opcionmenu`
--
ALTER TABLE `opcionmenu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `repuesto`
--
ALTER TABLE `repuesto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipohora`
--
ALTER TABLE `tipohora`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trabajo`
--
ALTER TABLE `trabajo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ua`
--
ALTER TABLE `ua`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidad`
--
ALTER TABLE `unidad`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acceso`
--
ALTER TABLE `acceso`
  ADD CONSTRAINT `acceso_opcionmenu_id_foreign` FOREIGN KEY (`opcionmenu_id`) REFERENCES `opcionmenu` (`id`),
  ADD CONSTRAINT `acceso_tipousuario_id_foreign` FOREIGN KEY (`tipousuario_id`) REFERENCES `tipousuario` (`id`);

--
-- Filtros para la tabla `conductor`
--
ALTER TABLE `conductor`
  ADD CONSTRAINT `conductor_contratista_id_foreign` FOREIGN KEY (`contratista_id`) REFERENCES `contratista` (`id`);

--
-- Filtros para la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD CONSTRAINT `equipo_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`),
  ADD CONSTRAINT `equipo_contratista_id_foreign` FOREIGN KEY (`contratista_id`) REFERENCES `contratista` (`id`),
  ADD CONSTRAINT `equipo_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marca` (`id`),
  ADD CONSTRAINT `equipo_ua_id_foreign` FOREIGN KEY (`ua_id`) REFERENCES `ua` (`id`);

--
-- Filtros para la tabla `opcionmenu`
--
ALTER TABLE `opcionmenu`
  ADD CONSTRAINT `opcionmenu_grupomenu_id_foreign` FOREIGN KEY (`grupomenu_id`) REFERENCES `grupomenu` (`id`);

--
-- Filtros para la tabla `propietarios`
--
ALTER TABLE `propietarios`
  ADD CONSTRAINT `propietarios_ua_id_foreign` FOREIGN KEY (`ua_id`) REFERENCES `ua` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `repuesto`
--
ALTER TABLE `repuesto`
  ADD CONSTRAINT `repuesto_unidad_id_foreign` FOREIGN KEY (`unidad_id`) REFERENCES `unidad` (`id`);

--
-- Filtros para la tabla `ua`
--
ALTER TABLE `ua`
  ADD CONSTRAINT `ua_unidad_id_foreign` FOREIGN KEY (`unidad_id`) REFERENCES `unidad` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
