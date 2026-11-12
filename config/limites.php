<?php

declare(strict_types=1);

use Ordenalo\Models\Categoria;

/**
 * 
 * Límite mensual de gasto por categoría, en USD (texto con punto decimal).
 * Las categorías que no aparezcan aquí no tienen límite.
 */
return [
    Categoria::Alimentacion->value => '250.00',
    Categoria::Transporte->value => '80.00',
    Categoria::Vivienda->value => '400.00',
    Categoria::Servicios->value => '120.00',
    Categoria::Salud->value => '100.00',
    Categoria::Educacion->value => '150.00',
    Categoria::Ocio->value => '100.00',
    Categoria::Otros->value => '50.00',
];