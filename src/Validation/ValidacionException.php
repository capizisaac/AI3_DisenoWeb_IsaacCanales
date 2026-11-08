<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

use RuntimeException;

final class ValidacionException extends RuntimeException
{
    /** @param array<string, string> $errores Mensajes por campo. */
    public function __construct(private array $errores)
    {
        parent::__construct('Los datos del movimiento no son válidos.');
    }

    /** @return array<string, string> */
    public function errores(): array
    {
        return $this->errores;
    }
}