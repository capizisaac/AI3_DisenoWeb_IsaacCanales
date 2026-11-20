<?php

declare(strict_types=1);

namespace Ordenalo\Presupuesto;

use Ordenalo\Models\Dinero;
use Ordenalo\Models\ModoBase;

final readonly class ConfiguracionPresupuesto
{
    /** @param list<LimiteConfigurado> $limites */
    public function __construct(
        public ModoBase $modo,
        public ?Dinero $ingresoManual,
        public ?Dinero $base,
        public array $limites,
    ) {
    }
}