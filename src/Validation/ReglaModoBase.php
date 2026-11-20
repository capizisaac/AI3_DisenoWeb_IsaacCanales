<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

use Ordenalo\Models\ModoBase;

final class ReglaModoBase implements ReglaValidacion
{
    public function campo(): string
    {
        return 'modo_base';
    }

    public function validar(array $datos): ?string
    {
        $valor = $datos['modo_base'] ?? null;

        if (!is_string($valor) || ModoBase::tryFrom($valor) === null) {
            return 'Elige cómo se calcula tu ingreso mensual de referencia.';
        }

        return null;
    }
}