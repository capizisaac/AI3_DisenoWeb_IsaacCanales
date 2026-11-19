<?php

declare(strict_types=1);

namespace Ordenalo\Repositories;

use Ordenalo\Models\AjustesPresupuesto;
use Ordenalo\Models\Dinero;
use Ordenalo\Models\ModoBase;
use PDO;

final class AjustesRepositorioPdo implements AjustesRepositorio
{
    public function __construct(private PDO $pdo)
    {
    }

    public function obtener(): AjustesPresupuesto
    {
        $fila = $this->pdo
            ->query('SELECT modo_base, ingreso_manual FROM ajustes_presupuesto WHERE id = 1')
            ->fetch();

        if ($fila === false) {
            return new AjustesPresupuesto(ModoBase::Manual, null);
        }

        return new AjustesPresupuesto(
            ModoBase::from($fila['modo_base']),
            $fila['ingreso_manual'] === null ? null : Dinero::desdeTexto($fila['ingreso_manual']),
        );
    }

    public function guardar(AjustesPresupuesto $ajustes): void
    {
        $sentencia = $this->pdo->prepare(
            'UPDATE ajustes_presupuesto SET modo_base = :modo, ingreso_manual = :ingreso WHERE id = 1'
        );

        $ingreso = $ajustes->ingresoManual?->aTextoDecimal();

        $sentencia->bindValue(':modo', $ajustes->modo->value);
        $sentencia->bindValue(':ingreso', $ingreso, $ingreso === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $sentencia->execute();
    }
}