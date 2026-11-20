<?php

declare(strict_types=1);

namespace Ordenalo\Seguridad;

final class TokenCsrf
{
    private const CLAVE = '_csrf';

    public function token(): string
    {
        $actual = $_SESSION[self::CLAVE] ?? null;

        if (!is_string($actual) || $actual === '') {
            $actual = bin2hex(random_bytes(32));
            $_SESSION[self::CLAVE] = $actual;
        }

        return $actual;
    }

    public function validar(mixed $recibido): bool
    {
        $esperado = $_SESSION[self::CLAVE] ?? null;

        return is_string($esperado)
            && $esperado !== ''
            && is_string($recibido)
            && hash_equals($esperado, $recibido);
    }
}