<?php

declare(strict_types=1);

date_default_timezone_set('America/Guayaquil');

use Ordenalo\Repositories\MovimientoRepositorioPdo;
use Ordenalo\Services\MovimientoService;
use Ordenalo\Services\ResumenService;
use Ordenalo\Validation\ReglaCategoria;
use Ordenalo\Validation\ReglaDescripcion;
use Ordenalo\Validation\ReglaFecha;
use Ordenalo\Validation\ReglaMonto;
use Ordenalo\Validation\ReglaTipo;

$pdo = require __DIR__ . '/conexion.php';
$repositorio = new MovimientoRepositorioPdo($pdo);

return [
    'movimientos' => new MovimientoService($repositorio, [
        new ReglaTipo(),
        new ReglaCategoria(),
        new ReglaMonto(),
        new ReglaDescripcion(),
        new ReglaFecha(),
    ]),
    'resumen' => new ResumenService($repositorio, require __DIR__ . '/limites.php'),
];