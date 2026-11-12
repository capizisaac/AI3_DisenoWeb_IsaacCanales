<?php

declare(strict_types=1);

namespace Ordenalo\Models;

enum NivelPresupuesto: string
{
    case Normal = 'normal';
    case Cerca = 'cerca';
    case Excedido = 'excedido';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Normal => 'Dentro del límite',
            self::Cerca => 'Cerca del límite',
            self::Excedido => 'Límite excedido',
        };
    }
}