<?php

declare(strict_types=1);

namespace Ordenalo\Models;

use InvalidArgumentException;

final readonly class Dinero
{
    private function __construct(
        private int $centavos,
        private string $moneda,
    ) {
        if (!preg_match('/^[A-Z]{3}$/D', $moneda)) {
            throw new InvalidArgumentException('Código de moneda inválido.');
        }
    }

    public static function desdeCentavos(int $centavos, string $moneda = 'USD'): self
    {
        return new self($centavos, strtoupper($moneda));
    }

    public static function desdeTexto(string $texto, string $moneda = 'USD'): self
    {
        $texto = trim($texto);

        if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $texto)) {
            throw new InvalidArgumentException(
                'Monto inválido: use un número positivo con hasta 2 decimales.'
            );
        }

        [$enteros, $decimales] = array_pad(explode('.', $texto, 2), 2, '0');
        $centavos = (int) $enteros * 100 + (int) str_pad($decimales, 2, '0');

        return new self($centavos, strtoupper($moneda));
    }

    public function centavos(): int
    {
        return $this->centavos;
    }

    public function moneda(): string
    {
        return $this->moneda;
    }

    public function aTextoDecimal(): string
    {
    $absoluto = abs($this->centavos);

    return sprintf(
        '%s%d.%02d',
        $this->centavos < 0 ? '-' : '',
        intdiv($absoluto, 100),
        $absoluto % 100
    );
    }

        public static function cero(string $moneda = 'USD'): self
    {
        return new self(0, strtoupper($moneda));
    }

    public function sumar(self $otro): self
    {
        $this->exigirMismaMoneda($otro);

        return new self($this->centavos + $otro->centavos, $this->moneda);
    }

    public function restar(self $otro): self
    {
        $this->exigirMismaMoneda($otro);

        return new self($this->centavos - $otro->centavos, $this->moneda);
    }

    public function esMayorQue(self $otro): bool
    {
        $this->exigirMismaMoneda($otro);

        return $this->centavos > $otro->centavos;
    }

    /** Porcentaje entero (redondeado hacia abajo) que este monto representa del límite. */
    public function porcentajeDe(self $limite): int
    {
        $this->exigirMismaMoneda($limite);

        if ($limite->centavos <= 0) {
            throw new InvalidArgumentException('El límite debe ser mayor que cero.');
        }

        return intdiv($this->centavos * 100, $limite->centavos);
    }

    private function exigirMismaMoneda(self $otro): void
    {
        if ($this->moneda !== $otro->moneda) {
            throw new InvalidArgumentException('No se pueden operar montos de monedas distintas.');
        }
    }
    
    public function formatear(): string
    {
        $signo = $this->centavos < 0 ? '-' : '';
        $absoluto = abs($this->centavos);
        $simbolo = match ($this->moneda) {
            'USD' => '$',
            'EUR' => '€',
            default => $this->moneda . ' ',
        };

        return $signo . $simbolo
            . number_format(intdiv($absoluto, 100), 0, '.', ',')
            . '.' . sprintf('%02d', $absoluto % 100);
    }
}