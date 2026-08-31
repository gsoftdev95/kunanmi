<?php

// Ruta al archivo .env
$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    throw new Exception('No se encontró el archivo .env');
}

// Leer el archivo .env
$env = parse_ini_file($envPath);

if ($env === false) {
    throw new Exception('No se pudo leer el archivo .env');
}

// Obtener las claves de Culqi
$publicKey = $env['CULQI_PUBLIC_KEY'] ?? '';
$secretKey = $env['CULQI_SECRET_KEY'] ?? '';

// Validar que existan
if (empty($publicKey) || empty($secretKey)) {
    throw new Exception('Las claves de Culqi no están configuradas en el archivo .env');
}

// Devolver configuración
return [
    'public_key' => $publicKey,
    'secret_key' => $secretKey
];