<?php

declare(strict_types=1);

namespace Ordenalo\Repositories;

use DateTimeImmutable;
use Ordenalo\Models\Movimiento;

interface MovimientoRepositorio
{
    /** Guarda el movimiento y devuelve el id generado. */
    public function guardar(Movimiento $movimiento): int;

    /** @return list<Movimiento> */
    public function listarTodos(): array;

    /**
     * Movimientos cuya fecha cae entre ambas fechas, inclusive.
     *
     * @return list<Movimiento>
     */
    public function listarPorPeriodo(DateTimeImmutable $desde, DateTimeImmutable $hasta): array;
}