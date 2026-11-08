<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

final class ReglaFecha implements ReglaValidacion
{
    private const MINIMA = '2000-01-01';
    private const MAXIMA = '2100-12-31';

    public function campo(): string
    {
        return 'fecha_movimiento';
    }

    public function validar(array $datos): ?string
    {
        $valor = $datos['fecha_movimiento'] ?? null;

        if (!is_string($valor) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $valor, $partes)) {
            return 'Ingrese una fecha válida con formato AAAA-MM-DD.';
        }

        if (!checkdate((int) $partes[2], (int) $partes[3], (int) $partes[1])) {
            return 'La fecha no existe en el calendario.';
        }

        if ($valor < self::MINIMA || $valor > self::MAXIMA) {
            return sprintf('La fecha debe estar entre %s y %s.', self::MINIMA, self::MAXIMA);
        }

        return null;
    }
}