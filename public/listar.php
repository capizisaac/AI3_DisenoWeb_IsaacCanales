<?php

declare(strict_types=1);

use Ordenalo\Views\Vista;

$servicios = require __DIR__ . '/../config/contenedor.php';
$vista = new Vista(__DIR__ . '/../src/Views/plantillas');

try {
    $movimientos = $servicios['movimientos']->listar();
    $resumen = $servicios['resumen']->delMes(new DateTimeImmutable('now'));
} catch (PDOException $e) {
    error_log('Error al consultar los movimientos: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo consultar la información. Inténtalo más tarde.');
}

echo $vista->renderizar(
    'consulta',
    [
        'movimientos' => $movimientos,
        'resumen' => $resumen,
        'guardado' => ($_GET['guardado'] ?? '') === '1',
    ],
    'Movimientos'
);