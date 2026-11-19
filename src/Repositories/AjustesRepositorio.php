<?php

declare(strict_types=1);

namespace Ordenalo\Repositories;

use Ordenalo\Models\AjustesPresupuesto;

interface AjustesRepositorio
{
    public function obtener(): AjustesPresupuesto;

    public function guardar(AjustesPresupuesto $ajustes): void;
}