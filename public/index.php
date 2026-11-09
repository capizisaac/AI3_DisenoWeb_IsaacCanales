<?php

declare(strict_types=1);

use Ordenalo\Views\Vista;

require_once __DIR__ . '/../vendor/autoload.php';

$vista = new Vista(__DIR__ . '/../src/Views/plantillas');

echo $vista->renderizar('inicio', [], 'Inicio');