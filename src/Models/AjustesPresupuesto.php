<?php

declare(strict_types=1);

namespace Ordenalo\Models;

final readonly class AjustesPresupuesto
{
    public function __construct(
        public ModoBase $modo,
        public ?Dinero $ingresoManual,
    ) {
    }
}