<?php

declare(strict_types=1);

namespace Ordenalo\Repositories;

use Ordenalo\Models\Categoria;
use PDO;
use Throwable;

final class LimiteRepositorioPdo implements LimiteRepositorio
{
    public function __construct(private PDO $pdo)
    {
    }

    public function personalizados(): array
    {
        $filas = $this->pdo->query('SELECT categoria, porcentaje FROM limites')->fetchAll();
        $limites = [];

        foreach ($filas as $fila) {
            $limites[$fila['categoria']] = $fila['porcentaje'] === null ? null : (int) $fila['porcentaje'];
        }

        return $limites;
    }

    public function reemplazar(array $personalizados): void
    {
        $this->pdo->beginTransaction();

        try {
            $this->pdo->exec('DELETE FROM limites');

            $insertar = $this->pdo->prepare(
                'INSERT INTO limites (categoria, porcentaje) VALUES (:categoria, :porcentaje)'
            );

            foreach ($personalizados as $categoria => $porcentaje) {
                $insertar->bindValue(':categoria', Categoria::from((string) $categoria)->value);
                $insertar->bindValue(
                    ':porcentaje',
                    $porcentaje,
                    $porcentaje === null ? PDO::PARAM_NULL : PDO::PARAM_INT
                );
                $insertar->execute();
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}