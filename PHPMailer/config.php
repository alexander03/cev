<?php
// Carga vendor/ y las variables del .env del proyecto para los scripts de notificacion.
// Las credenciales (SMTP y base de datos) se leen con env(), nunca se escriben en el codigo.
require_once __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
