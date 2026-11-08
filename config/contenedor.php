<?php

declare(strict_types=1);

use Ordenalo\Repositories\MovimientoRepositorioPdo;
use Ordenalo\Services\MovimientoService;
use Ordenalo\Validation\ReglaCategoria;
use Ordenalo\Validation\ReglaDescripcion;
use Ordenalo\Validation\ReglaFecha;
use Ordenalo\Validation\ReglaMonto;
use Ordenalo\Validation\ReglaTipo;

$pdo = require __DIR__ . '/conexion.php';

return new MovimientoService(
    new MovimientoRepositorioPdo($pdo),
    [
        new ReglaTipo(),
        new ReglaCategoria(),
        new ReglaMonto(),
        new ReglaDescripcion(),
        new ReglaFecha(),
    ],
);