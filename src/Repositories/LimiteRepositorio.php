<?php

declare(strict_types=1);

namespace Ordenalo\Repositories;

interface LimiteRepositorio
{
    /**
     * Límites que el usuario ha definido. Clave: valor de la categoría.
     * Valor: porcentaje (1 a 100) o null si eligió "Sin límite".
     * Las categorías sin fila no aparecen: usan el porcentaje sugerido.
     *
     * @return array<string, int|null>
     */
    public function personalizados(): array;

    /**
     * Reemplaza todos los límites personalizados de forma atómica.
     *
     * @param array<string, int|null> $personalizados
     */
    public function reemplazar(array $personalizados): void;
}