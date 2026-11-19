<?php

declare(strict_types=1);

namespace Ordenalo\Models;

enum ModoBase: string
{
    case Manual = 'manual';
    case Promedio = 'promedio';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Manual => 'Monto fijo mensual',
            self::Promedio => 'Promedio de mis ingresos registrados',
        };
    }
}