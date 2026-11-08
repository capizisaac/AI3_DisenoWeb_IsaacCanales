<?php

declare(strict_types=1);

namespace Ordenalo\Database;

use PDO;

final class ConexionFactory
{
    public function __construct(
        private string $host,
        private string $nombreBd,
        private string $usuario,
        private string $clave,
        private string $charset = 'utf8mb4'
    ) {
    }

    public function crear(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $this->host,
            $this->nombreBd,
            $this->charset
        );

        return new PDO($dsn, $this->usuario, $this->clave, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
}