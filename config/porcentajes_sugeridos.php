<?php

declare(strict_types=1);

use Ordenalo\Models\Categoria;

/**
 * Porcentaje preestablecido del ingreso mensual base para cada categoría de gasto.
 * Se aplica cuando el usuario no ha definido su propio límite.
 * El 20 % restante queda implícito como ahorro (regla 50/30/20 adaptada).
 * Punto de partida orientativo; no es asesoría financiera.
 */
return [
    Categoria::Alimentacion->value => 14,
    Categoria::Transporte->value => 8,
    Categoria::Vivienda->value => 25,
    Categoria::Servicios->value => 6,
    Categoria::Salud->value => 6,
    Categoria::Educacion->value => 6,
    Categoria::Ocio->value => 10,
    Categoria::Otros->value => 5,
];