<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

use InvalidArgumentException;
use Ordenalo\Models\Dinero;
use Ordenalo\Models\ModoBase;

final class ReglaIngresoManual implements ReglaValidacion
{
    public function campo(): string
    {
        return 'ingreso_manual';
    }

    public function validar(array $datos): ?string
    {
        $valorModo = $datos['modo_base'] ?? null;
        $modo = is_string($valorModo) ? ModoBase::tryFrom($valorModo) : null;
        $valor = $datos['ingreso_manual'] ?? '';

        if (!is_string($valor)) {
            return 'Ingreso mensual inválido.';
        }

        $texto = trim($valor);

        if ($texto === '') {
            return $modo === ModoBase::Manual ? 'Ingresa tu ingreso mensual de referencia (neto).' : null;
        }

        try {
            $dinero = Dinero::desdeTexto($texto);
        } catch (InvalidArgumentException) {
            return 'Monto inválido: use punto decimal y hasta 2 decimales (ej. 1200.00).';
        }

        if ($dinero->centavos() <= 0) {
            return 'El ingreso mensual debe ser mayor que cero.';
        }

        return null;
    }
}