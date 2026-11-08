<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Dotenv\Exception\ExceptionInterface as DotenvException;
use Ordenalo\Database\ConexionFactory;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHARSET'])->notEmpty();

    $fabrica = new ConexionFactory(
        $_ENV['DB_HOST'],
        $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        $_ENV['DB_CHARSET']
    );

    return $fabrica->crear();
} catch (PDOException | DotenvException $e) {
    error_log('Fallo de conexión a la base de datos: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo conectar con el servicio de datos. Inténtalo más tarde.');
}