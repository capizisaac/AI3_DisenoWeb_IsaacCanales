<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

use Ordenalo\Models\Tipo;

final class ReglaTipo implements ReglaValidacion
{
    public function campo(): string
    {
        return 'tipo';
    }

    public function validar(array $datos): ?string
    {
        $valor = $datos['tipo'] ?? null;

        if (!is_string($valor) || Tipo::tryFrom($valor) === null) {
            return 'Seleccione un tipo válido (ingreso o gasto).';
        }

        return null;
    }
}