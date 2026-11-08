<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

use Ordenalo\Models\Categoria;
use Ordenalo\Models\Tipo;

final class ReglaCategoria implements ReglaValidacion
{
    public function campo(): string
    {
        return 'categoria';
    }

    public function validar(array $datos): ?string
    {
        $valorCategoria = $datos['categoria'] ?? null;
        $categoria = is_string($valorCategoria) ? Categoria::tryFrom($valorCategoria) : null;

        if ($categoria === null) {
            return 'Seleccione una categoría válida.';
        }

        $valorTipo = $datos['tipo'] ?? null;
        $tipo = is_string($valorTipo) ? Tipo::tryFrom($valorTipo) : null;

        if ($tipo === null) {
            return null; // el tipo inválido ya lo informa ReglaTipo
        }

        if (!in_array($categoria, Categoria::permitidasPara($tipo), true)) {
            return sprintf(
                'La categoría "%s" no corresponde a un %s.',
                $categoria->etiqueta(),
                $tipo->value
            );
        }

        return null;
    }
}