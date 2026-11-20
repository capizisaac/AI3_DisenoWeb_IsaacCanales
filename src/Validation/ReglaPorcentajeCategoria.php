<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

use Ordenalo\Models\Categoria;

final class ReglaPorcentajeCategoria implements ReglaValidacion
{
    public function __construct(private Categoria $categoria)
    {
    }

    public function campo(): string
    {
        return 'pct_' . $this->categoria->value;
    }

    public function validar(array $datos): ?string
    {
        if (($datos['sin_limite_' . $this->categoria->value] ?? '') === '1') {
            return null;
        }

        $valor = $datos[$this->campo()] ?? '';

        if (!is_string($valor)) {
            return 'Porcentaje inválido.';
        }

        $texto = trim($valor);

        if ($texto === '') {
            return null;
        }

        if (!preg_match('/^\d{1,3}$/D', $texto) || (int) $texto < 1 || (int) $texto > 100) {
            return 'Ingresa un entero entre 1 y 100, o déjalo vacío para usar el sugerido.';
        }

        return null;
    }
}