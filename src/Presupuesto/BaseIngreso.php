<?php

declare(strict_types=1);

namespace Ordenalo\Presupuesto;

use DateTimeImmutable;
use Ordenalo\Models\AjustesPresupuesto;
use Ordenalo\Models\Dinero;

interface BaseIngreso
{
    /**
     * Ingreso mensual de referencia sobre el que se calculan los límites,
     * o null si todavía no se puede determinar.
     */
    public function calcular(AjustesPresupuesto $ajustes, DateTimeImmutable $referencia): ?Dinero;
}