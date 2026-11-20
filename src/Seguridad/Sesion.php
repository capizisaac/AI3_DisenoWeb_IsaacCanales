<?php

declare(strict_types=1);

namespace Ordenalo\Seguridad;

final class Sesion
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        session_name('ORDENALO_SESION');
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'cookie_secure' => $https,
            'use_strict_mode' => true,
            'use_only_cookies' => true,
        ]);
    }
}