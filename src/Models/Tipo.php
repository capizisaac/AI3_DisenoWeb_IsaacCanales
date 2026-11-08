<?php

declare(strict_types=1);

namespace Ordenalo\Models;

enum Tipo: string
{
    case Ingreso = 'ingreso';
    case Gasto = 'gasto';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Ingreso => 'Ingreso',
            self::Gasto => 'Gasto',
        };
    }
}