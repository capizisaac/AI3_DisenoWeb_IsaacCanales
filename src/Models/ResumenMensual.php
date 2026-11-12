<?php

declare(strict_types=1);

namespace Ordenalo\Models;

use DateTimeImmutable;

final readonly class ResumenMensual
{
    /** @param list<EstadoCategoria> $estados */
    public function __construct(
        public DateTimeImmutable $mes,
        public Dinero $ingresos,
        public Dinero $gastos,
        public Dinero $saldo,
        public array $estados,
    ) {
    }
}
