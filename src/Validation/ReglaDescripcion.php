<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

final class ReglaDescripcion implements ReglaValidacion
{
    private const MINIMO = 3;
    private const MAXIMO = 255;

    public function campo(): string
    {
        return 'descripcion';
    }

    public function validar(array $datos): ?string
    {
        $valor = $datos['descripcion'] ?? null;

        if (!is_string($valor)) {
            return 'Ingrese una descripción.';
        }

        $longitud = mb_strlen(trim($valor));

        if ($longitud < self::MINIMO) {
            return sprintf('La descripción debe tener al menos %d caracteres.', self::MINIMO);
        }

        if ($longitud > self::MAXIMO) {
            return sprintf('La descripción no puede superar los %d caracteres.', self::MAXIMO);
        }

        return null;
    }
}