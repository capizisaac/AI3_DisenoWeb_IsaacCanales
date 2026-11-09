<?php

declare(strict_types=1);

namespace Ordenalo\Views;

final class Html
{
    public static function e(mixed $valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

