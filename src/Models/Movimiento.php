<?php

declare(strict_types=1);

namespace Ordenalo\Models;

use DateTimeImmutable;

final readonly class Movimiento
{
    public function __construct(
        public Tipo $tipo,
        public Categoria $categoria,
        public Dinero $monto,
        public string $descripcion,
        public DateTimeImmutable $fechaMovimiento,
        public ?int $id = null,
        public ?DateTimeImmutable $fechaCreacion = null,
    ) {
    }
}