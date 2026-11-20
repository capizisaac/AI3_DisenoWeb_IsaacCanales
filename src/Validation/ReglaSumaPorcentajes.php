<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

use Ordenalo\Models\Categoria;
use Ordenalo\Models\Tipo;

final class ReglaSumaPorcentajes implements ReglaValidacion
{
    /** @param array<string, int> $sugeridos Porcentaje sugerido por valor de categoría. */
    public function __construct(private array $sugeridos)
    {
    }

    public function campo(): string
    {
        return 'porcentajes';
    }

    public function validar(array $datos): ?string
    {
        $suma = 0;

        foreach (Categoria::permitidasPara(Tipo::Gasto) as $categoria) {
            $clave = $categoria->value;

            if (($datos['sin_limite_' . $clave] ?? '') === '1') {
                continue;
            }

            $valor = $datos['pct_' . $clave] ?? '';
            $texto = is_string($valor) ? trim($valor) : '';

            if ($texto === '') {
                $suma += $this->sugeridos[$clave] ?? 0;
                continue;
            }

            if (!preg_match('/^\d{1,3}$/D', $texto)) {
                return null; // el error de ese campo ya lo informa ReglaPorcentajeCategoria
            }

            $suma += (int) $texto;
        }

        if ($suma > 100) {
            return sprintf('Los porcentajes suman %d %%; no pueden superar el 100 %%.', $suma);
        }

        return null;
    }
}