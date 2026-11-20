<?php

declare(strict_types=1);

namespace Ordenalo\Presupuesto;

use DateTimeImmutable;
use Ordenalo\Models\AjustesPresupuesto;
use Ordenalo\Models\Dinero;

final class BaseManual implements BaseIngreso
{
    public function calcular(AjustesPresupuesto $ajustes, DateTimeImmutable $referencia): ?Dinero
    {
        return $ajustes->ingresoManual;
    }
}