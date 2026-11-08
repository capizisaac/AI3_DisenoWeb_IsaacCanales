<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

use InvalidArgumentException;
use Ordenalo\Models\Dinero;

final class ReglaMonto implements ReglaValidacion
{
    public function campo(): string
    {
        return 'monto';
    }

    public function validar(array $datos): ?string
    {
        $valor = $datos['monto'] ?? null;

        if (!is_string($valor) || trim($valor) === '') {
            return 'Ingrese un monto.';
        }

        try {
            $dinero = Dinero::desdeTexto($valor);
        } catch (InvalidArgumentException) {
            return 'Monto inválido: use punto decimal y hasta 2 decimales (ej. 125.50).';
        }

        if ($dinero->centavos() <= 0) {
            return 'El monto debe ser mayor que cero.';
        }

        return null;
    }
}