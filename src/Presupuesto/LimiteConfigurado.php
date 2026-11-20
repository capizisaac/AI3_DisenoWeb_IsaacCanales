<?php

declare(strict_types=1);

namespace Ordenalo\Presupuesto;

use Ordenalo\Models\Categoria;
use Ordenalo\Models\Dinero;

final readonly class LimiteConfigurado
{
    public function __construct(
        public Categoria $categoria,
        public ?int $personalizado,
        public bool $sinLimite,
        public int $sugerido,
        public ?int $efectivo,
        public ?Dinero $limite,
    ) {
    }
}