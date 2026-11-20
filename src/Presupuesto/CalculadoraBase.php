<?php

declare(strict_types=1);

namespace Ordenalo\Presupuesto;

use DateTimeImmutable;
use Ordenalo\Models\AjustesPresupuesto;
use Ordenalo\Models\Dinero;

final class CalculadoraBase
{
    /** @param array<string, BaseIngreso> $estrategias Clave: valor de ModoBase. */
    public function __construct(private array $estrategias)
    {
    }

    public function calcular(AjustesPresupuesto $ajustes, DateTimeImmutable $referencia): ?Dinero
    {
        $estrategia = $this->estrategias[$ajustes->modo->value] ?? null;

        return $estrategia?->calcular($ajustes, $referencia);
    }
}