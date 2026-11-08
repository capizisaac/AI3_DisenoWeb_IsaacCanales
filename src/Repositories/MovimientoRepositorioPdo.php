<?php

declare(strict_types=1);

namespace Ordenalo\Repositories;

use DateTimeImmutable;
use Ordenalo\Models\Categoria;
use Ordenalo\Models\Dinero;
use Ordenalo\Models\Movimiento;
use Ordenalo\Models\Tipo;
use PDO;

final class MovimientoRepositorioPdo implements MovimientoRepositorio
{
    public function __construct(private PDO $pdo)
    {
    }

    public function guardar(Movimiento $movimiento): int
    {
        $sql = 'INSERT INTO movimientos
                    (tipo, categoria, monto, descripcion, fecha_movimiento)
                VALUES
                    (:tipo, :categoria, :monto, :descripcion, :fecha_movimiento)';

        $sentencia = $this->pdo->prepare($sql);
        $sentencia->execute([
            ':tipo'             => $movimiento->tipo->value,
            ':categoria'        => $movimiento->categoria->value,
            ':monto'            => $movimiento->monto->aTextoDecimal(),
            ':descripcion'      => $movimiento->descripcion,
            ':fecha_movimiento' => $movimiento->fechaMovimiento->format('Y-m-d'),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function listarTodos(): array
    {
        $sentencia = $this->pdo->query(
            'SELECT id, tipo, categoria, monto, descripcion,
                    fecha_movimiento, fecha_creacion
             FROM movimientos
             ORDER BY fecha_movimiento DESC, id DESC'
        );

        return array_map($this->hidratar(...), $sentencia->fetchAll());
    }

    /** @param array<string, mixed> $fila */
    private function hidratar(array $fila): Movimiento
    {
        return new Movimiento(
            tipo: Tipo::from($fila['tipo']),
            categoria: Categoria::from($fila['categoria']),
            monto: Dinero::desdeTexto($fila['monto']),
            descripcion: $fila['descripcion'],
            fechaMovimiento: new DateTimeImmutable($fila['fecha_movimiento']),
            id: (int) $fila['id'],
            fechaCreacion: new DateTimeImmutable($fila['fecha_creacion']),
        );
    }
}