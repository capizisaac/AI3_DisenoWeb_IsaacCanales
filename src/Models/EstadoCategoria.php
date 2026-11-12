<?php

declare(strict_types=1);

namespace Ordenalo\Models;

final readonly class EstadoCategoria
{
    public function __construct(
        public Categoria $categoria,
        public Dinero $gastado,
        public Dinero $limite,
        public int $porcentaje,
        public NivelPresupuesto $nivel,
    ) {
    }
}
